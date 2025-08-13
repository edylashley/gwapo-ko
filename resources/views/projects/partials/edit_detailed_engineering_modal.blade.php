<!-- Edit Detailed Engineering Modal -->
<div id="editDetailedEngineeringModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-2xl relative animate-fadeInUp">
        <button id="closeEditDetailedEngineeringModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit Detailed Engineering</h2>
            <p class="text-gray-600">Manage engineer assignments and salaries for this project</p>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3 flex items-center">
                <i class="fas fa-users-cog mr-2 text-blue-600"></i>
                Current Engineering Team
            </h3>
            
            <div id="engineeringTeamList" class="space-y-4">
                <!-- Team members will be loaded here -->
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Loading team data...</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button type="button" id="cancelEditDetailedEngineeringBtn" class="px-6 py-2 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100">
                Close
            </button>
            <button type="button" id="saveDetailedEngineeringBtn" class="px-6 py-2 rounded-lg font-bold text-white bg-blue-600 hover:bg-blue-800 border-2 border-blue-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Template for team member row -->
<template id="teamMemberTemplate">
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 team-member-row" data-engineer-id="">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
                <span class="team-head-badge bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2 hidden">
                    Team Head
                </span>
                <span class="project-engineer-badge bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">
                    Project Engineer
                </span>
                <h4 class="engineer-name font-medium text-gray-800"></h4>
            </div>
            <button type="button" class="delete-engineer-btn text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary (₱)</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">₱</span>
                    </div>
                    <input type="number" min="0" step="0.01" class="salary-input focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="0.00">
                </div>
            </div>
            <div class="flex items-end">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select class="role-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="member">Team Member</option>
                        <option value="head">Team Head</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>
