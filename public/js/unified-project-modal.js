/**
 * Unified Project Modal Management
 * Handles the tabbed interface and data management for project editing
 */
class UnifiedProjectModal {
    constructor() {
        this.modal = document.getElementById('unifiedProjectModal');
        this.currentProjectId = null;
        this.currentProjectName = null;
        this.editQueue = [];
        this.isProcessingQueue = false;
        
        // Tab elements
        this.tabButtons = document.querySelectorAll('.tab-button');
        this.tabContents = document.querySelectorAll('.tab-content');
        
        // Form elements
        this.projectForm = document.getElementById('projectDetailsForm');
        this.engineeringTeamList = document.getElementById('engineeringTeamList');
        this.expensesList = document.getElementById('expensesList');
        this.teamMemberTemplate = document.getElementById('teamMemberTemplate');
        
        // Buttons
        this.saveBtn = document.getElementById('saveUnifiedProjectBtn');
        this.cancelBtn = document.getElementById('cancelUnifiedProjectBtn');
        this.closeBtn = document.getElementById('closeUnifiedProjectModal');
        this.addEngineerBtn = document.getElementById('addEngineerBtn');
        this.addExpenseBtn = document.getElementById('addExpenseBtn');
        
        // Initialize the modal
        this.initializeEventListeners();
    }
    
    /**
     * Initialize all event listeners
     */
    initializeEventListeners() {
        // Tab switching
        this.tabButtons.forEach(button => {
            button.addEventListener('click', (e) => this.switchTab(e));
        });
        
        // Modal controls
        this.closeBtn?.addEventListener('click', () => this.close());
        this.cancelBtn?.addEventListener('click', () => this.close());
        this.saveBtn?.addEventListener('click', () => this.saveAllChanges());
        
        // Add engineer button
        this.addEngineerBtn?.addEventListener('click', () => this.showAddEngineerForm());
        
        // FPP code validation
        const fppCodeInput = document.getElementById('fpp_code');
        if (fppCodeInput) {
            // Debounce the input event to avoid too many API calls
            let debounceTimer;
            fppCodeInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                const value = e.target.value.trim();
                
                // Only validate if there's a value and it's different from the original (for edit mode)
                if (value && value !== this.originalFppCode) {
                    debounceTimer = setTimeout(() => {
                        this.validateFppCode(value);
                    }, 500);
                } else {
                    // Clear any existing error if input is empty or same as original
                    this.clearFppCodeError();
                }
            });
        }
        
        // Close modal when clicking outside
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });
        
        // Add engineer form submission
        const addEngineerForm = document.getElementById('addEngineerForm');
        if (addEngineerForm) {
            addEngineerForm.addEventListener('submit', (e) => this.handleAddEngineer(e));
            
            // Cancel add engineer
            const cancelBtn = document.getElementById('cancelAddEngineerBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    document.getElementById('addEngineerFormContainer').classList.add('hidden');
                });
            }
        }
    }
    
    /**
     * Switch between tabs
     * @param {Event} e - The click event
     */
    switchTab(e) {
        const tabId = e.currentTarget.getAttribute('data-tab');
        
        // Update active tab button
        this.tabButtons.forEach(btn => {
            btn.classList.remove('tab-active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        e.currentTarget.classList.add('tab-active', 'border-blue-500', 'text-blue-600');
        e.currentTarget.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        
        // Show active tab content
        this.tabContents.forEach(content => {
            if (content.id === tabId) {
                content.classList.remove('hidden');
                
                // Load data when tab becomes active
                if (tabId === 'engineering-tab') {
                    this.loadEngineeringTeam();
                } else if (tabId === 'expenses-tab') {
                    this.loadExpenses();
                }
            } else {
                content.classList.add('hidden');
            }
        });
    }
    
    /**
     * Queue a project for editing
     * @param {string} projectId - The ID of the project to edit
     * @param {string} projectName - The name of the project
     * @param {boolean} forceOpen - Whether to force open the modal even if queue is empty
     */
    queueForEdit(projectId, projectName, forceOpen = false) {
        // Add to queue if not already in it
        if (!this.editQueue.some(item => item.projectId === projectId)) {
            this.editQueue.push({ projectId, projectName });
        }
        
        // Process queue if not already processing or if forced
        if (!this.isProcessingQueue || forceOpen) {
            this.processQueue();
        }
    }
    
    /**
     * Process the next project in the edit queue
     */
    processQueue() {
        if (this.editQueue.length === 0) {
            this.isProcessingQueue = false;
            return;
        }
        
        this.isProcessingQueue = true;
        const nextProject = this.editQueue.shift();
        this.open(nextProject.projectId, nextProject.projectName);
    }
    
    /**
     * Open the modal for a specific project
     * @param {string} projectId - The ID of the project to edit
     * @param {string} projectName - The name of the project
     */
    open(projectId, projectName) {
        this.currentProjectId = projectId;
        this.currentProjectName = projectName;
        this.originalFppCode = '';
        
        // Update modal title
        const title = this.modal.querySelector('h2');
        if (title && projectName) {
            title.textContent = `Edit Project - ${projectName}`;
        }
        
        // Load project data
        this.loadProjectData(projectId);
        
        // Show the modal
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Switch to first tab and load its data
        const firstTab = this.tabButtons[0];
        if (firstTab) {
            firstTab.click();
        }
    }
    
    /**
     * Close the modal
     */
    close() {
        if (this.modal) {
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
            
            // Process next in queue if available
            if (this.editQueue.length > 0) {
                setTimeout(() => {
                    this.processQueue();
                }, 300);
            } else {
                this.isProcessingQueue = false;
            }
        }
    }
    
    /**
     * Load project data into the form
     * @param {string} projectId - The ID of the project to load
     */
    async loadProjectData(projectId) {
        try {
            // Show loading state
            this.showLoadingState('project-tab', 'Loading project details...');
            
            // Fetch project data using the API endpoint
            const response = await fetch(`/api/projects/${projectId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to load project data');
            }
            
            const project = await response.json();
            
            // Populate form fields
            if (this.projectForm) {
                this.projectForm.querySelector('#project_id').value = project.id || '';
                this.projectForm.querySelector('#project_name').value = project.name || '';
                this.projectForm.querySelector('#project_budget').value = project.budget || '';
                this.projectForm.querySelector('#fpp_code').value = project.fpp_code || '';
                
                const engineerSelect = this.projectForm.querySelector('#project_engineer_id');
                if (engineerSelect) {
                    engineerSelect.value = project.project_engineer_id || '';
                }
                
                // Store the original FPP code for validation
                this.originalFppCode = project.fpp_code || '';
                
                // Clear any previous error states
                this.showNotification('Project data loaded successfully', 'success');
            }
            
        } catch (error) {
            console.error('Error loading project data:', error);
            this.showError('Failed to load project data');
        }
    }
    
    /**
     * Load engineering team data
     */
    async loadEngineeringTeam() {
        if (!this.currentProjectId) return;
        
        try {
            // Show loading state
            this.showLoadingState('engineering-tab', 'Loading engineering team...');
            
            // Fetch engineering team data
            const response = await fetch(`/projects/${this.currentProjectId}/monthly-assignments`);
            if (!response.ok) throw new Error('Failed to load engineering team');
            
            const data = await response.json();
            this.renderEngineeringTeam(data);
            
        } catch (error) {
            console.error('Error loading engineering team:', error);
            this.showError('Failed to load engineering team');
        }
    }
    
    /**
     * Render the engineering team list
     * @param {Array} teamMembers - Array of team member objects
     */
    renderEngineeringTeam(teamMembers) {
        if (!this.engineeringTeamList || !this.teamMemberTemplate) return;
        
        // Clear existing content
        this.engineeringTeamList.innerHTML = '';
        
        if (!teamMembers || teamMembers.length === 0) {
            this.engineeringTeamList.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-users-slash text-3xl mb-2"></i>
                    <p>No team members found</p>
                </div>
            `;
            return;
        }
        
        // Render each team member
        teamMembers.forEach(member => {
            const clone = document.importNode(this.teamMemberTemplate.content, true);
            const row = clone.querySelector('.team-member-row');
            
            // Set data attributes
            row.setAttribute('data-engineer-id', member.id);
            
            // Set member name
            const nameElement = row.querySelector('.engineer-name');
            if (nameElement) nameElement.textContent = member.name;
            
            // Set salary
            const salaryInput = row.querySelector('.salary-input');
            if (salaryInput) salaryInput.value = member.salary || '';
            
            // Set role
            const roleSelect = row.querySelector('.role-select');
            if (roleSelect) {
                roleSelect.value = member.is_team_head ? 'head' : 'member';
                
                // Show/hide team head badge based on selection
                const teamHeadBadge = row.querySelector('.team-head-badge');
                if (teamHeadBadge) {
                    teamHeadBadge.classList.toggle('hidden', roleSelect.value !== 'head');
                }
                
                // Update badge when role changes
                roleSelect.addEventListener('change', (e) => {
                    const isHead = e.target.value === 'head';
                    if (teamHeadBadge) {
                        teamHeadBadge.classList.toggle('hidden', !isHead);
                    }
                });
            }
            
            // Set up delete button
            const deleteBtn = row.querySelector('.delete-engineer-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => {
                    this.confirmDeleteEngineer(member.id, row);
                });
            }
            
            // Show/hide project engineer badge
            const projectEngineerBadge = row.querySelector('.project-engineer-badge');
            if (projectEngineerBadge) {
                const projectEngineerId = this.projectForm?.querySelector('#project_engineer_id')?.value;
                projectEngineerBadge.classList.toggle('hidden', member.id.toString() !== projectEngineerId);
            }
            
            this.engineeringTeamList.appendChild(clone);
        });
    }
    
    /**
     * Load expenses data
     */
    async loadExpenses() {
        if (!this.currentProjectId) return;
        
        try {
            // Show loading state
            this.showLoadingState('expenses-tab', 'Loading expenses...');
            
            // Fetch expenses data
            const response = await fetch(`/projects/${this.currentProjectId}/expenses`);
            if (!response.ok) throw new Error('Failed to load expenses');
            
            const expenses = await response.json();
            this.renderExpenses(expenses);
            
        } catch (error) {
            console.error('Error loading expenses:', error);
            this.showError('Failed to load expenses');
        }
    }
    
    /**
     * Render the expenses list
     * @param {Array} expenses - Array of expense objects
     */
    renderExpenses(expenses) {
        if (!this.expensesList) return;
        
        // Clear existing content
        this.expensesList.innerHTML = '';
        
        if (!expenses || expenses.length === 0) {
            this.expensesList.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-receipt text-3xl mb-2"></i>
                    <p>No expenses found</p>
                </div>
            `;
            return;
        }
        
        // Render each expense
        expenses.forEach(expense => {
            const expenseElement = document.createElement('div');
            expenseElement.className = 'bg-white rounded-lg p-4 border border-gray-200';
            expenseElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-medium text-gray-900">${expense.description || 'No description'}</h4>
                        <p class="text-sm text-gray-500">${new Date(expense.date).toLocaleDateString()}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">₱${parseFloat(expense.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                        <div class="mt-1 flex space-x-2">
                            <button class="edit-expense-btn text-xs text-blue-600 hover:text-blue-800" data-expense-id="${expense.id}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="delete-expense-btn text-xs text-red-600 hover:text-red-800" data-expense-id="${expense.id}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add event listeners for edit/delete buttons
            const editBtn = expenseElement.querySelector('.edit-expense-btn');
            if (editBtn) {
                editBtn.addEventListener('click', () => this.handleEditExpense(expense.id));
            }
            
            const deleteBtn = expenseElement.querySelector('.delete-expense-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => this.confirmDeleteExpense(expense.id, expenseElement));
            }
            
            this.expensesList.appendChild(expenseElement);
        });
    }
    
    /**
     * Show the add engineer form
     */
    showAddEngineerForm() {
        const formContainer = document.getElementById('addEngineerFormContainer');
        if (formContainer) {
            formContainer.classList.toggle('hidden');
            
            // Scroll to the form if it's being shown
            if (!formContainer.classList.contains('hidden')) {
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    }
    
    /**
     * Handle adding a new engineer
     * @param {Event} e - The form submission event
     */
    async handleAddEngineer(e) {
        e.preventDefault();
        
        if (!this.currentProjectId) return;
        
        const nameInput = document.getElementById('newEngineerName');
        const salaryInput = document.getElementById('newEngineerSalary');
        const roleSelect = document.getElementById('newEngineerRole');
        
        if (!nameInput || !salaryInput || !roleSelect) return;
        
        const engineerData = {
            name: nameInput.value.trim(),
            salary: parseFloat(salaryInput.value) || 0,
            is_team_head: roleSelect.value === 'head',
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
        
        try {
            const response = await fetch(`/projects/${this.currentProjectId}/engineers`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': engineerData._token
                },
                body: JSON.stringify(engineerData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to add engineer');
            }
            
            // Reload the engineering team
            this.loadEngineeringTeam();
            
            // Reset the form
            nameInput.value = '';
            salaryInput.value = '';
            roleSelect.value = 'member';
            
            // Hide the form
            document.getElementById('addEngineerFormContainer').classList.add('hidden');
            
            // Show success message
            this.showSuccess('Engineer added successfully');
            
        } catch (error) {
            console.error('Error adding engineer:', error);
            this.showError(error.message || 'Failed to add engineer');
        }
    }
    
    /**
     * Confirm before deleting an engineer
     * @param {string} engineerId - The ID of the engineer to delete
     * @param {HTMLElement} rowElement - The row element to remove if deletion is successful
     */
    async confirmDeleteEngineer(engineerId, rowElement) {
        const isConfirmed = await this.showConfirmation('Are you sure you want to remove this engineer from the project?');
        if (!isConfirmed) {
            return;
        }
        
        try {
            const response = await fetch(`/projects/${this.currentProjectId}/engineers/${engineerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to delete engineer');
            }
            
            // Remove the row from the UI
            rowElement.remove();
            
            // Show success message
            this.showSuccess('Engineer removed successfully');
            
            // If no engineers left, show empty state
            if (this.engineeringTeamList.children.length === 0) {
                this.engineeringTeamList.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-users-slash text-3xl mb-2"></i>
                        <p>No team members found</p>
                    </div>
                `;
            }
            
        } catch (error) {
            console.error('Error deleting engineer:', error);
            this.showError('Failed to remove engineer');
        }
    }
    
    /**
     * Handle editing an expense
     * @param {string} expenseId - The ID of the expense to edit
     */
    handleEditExpense(expenseId) {
        // This would open an edit expense modal in a real implementation
        console.log('Edit expense:', expenseId);
        // For now, just show an alert
        alert('Edit expense functionality will be implemented here');
    }
    
    /**
     * Confirm before deleting an expense
     * @param {string} expenseId - The ID of the expense to delete
     * @param {HTMLElement} expenseElement - The expense element to remove if deletion is successful
     */
    async confirmDeleteExpense(expenseId, expenseElement) {
        const isConfirmed = await this.showConfirmation('Are you sure you want to delete this expense?');
        if (!isConfirmed) {
            return;
        }
        
        try {
            const response = await fetch(`/projects/${this.currentProjectId}/expenses/${expenseId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to delete expense');
            }
            
            // Remove the expense from the UI
            expenseElement.remove();
            
            // Show success message
            this.showSuccess('Expense deleted successfully');
            
            // If no expenses left, show empty state
            if (this.expensesList.children.length === 0) {
                this.expensesList.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-receipt text-3xl mb-2"></i>
                        <p>No expenses found</p>
                    </div>
                `;
            }
            
        } catch (error) {
            console.error('Error deleting expense:', error);
            this.showError('Failed to delete expense');
        }
    }
    
    /**
     * Save all changes made in the modal
     */
    async saveAllChanges() {
        if (!this.currentProjectId || !this.projectForm) return;
        
        // Disable save button and show loading state
        const saveBtn = this.saveBtn;
        const originalBtnText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        
        try {
            // Get form data
            const formData = new FormData(this.projectForm);
            const fppCode = formData.get('fpp_code');
            
            // Validate FPP code if it has changed
            if (fppCode && fppCode !== this.originalFppCode) {
                const isValid = await this.validateFppCode(fppCode);
                if (!isValid) {
                    // Show error and re-enable save button
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalBtnText;
                    return;
                }
            }
            
            // Save project details
            const projectResponse = await fetch(`/projects/${this.currentProjectId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const responseData = await projectResponse.json();
            
            if (!projectResponse.ok) {
                // Handle FPP code validation error from server
                if (responseData.errors && responseData.errors.fpp_code) {
                    this.showFppCodeError(responseData.errors.fpp_code[0]);
                    this.showNotification('FPP code already exists', 'error');
                } else {
                    throw new Error(responseData.message || 'Failed to save project details');
                }
                return;
            }
            
            // Save engineering team changes
            const engineerRows = this.engineeringTeamList?.querySelectorAll('.team-member-row') || [];
            for (const row of engineerRows) {
                const engineerId = row.getAttribute('data-engineer-id');
                const salaryInput = row.querySelector('.salary-input');
                const roleSelect = row.querySelector('.role-select');
                
                if (!engineerId || !salaryInput || !roleSelect) continue;
                
                const updateData = {
                    salary: parseFloat(salaryInput.value) || 0,
                    is_team_head: roleSelect.value === 'head',
                    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                };
                
                const response = await fetch(`/projects/${this.currentProjectId}/engineers/${engineerId}/salary`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': updateData._token
                    },
                    body: JSON.stringify(updateData)
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Failed to update engineer:', errorData);
                    // Continue with other updates even if one fails
                }
            }
            
            // Show success message
            this.showSuccess('All changes saved successfully');
            
            // Close the modal after a short delay
            setTimeout(() => {
                this.close();
            }, 1000);
            
        } catch (error) {
            console.error('Error saving changes:', error);
            this.showError(error.message || 'Failed to save changes');
        } finally {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
        }
    }
    
    /**
     * Show a loading state in a specific tab
     * @param {string} tabId - The ID of the tab to show loading in
     * @param {string} message - The loading message to display
     */
    showLoadingState(tabId, message = 'Loading...') {
        const tabContent = document.getElementById(tabId);
        if (!tabContent) return;
        
        tabContent.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-3"></i>
                <p class="text-gray-600">${message}</p>
            </div>
        `;
    }
    
    /**
     * Show a success message
     * @param {string} message - The success message to display
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    /**
     * Show an error message
     * @param {string} message - The error message to display
     */
    showError(message) {
        this.showNotification(message, 'error');
    }
    
    /**
     * Show a custom confirmation dialog
     * @param {string} message - The confirmation message to display
     * @returns {Promise<boolean>} - Resolves to true if confirmed, false if cancelled
     */
    showConfirmation(message) {
        return new Promise((resolve) => {
            // Create modal overlay if it doesn't exist
            let modal = document.getElementById('confirmation-modal');
            
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'confirmation-modal';
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4';
                modal.style.display = 'none';
                
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                        <div class="mb-4">
                            <p id="confirmation-message" class="text-gray-800"></p>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button id="confirm-cancel" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">
                                Cancel
                            </button>
                            <button id="confirm-ok" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                OK
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Add event listeners
                document.getElementById('confirm-ok').addEventListener('click', () => {
                    modal.style.display = 'none';
                    resolve(true);
                });
                
                document.getElementById('confirm-cancel').addEventListener('click', () => {
                    modal.style.display = 'none';
                    resolve(false);
                });
                
                // Close when clicking outside
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                        resolve(false);
                    }
                });
            }
            
            // Set the message and show the modal
            document.getElementById('confirmation-message').textContent = message;
            modal.style.display = 'flex';
        });
    }
    
    /**
     * Show a notification
     * @param {string} message - The message to display
     * @param {string} type - The type of notification (success, error, etc.)
     */
    showNotification(message, type = 'info') {
        // Create notification element if it doesn't exist
        let notification = document.querySelector('.global-notification');
        
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-[110] px-6 py-3 rounded shadow-lg transition-all duration-300 transform';
            notification.style.maxWidth = '320px';
            document.body.appendChild(notification);
        }
        
        // Set notification content and styling
        notification.textContent = message;
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded shadow-lg transition-all duration-300 transform ${
            type === 'error' 
                ? 'bg-red-100 border-l-4 border-red-500 text-red-700' 
                : 'bg-green-100 border-l-4 border-green-500 text-green-700'
        }`;
        
        // Show notification
        notification.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            
            // Remove from DOM after animation
            setTimeout(() => {
                notification.style.display = 'none';
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 300);
        }, 2000);
    }
    
    /**
     * Validate if the FPP code is unique
     * @param {string} fppCode - The FPP code to validate
     */
    async validateFppCode(fppCode) {
        if (!fppCode) {
            this.clearFppCodeError();
            return true;
        }
        
        // Skip validation if the code hasn't changed
        if (fppCode === this.originalFppCode) {
            this.clearFppCodeError();
            return true;
        }
        
        try {
            const response = await fetch(`/api/validate-fpp?code=${encodeURIComponent(fppCode)}`);
            const data = await response.json();
            
            if (data.valid === false) {
                this.showFppCodeError('Code already in use');
                this.showNotification('FPP code already exists', 'error');
                return false;
            }
            
            // Clear any existing error
            this.clearFppCodeError();
            return true;
        } catch (error) {
            console.error('Error validating FPP code:', error);
            const errorMessage = 'An error occurred while validating the FPP code. Please try again.';
            this.showNotification(errorMessage, 'error');
            return false;
        }
    }
    
    /**
     * Show FPP code validation error
     * @param {string} message - The error message to display
     */
    showFppCodeError(message) {
        this.clearFppCodeError();
        
        const fppInput = document.getElementById('fpp_code');
        if (!fppInput) return;
        
        // Add error class to input
        fppInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        fppInput.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        
        // Create and show error message
        const errorElement = document.createElement('p');
        errorElement.className = 'mt-1 text-sm text-red-600 error-message';
        errorElement.id = 'fpp_code_error';
        errorElement.textContent = message;
        
        // Insert after the input
        fppInput.parentNode.insertBefore(errorElement, fppInput.nextSibling);
    }
    
    /**
     * Clear FPP code validation error
     */
    clearFppCodeError() {
        const fppInput = document.getElementById('fpp_code');
        if (!fppInput) return;
        
        // Remove error classes from input
        fppInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        fppInput.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');
        
        // Remove error message
        const errorElement = document.getElementById('fpp_code_error');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

// Initialize the modal when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the modal instance
    window.unifiedProjectModal = new UnifiedProjectModal();
    
    // Global function to open the modal from HTML
    window.openUnifiedProjectModal = function(projectId, projectName, isBatch = false) {
        if (!projectId || !projectName) {
            console.error('Missing project ID or name');
            return;
        }
        
        if (isBatch) {
            // Add to queue for batch processing
            window.unifiedProjectModal.queueForEdit(projectId, projectName);
        } else {
            // Open immediately (single edit)
            window.unifiedProjectModal.queueForEdit(projectId, projectName, true);
        }
    };
    
    // Add event listener for batch edit buttons if they exist
    document.addEventListener('click', (e) => {
        const batchEditBtn = e.target.closest('.batch-edit-project-btn');
        if (batchEditBtn) {
            e.preventDefault();
            const projectId = batchEditBtn.dataset.projectId;
            const projectName = batchEditBtn.dataset.projectName;
            if (projectId && projectName) {
                window.openUnifiedProjectModal(projectId, projectName, true);
            }
        }
    });
});
