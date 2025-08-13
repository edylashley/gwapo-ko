// Detailed Engineering Modal Management
class DetailedEngineeringModal {
    constructor() {
        this.modal = document.getElementById('editDetailedEngineeringModal');
        this.teamList = document.getElementById('engineeringTeamList');
        this.teamMemberTemplate = document.getElementById('teamMemberTemplate');
        this.currentProjectId = null;
        this.engineersData = [];
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Close modal buttons
        const closeButtons = [
            document.getElementById('closeEditDetailedEngineeringModal'),
            document.getElementById('cancelEditDetailedEngineeringBtn')
        ];
        
        closeButtons.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', () => this.close());
            }
        });

        // Save button
        const saveBtn = document.getElementById('saveDetailedEngineeringBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveChanges());
        }

        // Close when clicking outside the modal
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.close();
                }
            });
        }

        // Handle delete engineer button clicks using event delegation
        if (this.teamList) {
            this.teamList.addEventListener('click', (e) => {
                const deleteBtn = e.target.closest('.delete-engineer-btn');
                if (deleteBtn) {
                    const row = deleteBtn.closest('.team-member-row');
                    if (row) {
                        const engineerId = row.dataset.engineerId;
                        this.confirmDeleteEngineer(engineerId, row);
                    }
                }
            });
        }
    }

    open(projectId, projectName) {
        if (!this.modal) return;
        
        this.currentProjectId = projectId;
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Update modal title with project name
        const title = this.modal.querySelector('h2');
        if (title && projectName) {
            title.textContent = `Edit Detailed Engineering - ${projectName}`;
        }
        
        // Load team data
        this.loadTeamData(projectId);
    }

    close() {
        if (this.modal) {
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    async loadTeamData(projectId) {
        if (!this.teamList) return;
        
        // Show loading state
        this.teamList.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading team data...</p>
            </div>
        `;
        
        try {
            const response = await fetch(`/projects/${projectId}/monthly-assignments`);
            if (!response.ok) {
                throw new Error('Failed to load team data');
            }
            
            const data = await response.json();
            this.engineersData = data.monthly_assignments || [];
            this.renderTeamList(data);
        } catch (error) {
            console.error('Error loading team data:', error);
            this.teamList.innerHTML = `
                <div class="text-center py-4 text-red-600">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p>Failed to load team data. Please try again.</p>
                </div>
            `;
        }
    }

    renderTeamList(data) {
        if (!this.teamList || !this.teamMemberTemplate) return;
        
        this.teamList.innerHTML = '';
        
        if (!data.monthly_assignments || data.monthly_assignments.length === 0) {
            this.teamList.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-users-slash text-2xl mb-2"></i>
                    <p>No engineers assigned to this project yet.</p>
                </div>
            `;
            return;
        }
        
        data.monthly_assignments.forEach(engineer => {
            const clone = this.teamMemberTemplate.content.cloneNode(true);
            const row = clone.querySelector('.team-member-row');
            const nameElement = clone.querySelector('.engineer-name');
            const salaryInput = clone.querySelector('.salary-input');
            const roleSelect = clone.querySelector('.role-select');
            const teamHeadBadge = clone.querySelector('.team-head-badge');
            const projectEngineerBadge = clone.querySelector('.project-engineer-badge');
            
            // Set engineer data
            row.dataset.engineerId = engineer.engineer_id;
            nameElement.textContent = engineer.engineer_name;
            salaryInput.value = engineer.salary || '';
            
            // Set role
            if (engineer.is_team_head) {
                roleSelect.value = 'head';
                teamHeadBadge.classList.remove('hidden');
            } else {
                roleSelect.value = 'member';
                teamHeadBadge.classList.add('hidden');
            }
            
            // Show project engineer badge if this is the project engineer
            if (data.project_engineer_id && engineer.engineer_id === data.project_engineer_id) {
                projectEngineerBadge.classList.remove('hidden');
            } else {
                projectEngineerBadge.classList.add('hidden');
            }
            
            // Disable editing for project engineer (they can't be removed or have their role changed)
            if (data.project_engineer_id && engineer.engineer_id === data.project_engineer_id) {
                roleSelect.disabled = true;
                row.querySelector('.delete-engineer-btn').classList.add('hidden');
            }
            
            // Update team head badge when role changes
            roleSelect.addEventListener('change', (e) => {
                if (e.target.value === 'head') {
                    teamHeadBadge.classList.remove('hidden');
                } else {
                    teamHeadBadge.classList.add('hidden');
                }
            });
            
            this.teamList.appendChild(clone);
        });
    }

    async saveChanges() {
        if (!this.currentProjectId) return;
        
        const saveBtn = document.getElementById('saveDetailedEngineeringBtn');
        const originalBtnText = saveBtn ? saveBtn.innerHTML : '';
        
        try {
            // Show loading state
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            }
            
            // Collect all engineer data from the form
            const updates = [];
            const rows = this.teamList.querySelectorAll('.team-member-row');
            
            rows.forEach(row => {
                const engineerId = row.dataset.engineerId;
                const salary = parseFloat(row.querySelector('.salary-input').value) || 0;
                const role = row.querySelector('.role-select').value;
                
                updates.push({
                    engineer_id: engineerId,
                    salary: salary,
                    is_team_head: role === 'head'
                });
            });
            
            // Send updates to the server
            const response = await fetch(`/projects/${this.currentProjectId}/update-team-salaries`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ updates })
            });
            
            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(error.message || 'Failed to save changes');
            }
            
            // Show success message and close modal
            showCenteredNotification('Team salaries updated successfully!', 'success', 2000);
            this.close();
            
        } catch (error) {
            console.error('Error saving changes:', error);
            showCenteredNotification(error.message || 'Failed to save changes', 'error', 3000);
        } finally {
            // Restore button state
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalBtnText;
            }
        }
    }

    confirmDeleteEngineer(engineerId, row) {
        if (!confirm('Are you sure you want to remove this engineer from the project? This will also delete their salary information.')) {
            return;
        }
        
        this.deleteEngineer(engineerId, row);
    }
    
    async deleteEngineer(engineerId, row) {
        if (!this.currentProjectId || !engineerId) return;
        
        try {
            const response = await fetch(`/projects/${this.currentProjectId}/delete-engineer-salary/${engineerId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(error.message || 'Failed to delete engineer');
            }
            
            // Remove the row from the UI
            row.remove();
            
            // Show success message
            showCenteredNotification('Engineer removed successfully', 'success', 2000);
            
        } catch (error) {
            console.error('Error deleting engineer:', error);
            showCenteredNotification(error.message || 'Failed to delete engineer', 'error', 3000);
        }
    }
}

// Initialize the modal when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.detailedEngineeringModal = new DetailedEngineeringModal();
    
    // Add click handlers for all edit detailed engineering buttons
    document.querySelectorAll('.edit-detailed-engineering-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const projectId = btn.dataset.projectId;
            const projectName = btn.dataset.projectName;
            
            if (projectId && window.detailedEngineeringModal) {
                window.detailedEngineeringModal.open(projectId, projectName);
            }
        });
    });
});

// Helper function to show notifications (if not already defined)
function showCenteredNotification(message, type = 'info', duration = 3000) {
    // Implementation of showCenteredNotification function
    // (This should be moved to a shared utilities file if used in multiple places)
    let container = document.getElementById('notificationContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificationContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.left = '50%';
        container.style.transform = 'translateX(-50%)';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        document.body.appendChild(container);
    }
    
    const notification = document.createElement('div');
    notification.className = `px-6 py-3 rounded-lg shadow-lg text-white font-medium flex items-center justify-between min-w-[300px] max-w-[90vw] ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.style.transition = 'all 0.3s ease';
    notification.style.transform = 'translateY(-20px)';
    notification.style.opacity = '0';
    
    notification.innerHTML = `
        <span>${message}</span>
        <button class="ml-4 text-xl font-bold" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.style.transform = 'translateY(0)';
        notification.style.opacity = '1';
    }, 10);
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            notification.style.transform = 'translateY(-20px)';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
    
    return notification;
}
