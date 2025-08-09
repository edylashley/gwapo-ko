# 🗑️ Recently Deleted System Documentation

## Overview
The Recently Deleted system provides a safety net for project deletions, allowing users to recover accidentally deleted projects within a 30-day window.

## Features

### ✅ **Soft Delete Implementation**
- Projects are not immediately deleted from the database
- Instead, they are marked with a `deleted_at` timestamp
- Associated expenses are also soft deleted automatically

### ✅ **Recently Deleted Page**
- Accessible via "Recently Deleted" button in the projects header
- Shows all projects deleted within the last 30 days
- Displays deletion date and expiration date for each project
- Color-coded interface (red theme) to indicate deleted status

### ✅ **Restore Functionality**
- One-click restore button for each deleted project
- Restores ONLY the specific project and its associated expenses that were deleted together
- Smart expense restoration: only restores expenses deleted within 1 minute of the project deletion
- Enhanced UI feedback with loading states and detailed success messages
- Prevents double-clicking with button state management

### ✅ **Permanent Deletion**
- Manual permanent deletion via "Delete Forever" button
- Automatic permanent deletion after 30 days
- Cannot be undone once permanently deleted

### ✅ **Automated Cleanup**
- Daily scheduled command to clean up old deleted projects
- Runs automatically via Laravel's task scheduler
- Can be run manually: `php artisan projects:cleanup-deleted`

## User Experience Flow

### 1. **Deleting a Project**
1. User clicks "Delete" button on a project
2. Confirmation dialog: "Are you sure you want to delete [name]? You can restore it from Recently Deleted if needed."
3. Project is soft deleted
4. Success message: "Project deleted successfully. You can restore it from Recently Deleted."

### 2. **Accessing Recently Deleted**
1. User clicks "🗑️ Recently Deleted" button in header
2. Navigates to dedicated Recently Deleted page
3. Shows all projects deleted within 30 days

### 3. **Restoring a Project**
1. User clicks "↩️ Restore" button
2. Confirmation dialog: "Are you sure you want to restore [name]?"
3. Project and expenses are restored
4. Success message and page refresh

### 4. **Permanent Deletion**
1. User clicks "🗑️ Delete Forever" button
2. Strong confirmation: "Are you sure you want to PERMANENTLY delete [name]? This action cannot be undone!"
3. Project is permanently removed from database
4. Cannot be recovered

## Technical Implementation

### **Database Changes**
- Added `deleted_at` column to `projects` table
- Added `deleted_at` column to `expenses` table
- Both tables support soft deletes

### **Model Updates**
- `Project` model uses `SoftDeletes` trait
- `Expense` model uses `SoftDeletes` trait
- Custom `restore()` method in Project model

### **Controller Methods**
- `recentlyDeleted()` - Shows recently deleted projects page
- `restore($id)` - Restores a soft deleted project
- `forceDelete($id)` - Permanently deletes a project

### **Routes Added**
- `GET /projects/recently-deleted` - Recently deleted page
- `POST /projects/{id}/restore` - Restore project
- `DELETE /projects/{id}/force-delete` - Permanent delete

### **Scheduled Command**
- `CleanupDeletedProjects` command
- Runs daily via Laravel scheduler
- Automatically removes projects older than 30 days

## Recent Improvements

### **🔧 Individual Restore Fix**
**Problem:** Previously, restoring a project would restore ALL trashed expenses for that project, not just the ones deleted with the project.

**Solution:**
- Modified `Project::restore()` method to use time-based filtering
- Only restores expenses deleted within 1 minute of the project deletion
- Added error handling and detailed logging
- Enhanced UI feedback with loading states and better messages

**Technical Details:**
- Uses `whereBetween()` to filter expenses by deletion timestamp
- Compares expense `deleted_at` with project `deleted_at` ± 1 minute
- Prevents restoration of unrelated deleted expenses
- Maintains data integrity and user expectations

## Benefits

### **Safety**
- Prevents accidental permanent data loss
- 30-day recovery window for mistakes
- Clear warnings for permanent actions
- Individual project restoration without affecting other deleted items

### **User-Friendly**
- Intuitive interface with clear messaging
- Visual indicators (red theme, icons)
- Countdown to permanent deletion

### **Automated Maintenance**
- No manual intervention required
- Automatic cleanup prevents database bloat
- Configurable retention period

## Configuration

### **Retention Period**
Currently set to 30 days. To change:
1. Update `CleanupDeletedProjects` command
2. Update `recentlyDeleted()` controller method
3. Update documentation

### **Scheduler Setup**
Ensure Laravel's task scheduler is running:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### **Manual Cleanup**
Run cleanup manually:
```bash
php artisan projects:cleanup-deleted
```

## Security Considerations

- Only authenticated users can access deleted projects
- Permanent deletion requires double confirmation
- Audit trail maintained through Laravel's timestamps
- No sensitive data exposed in deleted state

## Future Enhancements

- Email notifications before permanent deletion
- Bulk restore functionality
- Admin-only permanent delete permissions
- Configurable retention periods per user
- Export deleted project data before permanent deletion

---

*This system provides a robust safety net while maintaining clean database hygiene through automated cleanup.*
