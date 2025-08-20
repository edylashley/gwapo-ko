<!-- Team Member Template -->
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
            <div class="flex space-x-2">
                <button type="button" 
                        class="delete-engineer-btn flex items-center px-2 py-1.5 bg-red-500 text-white hover:bg-red-600 rounded-md shadow-sm text-xs transition-all duration-200"
                        title="Remove Engineer">
                    <i class="fas fa-user-minus mr-1 text-xs"></i>
                    Remove
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary (₱)</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">₱</span>
                    </div>
                    <input type="number" min="0" step="0.01" 
                           class="salary-input focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2 border" 
                           placeholder="0.00">
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

<!-- Add Engineer Form (initially hidden) -->
<div id="addEngineerFormContainer" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
    <h4 class="text-lg font-medium text-blue-800 mb-3">Add New Engineer</h4>
    <form id="addEngineerForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="newEngineerName" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" id="newEngineerName" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="newEngineerSalary" class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary (₱)</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">₱</span>
                    </div>
                    <input type="number" id="newEngineerSalary" min="0" step="0.01" required
                           class="pl-7 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label for="newEngineerRole" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="newEngineerRole" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="member">Team Member</option>
                    <option value="head">Team Head</option>
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex space-x-2 w-full">
                    <button type="submit" class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Engineer
                    </button>
                    <button type="button" id="cancelAddEngineerBtn" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
