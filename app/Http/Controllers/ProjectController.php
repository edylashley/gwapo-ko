<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function archivePage()
    {
        $archivedProjects = Project::whereNotNull('archived_at')->get();
        $totalArchivedProjects = $archivedProjects->count();
        $totalArchivedBudget = $archivedProjects->sum('budget');
        $totalArchivedSpent = $archivedProjects->sum(function($project) {
            return $project->totalSpentWithDetailedEngineering();
        });
        return view('projects.archive', [
            'archivedProjects' => $archivedProjects,
            'totalArchivedProjects' => $totalArchivedProjects,
            'totalArchivedBudget' => $totalArchivedBudget,
            'totalArchivedSpent' => $totalArchivedSpent
        ]);
    }
    public function index()
{
    // Only get non-archived, non-deleted projects
    $projects = Project::whereNull('archived_at')
        ->whereNull('deleted_at')
        ->get();

    $totalBudget = $projects->sum('budget');
    $activeProjects = $projects->count(); // All non-archived, non-deleted
    $avgBudget = $projects->count() > 0 ? $projects->avg('budget') : 0;

    return view('projects.index', [
        'projects' => $projects,
        'totalBudget' => $totalBudget,
        'activeProjects' => $activeProjects,
        'avgBudget' => $avgBudget
    ]);
}

    public function apiIndex()
    {
        // First, auto-archive projects that reached 100% budget
        $this->autoArchiveCompletedProjects();

        // Return only non-archived projects for API
        $projects = Project::whereNull('archived_at')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        Log::info('Create project request received', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'name' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'fpp_code' => 'nullable|string|max:255',
            'project_engineer_id' => 'nullable|exists:engineers,id',
        ]);

        $data = $request->only(['name', 'budget', 'fpp_code', 'project_engineer_id']);

        // Set assigned_at timestamp if an engineer is assigned
        if (!empty($data['project_engineer_id'])) {
            $data['assigned_at'] = now();
        }

        $project = Project::create($data);

        Log::info('Project created successfully', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'user_id' => Auth::id()
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project
            ], 201);
        }

        // Return redirect for regular form submissions
        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function update(Request $request, Project $project)
    {
        Log::info('Update project request received', [
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'name' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'fpp_code' => 'nullable|string|max:255',
            'project_engineer_id' => 'nullable|exists:engineers,id',
        ]);

        $data = $request->only(['name', 'budget', 'fpp_code', 'project_engineer_id']);

        // Check if engineer assignment changed
        $oldEngineerId = $project->project_engineer_id;
        $newEngineerId = $data['project_engineer_id'] ?? null;

        // Set assigned_at timestamp if engineer assignment changed
        if ($oldEngineerId != $newEngineerId) {
            if (!empty($newEngineerId)) {
                // Engineer assigned or changed
                $data['assigned_at'] = now();
            } else {
                // Engineer unassigned
                $data['assigned_at'] = null;
            }
        }

        $project->update($data);

        Log::info('Project updated successfully', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'user_id' => Auth::id(),
            'changes' => $data
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => $project->fresh()
            ]);
        }

        // Return redirect for regular form submissions
        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        Log::info('Delete project request received', [
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'project_name' => $project->name
        ]);

        $projectName = $project->name;
        $projectId = $project->id;
        $project->delete();

        Log::info('Project deleted successfully', [
            'project_id' => $projectId,
            'project_name' => $projectName,
            'user_id' => Auth::id()
        ]);

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$projectName}' deleted successfully",
                'project_id' => $projectId,
                'project_name' => $projectName
            ]);
        }

        // Return redirect for regular form submissions
        return redirect()->route('projects.index')->with('success', "Project '{$projectName}' deleted successfully");
    }

    public function recentlyDeleted()
    {
        // Get projects deleted within the last 30 days
        $deletedProjects = Project::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(30))
            ->with('expenses')
            ->orderBy('deleted_at', 'desc')
            ->get();

        // Calculate statistics
        $totalDeletedBudget = $deletedProjects->sum('budget');
        $daysUntilCleanup = 30; // Days until auto-cleanup

        return view('projects.recently-deleted', [
            'deletedProjects' => $deletedProjects,
            'totalDeletedBudget' => $totalDeletedBudget,
            'daysUntilCleanup' => $daysUntilCleanup
        ]);
    }

    public function restore($id)
    {
        try {
            // Log the restore attempt
            Log::info("Attempting to restore project with ID: {$id}");

            // Find the specific trashed project by ID
            $project = Project::onlyTrashed()->findOrFail($id);
            $projectName = $project->name;

            Log::info("Found project to restore: {$projectName} (ID: {$id})");

            // Count expenses that will be restored with this project
            $expensesCount = $project->expenses()
                ->onlyTrashed()
                ->whereBetween('deleted_at', [
                    $project->deleted_at->copy()->subMinute(),
                    $project->deleted_at->copy()->addMinute()
                ])
                ->count();

            Log::info("Will restore {$expensesCount} expenses with project {$projectName}");

            // Store deletion time before restoration
            $deletedAt = $project->deleted_at;

            // Restore the project directly using database update
            $restored = DB::table('projects')
                ->where('id', $id)
                ->update(['deleted_at' => null]);

            Log::info("Project restoration result: " . ($restored ? 'success' : 'failed'));

            // Restore related expenses that were deleted at the same time
            if ($restored && $deletedAt) {
                $expensesRestored = DB::table('expenses')
                    ->where('project_id', $id)
                    ->whereNotNull('deleted_at')
                    ->whereBetween('deleted_at', [
                        $deletedAt->copy()->subMinute(),
                        $deletedAt->copy()->addMinute()
                    ])
                    ->update(['deleted_at' => null]);

                Log::info("Restored {$expensesRestored} expenses for project {$projectName}");
            }

            $message = "Project '{$projectName}' restored successfully";
            if ($expensesCount > 0) {
                $message .= " along with {$expensesCount} associated expense(s)";
            }

            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            // Return redirect for regular form submissions
            return redirect()->route('projects.recently-deleted')->with('success', $message);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error restoring project: ' . $e->getMessage());

            // Handle any errors during restoration
            $errorMessage = "Error restoring project. Please try again.";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->route('projects.recently-deleted')->with('error', $errorMessage);
        }
    }

    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $projectName = $project->name;

        // Force delete all associated expenses first
        $project->expenses()->onlyTrashed()->forceDelete();

        // Then force delete the project
        $project->forceDelete();

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$projectName}' permanently deleted"
            ]);
        }

        // Return redirect for regular form submissions
        return redirect()->route('projects.recently-deleted')->with('success', "Project '{$projectName}' permanently deleted");
    }

    public function trackRecord(Project $project)
    {
        $expenses = $project->expenses()->orderBy('date', 'desc')->get();

        // Get detailed engineering cost
        $detailedEngineeringCost = $project->getDetailedEngineeringCost();

        // Create a virtual "Detailed Engineering" expense if there are engineer salaries
        $allExpenses = $expenses->toArray();
        
        // Check if there's already a "Detailed Engineering" expense in the database
        $hasDetailedEngineeringExpense = $expenses->where('description', 'Detailed Engineering')->count() > 0;
        
        if ($detailedEngineeringCost > 0 && !$hasDetailedEngineeringExpense) {
            // Add virtual detailed engineering expense only if it doesn't exist
            $detailedEngineeringExpense = [
                'id' => 'detailed_engineering',
                'project_id' => $project->id,
                'description' => 'Detailed Engineering',
                'amount' => $detailedEngineeringCost,
                'date' => now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
                'is_virtual' => true // Flag to identify this as a virtual expense
            ];

            // Add to the beginning of expenses array
            array_unshift($allExpenses, $detailedEngineeringExpense);
        }

        return response()->json([
            'project' => $project,
            'expenses' => $allExpenses,
            'summary' => [
                'total_budget' => $project->budget,
                'total_spent' => $project->totalSpentWithDetailedEngineering(),
                'remaining_budget' => $project->remainingBudgetWithDetailedEngineering(),
                'expense_count' => count($allExpenses),
                'percent_used' => $project->budget > 0 ? ($project->totalSpentWithDetailedEngineering() / $project->budget) * 100 : 0
            ]
        ]);
    }

    public function getMonthlyAssignments(Project $project)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $monthlyAssignments = $project->monthlyAssignments()
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->with('engineer')
            ->get()
            ->map(function($assignment) {
                return [
                    'engineer_id' => $assignment->engineer_id,
                    'engineer_name' => $assignment->engineer->name,
                    'is_team_head' => $assignment->is_team_head,
                    'salary' => $assignment->salary,
                    'assigned_at' => $assignment->assigned_at
                ];
            });

        return response()->json([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'project_engineer_id' => $project->project_engineer_id,
            'monthly_assignments' => $monthlyAssignments
        ]);
    }

    public function receipt(Project $project)
    {
        $expenses = $project->expenses()->orderBy('date', 'asc')->get();

        // Get monthly assignments grouped by month for the receipt
        $monthlyAssignments = $project->getMonthlyAssignmentsGrouped();

        return view('projects.receipt', [
            'project' => $project,
            'expenses' => $expenses,
            'monthlyAssignments' => $monthlyAssignments,
            'summary' => [
                'total_budget' => $project->budget,
                'total_spent' => $project->totalSpent(),
                'detailed_engineering_cost' => $project->getDetailedEngineeringCost(),
                'total_spent_with_engineering' => $project->totalSpentWithDetailedEngineering(),
                'remaining_budget' => $project->remainingBudgetWithDetailedEngineering(),
                'percent_used' => $project->budget > 0 ? ($project->totalSpentWithDetailedEngineering() / $project->budget) * 100 : 0,
            ]
        ]);
    }

    public function multipleReceipts(Request $request)
    {
        $projectIds = $request->input('project_ids', []);

        if (empty($projectIds)) {
            return response()->json(['error' => 'No projects selected'], 400);
        }

        $projects = Project::whereIn('id', $projectIds)->with('expenses')->get();

        $projectsData = [];
        foreach ($projects as $project) {
            $expenses = $project->expenses()->orderBy('date', 'asc')->get();
            $monthlyAssignments = $project->getMonthlyAssignmentsGrouped();

            $projectsData[] = [
                'project' => $project,
                'expenses' => $expenses,
                'monthlyAssignments' => $monthlyAssignments,
                'summary' => [
                    'total_budget' => $project->budget,
                    'total_spent' => $project->totalSpentWithDetailedEngineering(),
                    'remaining_budget' => $project->remainingBudgetWithDetailedEngineering(),
                    'percent_used' => $project->budget > 0 ? ($project->totalSpentWithDetailedEngineering() / $project->budget) * 100 : 0,
                ]
            ];
        }

        return view('projects.multiple-receipts', [
            'projectsData' => $projectsData,
            'isPrint' => $request->has('print')
        ]);
    }
    
    public function archiveProject(Project $project)
    {
        Log::info('Archive project request received', [
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'project_name' => $project->name
        ]);
    
        try {
            $project->archive();
    
            Log::info('Project archived successfully', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'user_id' => Auth::id()
            ]);
    
            // Check if the request expects JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Project '{$project->name}' has been archived successfully."
                ]);
            }
    
            // For regular form POST, redirect back with a flash message
            return redirect()->route('projects.index')->with('success', "Project '{$project->name}' has been archived successfully.");
    
        } catch (\Exception $e) {
            Log::error('Error archiving project', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
    
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error archiving project. Please try again.'
                ], 500);
            }
    
            return redirect()->route('projects.index')->with('error', 'Error archiving project. Please try again.');
        }
    }
    // Auto-archive projects that have reached 100% budget utilization
    private function autoArchiveCompletedProjects()
    {
        $projects = Project::whereNull('archived_at')
            ->where('force_unarchived', false)
            ->with('expenses')
            ->get();

        foreach ($projects as $project) {
            if ($project->isFullyUtilized()) {
                $project->archived_at = now();
                $project->save();
            }
        }
    }

    public function archiveData()
    {
        // API endpoint for archive data
        $archivedProjects = Project::with('expenses')
            ->get()
            ->filter(function ($project) {
                return $project->isArchiveEligible();
            })
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'fpp_code' => $project->fpp_code,
                    'budget' => $project->budget,
                    'total_spent' => $project->totalSpent(),
                    'remaining_budget' => $project->remainingBudget(),
                    'percent_used' => $project->getBudgetUtilizationPercentage(),
                    'completion_status' => $project->getCompletionStatus(),
                    'expense_count' => $project->expenses->count(),
                    'created_at' => $project->created_at,
                    'completed_at' => $project->expenses()->latest('date')->first()?->date
                ];
            })
            ->values();

        return response()->json([
            'projects' => $archivedProjects,
            'summary' => [
                'total_projects' => $archivedProjects->count(),
                'total_budget' => $archivedProjects->sum('budget'),
                'total_spent' => $archivedProjects->sum('total_spent'),
                'average_utilization' => $archivedProjects->avg('percent_used')
            ]
        ]);
    }

    public function unarchive(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        Log::info('Unarchive project request received', [
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'project_name' => $project->name,
            'archived_at' => $project->archived_at
        ]);
        if (!$project->archived_at) {
            Log::warning('Unarchive attempted on non-archived project', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'archived_at' => $project->archived_at,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => "Project '{$project->name}' is not archived."
            ], 400);
        }
        try {
            $project->unarchive();
            Log::info('Project unarchived successfully', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'user_id' => Auth::id(),
                'force_unarchived' => $project->force_unarchived
            ]);
            return response()->json([
                'success' => true,
                'message' => "Project '{$project->name}' has been unarchived successfully.",
                'project' => $project->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Error unarchiving project', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error unarchiving project: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fetch team salaries for a project
public function getTeamSalaries(Project $project) {
    $team = $project->detailed_engineering_team; // use accessor/attribute
    return response()->json(['team' => $team]);
}

// Update salary for a team member
public function updateTeamSalary(Request $request, Project $project, $engineerId) {
    $year = now()->year;
    $month = now()->month;
    $assignment = $project->monthlyAssignments()
        ->where('engineer_id', $engineerId)
        ->where('year', $year)
        ->where('month', $month)
        ->firstOrFail();
    $assignment->salary = $request->input('salary');
    $assignment->save();
    return response()->json(['success' => true, 'salary' => $assignment->salary]);
}

// Delete salary for a team member
public function deleteTeamSalary(Project $project, $engineerId) {
    $year = now()->year;
    $month = now()->month;
    $assignment = $project->monthlyAssignments()
        ->where('engineer_id', $engineerId)
        ->where('year', $year)
        ->where('month', $month)
        ->first();
    if ($assignment) {
        $assignment->delete();
    }
    return response()->json(['success' => true]);
}
}