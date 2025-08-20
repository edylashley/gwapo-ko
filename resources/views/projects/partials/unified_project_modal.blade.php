<!-- Unified Project Edit Modal -->
<div id="unifiedProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl relative animate-fadeInUp max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Edit Project</h2>
            <button id="closeUnifiedProjectModal" class="text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button type="button" data-tab="project-tab" class="tab-button tab-active py-4 px-6 text-center border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                    <i class="fas fa-project-diagram mr-2"></i>Project
                </button>
                <button type="button" data-tab="engineering-tab" class="tab-button py-4 px-6 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-users-cog mr-2"></i>Engineering Team
                </button>
                <button type="button" data-tab="expenses-tab" class="tab-button py-4 px-6 text-center border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-money-bill-wave mr-2"></i>Expenses
                </button>
            </nav>
        </div>
        
        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Project Tab -->
            <div id="project-tab" class="tab-content">
                <form id="projectDetailsForm" class="space-y-6">
                    <input type="hidden" name="project_id" id="project_id">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                            <input type="text" name="name" id="project_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="project_budget" class="block text-sm font-medium text-gray-700 mb-1">Budget (₱)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">₱</span>
                                </div>
                                <input type="number" name="budget" id="project_budget" min="0" step="0.01" required
                                       class="pl-7 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="fpp_code" class="block text-sm font-medium text-gray-700 mb-1">FPP Code
                                <span class="text-xs text-gray-500">(Auto-generated if left blank)</span>
                            </label>
                            <input type="text" name="fpp_code" id="fpp_code"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   pattern="[A-Za-z0-9-]+" 
                                   title="Only letters, numbers, and hyphens are allowed">
                            <p class="mt-1 text-sm text-red-600 hidden" id="fpp_code_error">This FPP code is already in use. Please enter a unique code.</p>
                        </div>
                        
                        <div>
                            <label for="project_engineer_id" class="block text-sm font-medium text-gray-700 mb-1">Project Engineer</label>
                            <select name="project_engineer_id" id="project_engineer_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Engineer</option>
                                @foreach(\App\Models\Engineer::all() as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Engineering Team Tab -->
            <div id="engineering-tab" class="tab-content hidden">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Engineering Team</h3>
                        <button type="button" id="addEngineerBtn" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i> Add Engineer
                        </button>
                    </div>
                    
                    <div id="engineeringTeamList" class="space-y-4">
                        <!-- Team members will be loaded here -->
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading team data...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Expenses Tab -->
            <div id="expenses-tab" class="tab-content hidden">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Project Expenses</h3>
                        <button type="button" id="addExpenseBtn" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-plus mr-2"></i> Add Expense
                        </button>
                    </div>
                    
                    <div id="expensesList" class="space-y-4">
                        <!-- Expenses will be loaded here -->
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Loading expenses...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" id="cancelUnifiedProjectBtn" 
                    class="px-6 py-2 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100">
                Cancel
            </button>
            <button type="button" id="saveUnifiedProjectBtn" 
                    class="px-6 py-2 rounded-lg font-bold text-white bg-blue-600 hover:bg-blue-800 border-2 border-blue-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Include the team member template from the existing modal -->
@include('projects.partials.team_member_template')
