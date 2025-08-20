<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Project Management - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
/* Reset any default margins/padding */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    margin: 0 !important;
    padding: 0 !important;
}

/* Glass Card Base Styles */
.glass-card {
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    
    /* Animation Starting State */
    opacity: 0;
    transform: translateY(40px);
    animation: floatIn 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

.glass-card.card-delay-1 { animation-delay: 0.1s; }
.glass-card.card-delay-2 { animation-delay: 0.25s; }
.glass-card.card-delay-3 { animation-delay: 0.4s; }
.glass-card.card-delay-4 { animation-delay: 0.55s; }

/* Hover Effect */
.glass-card {
    will-change: transform, box-shadow, background;
}

/* Floating Animation */
@keyframes floatIn {
    0% {
        opacity: 0;
        transform: translateY(40px);
    }
    70% {
        opacity: 1;
        transform: translateY(-4px); /* Slight overshoot for natural motion */
    }
    100% {
        opacity: 1;
        transform: translateY(0); /* Resting position */
    }
}


    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: none;
        }
    }

    .animate-fadeInUp {
        opacity: 0;
        transform: translateY(40px);
        animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }
    .custom-confirm-dialog {
    animation: confirmDialogFadeIn 0.3s ease-out;
}

@keyframes confirmDialogFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
    /* Project Card Specific Styles - moved to consolidated section below */
    /* Budget Status Indicators */
    .budget-status {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 1rem;
    }
    .budget-status.over-budget {
        background: rgba(239, 68, 68, 0.2);
        color: rgb(239, 68, 68);
    }
    .budget-status.near-budget {
        background: rgba(245, 158, 11, 0.2);
        color: rgb(245, 158, 11);
    }
    .budget-status.on-track {
        background: rgba(16, 185, 129, 0.2);
        color: rgb(16, 185, 129);
    }
</style>
<body class="min-h-screen transition-all duration-300" style="background: #064e3b;">

    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Project Management'])

    <!-- Main Content -->
    <div class="main-content px-4 pb-10 transition-all duration-300" style="margin-left: 256px;" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Skeleton Summary Cards (shown while loading) -->
        <div id="skeletonSummaryCards" class="skeleton-container grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="skeleton-summary-card">
                <div class="skeleton-icon skeleton-dark"></div>
                <div class="skeleton-text-content">
                    <div class="skeleton-subheader skeleton-dark"></div>
                    <div class="skeleton-header skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-summary-card">
                <div class="skeleton-icon skeleton-dark"></div>
                <div class="skeleton-text-content">
                    <div class="skeleton-subheader skeleton-dark"></div>
                    <div class="skeleton-header skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-summary-card">
                <div class="skeleton-icon skeleton-dark"></div>
                <div class="skeleton-text-content">
                    <div class="skeleton-subheader skeleton-dark"></div>
                    <div class="skeleton-header skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-summary-card">
                <div class="skeleton-icon skeleton-dark"></div>
                <div class="skeleton-text-content">
                    <div class="skeleton-subheader skeleton-dark"></div>
                    <div class="skeleton-header skeleton-dark"></div>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div id="summaryCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Projects -->
            <div class="glass-card card-delay-1 flex items-center p-6">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <span class="text-2xl">📁</span>
                </div>
                <div>
                    <div class="text-gray-100 text-sm uppercase mb-1">Total Projects</div>
                    <div id="totalProjectsCard" class="text-2xl font-bold text-green-100 drop-shadow">{{ count($projects) }}</div>
                </div>
            </div>
            <!-- Total Budget -->
            <div class="glass-card card-delay-2 flex items-center p-6">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <div class="text-gray-100 text-sm uppercase mb-1">Total Budget</div>
                    <div id="totalBudgetCard" class="text-2xl font-bold text-blue-100 drop-shadow">₱{{ number_format($totalBudget) }}</div>
                </div>
            </div>
            <!-- Total Spent -->
            <div class="glass-card card-delay-3 flex items-center p-6">
                <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mr-4">
                    <span class="text-2xl">💸</span>
                </div>
                <div>
                    <div class="text-gray-100 text-sm uppercase mb-1">Total Spent</div>
                    <div id="totalSpentCard" class="text-2xl font-bold text-yellow-100 drop-shadow">₱{{ number_format($totalSpent, 2) }}</div>
                </div>
            </div>
            <!-- Remaining Budget -->
            <div class="glass-card card-delay-1 flex items-center p-6">
                <div class="bg-{{ $remainingBudget < 0 ? 'red' : 'green' }}-100 text-{{ $remainingBudget < 0 ? 'red' : 'green' }}-600 rounded-full p-3 mr-4">
                    <span class="text-2xl">{{ $remainingBudget < 0 ? '⚠️' : '✅' }}</span>
                </div>
                <div>
                    <div class="text-gray-100 text-sm uppercase mb-1">Remaining Budget</div>
                    <div id="remainingBudgetCard" class="text-2xl font-bold text-{{ $remainingBudget < 0 ? 'red' : 'green' }}-100 drop-shadow">
                        ₱{{ number_format($remainingBudget, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar and Controls -->
        <div class="animate-fadeInUp flex flex-col lg:flex-row justify-between items-center gap-4 mb-6" style="animation-delay: 0.20s;">
            <!-- Left Side: Search and Selection Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                <!-- Search Bar -->
                <div class="relative w-full sm:w-96">
                    <input
                        type="text"
                        id="projectSearch"
                        placeholder="Search by project name, F/P/P code, or engineer..."
                        class="w-full px-4 py-3 pl-12 bg-white bg-opacity-10 backdrop-blur-sm border border-blue-300 border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-blue-300 text-xl">🔍</span>
                    </div>
                </div>

                <!-- Selection Controls -->
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="selectAllCheckbox" class="w-5 h-5 text-emerald-600 bg-white bg-opacity-20 border-2 border-white border-opacity-30 rounded focus:ring-emerald-500 focus:ring-2">
                        <span class="text-white font-semibold">Select All</span>
                    </label>

                    <button id="trackRecordBtn" class="track-record-btn bg-slate-700 hover:bg-slate-800 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 border border-slate-600 disabled:opacity-50 disabled:cursor-not-allowed ease-in-out transform hover:-translate-y-1" disabled>
                        Track Record (<span id="selectedCount">0</span>)
                    </button>
                    @if(auth()->user()->is_admin)
                    <button id="deleteSelectedBtn" class="delete-selected-btn bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 border border-red-500 disabled:opacity-50 disabled:cursor-not-allowed ease-in-out transform hover:-translate-y-1" disabled>
                        Delete Selected (<span id="deleteSelectedCount">0</span>)
                    </button>
                    @endif
                </div>
            </div>

            <!-- Right Side: Add Project Button (Admin Only) -->
            @if(auth()->user()->is_admin)
            <button 
    id="openAddProjectModal" 
    class="techy-btn px-10 py-3 text-lg font-semibold rounded-xl shadow-lg flex items-center gap-3 whitespace-nowrap 
           bg-green-600 text-white 
           hover:bg-green-400 hover:shadow-2xl 
           transition-all duration-300 ease-in-out transform hover:-translate-y-1">
    <span class="text-2xl">＋</span> Add New Project
</button>

            @endif
        </div>

        <!-- Skeleton Projects Grid (shown while loading) -->
        <div id="skeletonProjectsGrid" class="hidden">
            <div class="skeleton-card">
                <div class="flex items-start mb-4">
                    <div class="skeleton-avatar skeleton-dark mr-3"></div>
                    <div class="flex-1">
                        <div class="skeleton-header skeleton-dark mb-2"></div>
                        <div class="skeleton-subheader skeleton-dark"></div>
                    </div>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="skeleton-line skeleton-dark"></div>
                    <div class="skeleton-line-short skeleton-dark"></div>
                    <div class="skeleton-line skeleton-dark"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
                <div class="flex space-x-2">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-card">
                <div class="flex items-start mb-4">
                    <div class="skeleton-avatar skeleton-dark mr-3"></div>
                    <div class="flex-1">
                        <div class="skeleton-header skeleton-dark mb-2"></div>
                        <div class="skeleton-subheader skeleton-dark"></div>
                    </div>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="skeleton-line skeleton-dark"></div>
                    <div class="skeleton-line-short skeleton-dark"></div>
                    <div class="skeleton-line skeleton-dark"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
                <div class="flex space-x-2">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-card">
                <div class="flex items-start mb-4">
                    <div class="skeleton-avatar skeleton-dark mr-3"></div>
                    <div class="flex-1">
                        <div class="skeleton-header skeleton-dark mb-2"></div>
                        <div class="skeleton-subheader skeleton-dark"></div>
                    </div>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="skeleton-line skeleton-dark"></div>
                    <div class="skeleton-line-short skeleton-dark"></div>
                    <div class="skeleton-line skeleton-dark"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
                <div class="flex space-x-2">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
            </div>
            <div class="skeleton-card">
                <div class="flex items-start mb-4">
                    <div class="skeleton-avatar skeleton-dark mr-3"></div>
                    <div class="flex-1">
                        <div class="skeleton-header skeleton-dark mb-2"></div>
                        <div class="skeleton-subheader skeleton-dark"></div>
                    </div>
                </div>
                <div class="space-y-3 mb-6">
                    <div class="skeleton-line skeleton-dark"></div>
                    <div class="skeleton-line-short skeleton-dark"></div>
                    <div class="skeleton-line skeleton-dark"></div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
                <div class="flex space-x-2">
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                    <div class="skeleton-button skeleton-dark"></div>
                </div>
            </div>
        </div>
        
        <!-- Projects Grid - Single Column for Wider Cards -->
        <div id="projectsGrid" class="grid grid-cols-2 gap-6 mb-16 mt-4 max-w-8xl mx-auto">
            @forelse($projects as $project)
                @php
                    $totalSpent = $project->totalSpentWithDetailedEngineering();
                    $remaining = $project->remainingBudgetWithDetailedEngineering();
                    $percentUsed = $project->budget > 0 ? ($totalSpent / $project->budget) * 100 : 0;
                    $isOverBudget = $totalSpent > $project->budget;
                    $isNearBudget = $percentUsed >= 80 && !$isOverBudget;

                    $cardClass = 'glass-card p-6 project-card';
                    if ($isOverBudget) {
                        $cardClass .= ' border-red-400 bg-red-900 bg-opacity-20';
                    } elseif ($isNearBudget) {
                        $cardClass .= ' border-yellow-400 bg-yellow-900 bg-opacity-20';
                    } else {
                        $cardClass .= ' border-green-400 bg-green-900 bg-opacity-10';
                    }
                @endphp
                <div class="glass-card card-delay-1 p-4 relative project-card group">
                    <!-- Checkbox (Top-left corner) -->
                    <div class="absolute top-3 left-3">
                        <input type="checkbox"
                               class="project-checkbox w-4 h-4 text-emerald-600 bg-white bg-opacity-20 border-2 border-white border-opacity-30 rounded focus:ring-emerald-500 focus:ring-2"
                               data-project-id="{{ $project->id }}"
                               data-project-name="{{ $project->name }}">
                    </div>
                    
                    <!-- Three-dot menu -->
                    <div class="absolute top-3 right-3">
                        <button class="three-dot-menu p-0 text-gray-300 hover:text-white focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </button>
                        <!-- Dropdown menu -->
                        <div class="hidden group-hover:block absolute right-0 mt-2 bg-gray-800 rounded-lg shadow-lg z-10 border border-gray-700 px-2 py-1">
                            <button class="archive-project-btn px-4 py-2 text-sm text-gray-300 text-center hover:bg-gray-700 rounded-lg block w-auto">
                                Archive
                            </button>
                            <button class="delete-project-btn px-4 py-2 text-sm text-red-400 text-center hover:bg-gray-700 rounded-lg block w-auto">
                                Delete
                            </button>
                        </div>
                    </div>
                    
                    <!-- Project Information (Centered) -->
                    <div class="flex flex-col items-center mb-2">
                        <div class="text-center w-full">
                            <h3 class="text-base font-bold px-3 text-white mb-1 line-clamp-1">{{ $project->name }}</h3>
                            <p class="text-gray-300 text-xs mb-1">F/P/P: {{ $project->fpp_code ?? 'N/A' }}</p>
                            <div class="text-lg font-bold text-green-200 mb-2">₱{{ number_format($project->budget) }}</div>
                            
                            <!-- Project Engineer -->
                            @if($project->projectEngineer)
                                <div class="text-green-200 text-sm flex items-center justify-center">
                                    <span class="mr-1">👷</span>
                                    <span class="engineer-name">{{ $project->projectEngineer->name }}</span>
                                    @if($project->projectEngineer->specialization)
                                        <span class="text-gray-300 ml-1">({{ $project->projectEngineer->specialization }})</span>
                                    @endif
                                </div>
                            @else
                                <div class="text-gray-300 text-sm flex items-center justify-center">
                                    <span class="mr-1">👷</span>
                                    <span class="engineer-name">No engineer assigned</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Hidden action buttons for JavaScript functionality -->
                    @if(auth()->user()->is_admin)
                        <div class="hidden action-buttons">
                            <button class="archive-project-btn" data-project-id="{{ $project->id }}" data-project-name="{{ $project->name }}"></button>
                            <button class="delete-project-btn" data-project-id="{{ $project->id }}" data-project-name="{{ $project->name }}"></button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">📁</div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Projects Available</h3>
                    <p class="text-green-200 mb-6">
                        @if(auth()->user()->is_admin)
                            Create your first project to start tracking expenses and budgets.
                        @else
                            No projects have been created yet.
                        @endif
                    </p>
                    @if(auth()->user()->is_admin)
                        <button id="openAddProjectModalEmpty" class="techy-btn px-6 py-3 rounded-lg font-bold">
                            Create First Project
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div id="addExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
            <button id="closeAddExpenseModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">💰</span> Project Expenses
            </h2>
            <div id="expenseProjectInfo" class="bg-gray-100 rounded-lg p-3 mb-4">
                <div class="text-sm text-gray-600">Project:</div>
                <div id="expenseProjectName" class="font-semibold text-gray-800"></div>
            </div>
            <form id="addExpenseForm" class="space-y-4">
                <input type="hidden" id="expenseProjectId" name="project_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <!-- Materials -->
                <div class="space-y-1">
                    <label for="materialsAmount" class="block text-gray-700 font-semibold">Materials (₱)</label>
                    <input type="number" id="materialsAmount" name="materials" min="0" step="0.01" 
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" 
                           placeholder="0.00" value="0.00">
                </div>
                
                <!-- Labor -->
                <div class="space-y-1">
                    <label for="laborAmount" class="block text-gray-700 font-semibold">Labor (₱)</label>
                    <input type="number" id="laborAmount" name="labor" min="0" step="0.01" 
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" 
                           placeholder="0.00" value="0.00">
                </div>
                
                <!-- Fuel/Oil/Equipment -->
                <div class="space-y-1">
                    <label for="fuelAmount" class="block text-gray-700 font-semibold">Fuel/Oil/Equipment (₱)</label>
                    <input type="number" id="fuelAmount" name="fuel" min="0" step="0.01" 
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" 
                           placeholder="0.00" value="0.00">
                </div>
                
                <!-- Miscellaneous & Contingencies -->
                <div class="space-y-1">
                    <label for="miscAmount" class="block text-gray-700 font-semibold">Miscellaneous & Contingencies (₱)</label>
                    <input type="number" id="miscAmount" name="miscellaneous" min="0" step="0.01" 
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" 
                           placeholder="0.00" value="0.00">
                </div>
                
                <!-- Others -->
                <div class="space-y-1">
                    <label for="othersAmount" class="block text-gray-700 font-semibold">Others (₱)</label>
                    <input type="number" id="othersAmount" name="others" min="0" step="0.01" 
                           class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" 
                           placeholder="0.00" value="0.00">
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="backToProjectBtn" 
                            class="px-6 py-3 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100">
                        Back
                    </button>
                    <button type="button" id="addSalaryBtn" 
                        class="px-6 py-3 rounded-lg font-bold text-white bg-green-600 hover:bg-green-800 border-2 border-green-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center">
                        <span>Add Salary</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                </div>
            </form>
        </div>
    </div>

    <!-- Add Salary Modal -->
    <div id="addSalaryModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden transition">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-lg relative animate-fadeInUp max-h-[85vh] overflow-y-auto">
            <button id="closeAddSalaryModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">👥</span> Add Salary & Team Assignment
            </h2>
            <div id="salaryProjectInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="text-sm text-blue-600 font-medium">Project:</div>
                <div id="salaryProjectName" class="font-bold text-blue-800"></div>
            </div>
            <form id="addSalaryForm" class="space-y-4">
                <input type="hidden" id="salaryProjectId" name="project_id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <!-- Project Engineer Section -->
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="text-lg mr-2">👨‍💼</span>
                        <label for="projectEngineer" class="block text-gray-700 font-semibold">Project Engineer</label>
                    </div>
                    <select id="projectEngineer" name="project_engineer_id" class="w-full rounded-lg px-3 py-2 border border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" required>
                        <option value="">Select Project Engineer</option>
                        @foreach(\App\Models\Engineer::where('is_active', true)->get() as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">The project engineer will oversee the entire project</p>
                </div>
                
                <!-- Team Members Section -->
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <span class="text-lg mr-2">👥</span>
                        <label class="block text-gray-700 font-semibold">Team Members</label>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">Select team members and assign individual salaries. One member will be the team head.</p>
                    
                    <div id="teamMembersContainer" class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-white">
                        @foreach(\App\Models\Engineer::where('is_active', true)->get() as $engineer)
                            <div class="team-member-item flex items-center space-x-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-200" data-engineer-id="{{ $engineer->id }}">
                                <div class="flex items-center space-x-2 flex-1">
                                    <input type="checkbox" id="team_member_{{ $engineer->id }}" name="team_members[]" value="{{ $engineer->id }}" 
                                           class="team-member-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                    <label for="team_member_{{ $engineer->id }}" class="text-sm font-medium text-gray-700 flex-1">{{ $engineer->name }}</label>
                                </div>
                                
                                <!-- Team Head Selection -->
                                <div class="flex items-center space-x-1">
                                    <input type="radio" name="team_head" value="{{ $engineer->id }}" id="team_head_{{ $engineer->id }}" 
                                           class="team-head-radio rounded-full border-gray-300 text-green-600 focus:ring-green-500 h-3 w-3" disabled>
                                    <label for="team_head_{{ $engineer->id }}" class="text-xs text-green-600 font-medium">Head</label>
                                </div>
                                
                                <!-- Individual Salary Input -->
                                <div class="flex items-center space-x-1">
                                    <span class="text-xs text-gray-500 font-medium">₱</span>
                                    <input type="number" name="individual_salaries[{{ $engineer->id }}]" 
                                           class="individual-salary-input w-20 rounded px-2 py-1 border border-gray-300 text-xs focus:outline-none focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-400" 
                                           placeholder="0.00" min="0" step="0.01" disabled>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Team Summary -->
                    <div id="teamSummary" class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-medium text-blue-800">Team Head:</span>
                            <span id="teamHeadName" class="text-blue-600 font-semibold">-</span>
                        </div>
                        <div class="flex justify-between items-center text-xs mt-1">
                            <span class="font-medium text-blue-800">Team Members:</span>
                            <span id="teamMembersCount" class="text-blue-600 font-semibold">0</span>
                        </div>
                        <div class="flex justify-between items-center text-xs mt-1">
                            <span class="font-medium text-blue-800">Total Salary:</span>
                            <span id="totalSalaryDisplay" class="text-blue-600 font-semibold">₱0.00</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end items-center pt-3 border-t border-gray-200">
                    <div class="flex space-x-3">
                        <button type="button" id="cancelSalaryBtn" 
                                class="px-4 py-2 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100 text-sm">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-800 px-4 py-2 rounded-lg font-bold text-white border-2 border-green-700 shadow-lg hover:shadow-xl transition-all duration-200 text-sm">
                            <span id="submitSalaryText">Save Salary & Team</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Salaries Modal -->
    <div id="editSalariesModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden transition">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-3xl relative animate-fadeInUp max-h-[90vh] overflow-y-auto">
            <button id="closeEditSalariesModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-xl font-bold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">👥</span> Current Engineering Team
            </h2>
            <div id="editSalariesProjectInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                <div class="text-sm text-blue-600 font-medium">Project:</div>
                        <div id="editSalariesProjectName" class="font-bold text-blue-800 text-lg"></div>
            </div>
                    <div class="text-right">
                        <div class="text-sm text-blue-600 font-medium">Period:</div>
                        <div id="editSalariesPeriod" class="font-medium text-blue-700"></div>
                    </div>
                </div>
                    </div>
                    
            <!-- Team Display Section -->
            <div id="teamDisplayContainer" class="space-y-4">
                        <!-- Will be populated by JavaScript -->
                <div class="text-center py-8 text-gray-400">
                    <div class="text-4xl mb-2">👷</div>
                    <p>Loading team information...</p>
                    </div>
                </div>
                
            <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Last updated: <span id="lastUpdatedTime">Just now</span>
                </div>
                    <div class="flex space-x-3">
                                         <button type="button" id="refreshTeamBtn" 
                             class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 font-medium transition-all duration-200 flex items-center space-x-2">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                         </svg>
                         <span>Refresh</span>
                     </button>
                     <button type="button" id="testRefreshBtn" 
                             class="px-4 py-2 rounded-lg border border-orange-300 text-orange-700 hover:bg-orange-100 font-medium transition-all duration-200 flex items-center space-x-2">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                         </svg>
                         <span>Test Refresh</span>
                     </button>
                     <button id="debugCostBtn" 
                             class="px-4 py-2 rounded-lg border border-red-300 text-red-700 hover:bg-red-100 font-medium transition-all duration-200 flex items-center space-x-2">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                         </svg>
                         <span>Debug Cost</span>
                     </button>
                    <button type="button" id="closeEditSalariesBtn" 
                            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-all duration-200">
                        Close
                        </button>
                    </div>
                </div>
        </div>
    </div>

    <!-- Back to Top Button - Centered in main content area (accounting for sidebar) -->
    <button id="backToTopBtn" class="fixed bottom-8 bg-green-800 hover:bg-green-700 text-white px-6 py-3 rounded-full shadow-lg transition-all duration-300 z-50 opacity-0 invisible hover:scale-105 flex items-center space-x-3 pointer-events-auto" onclick="scrollToTop()" style="left: calc(50% + 128px); transform: translateX(-50%);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
        <span class="text-base font-medium">TO TOP</span>
    </button>

<!-- Edit Expense Modal (for Track Records) -->
<div id="editExpenseModal" class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-40 hidden transition" style="z-index:9999;">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
        <button id="closeEditExpenseModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center">
            <span class="mr-2"></span> Edit Expense
        </h2>
        <form id="editExpenseForm" class="space-y-4">
            <input type="hidden" id="editExpenseId" name="expense_id">
            <div>
                <label for="editExpenseDescription" class="block text-gray-700 font-semibold mb-1">Description</label>
                <input id="editExpenseDescription" name="description" type="text" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" required>
            </div>
            <div>
                <label for="editExpenseAmount" class="block text-gray-700 font-semibold mb-1">Amount</label>
                <input id="editExpenseAmount" name="amount" type="number" min="0.01" step="0.01" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" required placeholder="0.00">
            </div>
            <!-- Date field removed as requested -->
            <input type="hidden" id="editExpenseDate" name="date" value="">
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelEditExpenseBtn" class="px-6 py-3 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100">Cancel</button>
                <button type="submit" class="bg-green-600 hover:bg-green-800 px-6 py-3 rounded-lg font-bold text-white border-2 border-emerald-700 shadow-lg hover:shadow-xl transition-all duration-200">
                    <span id="submitEditExpenseText">Update Expense</span>
                </button>
            </div>
        </form>
    </div>
</div>

    <!-- Add Project Modal -->
    <div id="addProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
            <button id="closeAddProjectModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center">
                <span class="mr-2"></span> <span id="modalTitle">Add New Project</span>
            </h2>
            <form id="projectForm" class="space-y-4">
                <input type="hidden" id="projectId" name="project_id">
                <div>
                    <label for="projectName" class="block text-gray-700 font-semibold mb-1">Project Name</label>
                    <input id="projectName" name="name" type="text" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="projectBudget" class="block text-gray-700 font-semibold mb-1">Budget (₱)</label>
                    <input type="number" id="projectBudget" name="budget" min="0.01" step="0.01" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="projectDate" class="block text-gray-700 font-semibold mb-1">Date</label>
                    <input type="date" id="projectDate" name="project_date" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label for="projectFppCode" class="block text-gray-700 font-semibold mb-1">F/P/P Code <span class="text-gray-500 text-sm"></span></label>
                    <input id="projectFppCode" name="fpp_code" type="text" class="w-full rounded-lg px-4 py-2 border border-gray-300 focus:outline-none focus:border-blue-500" placeholder="Enter F/P/P code" required>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" id="cancelProjectBtn" class="px-6 py-3 rounded-lg border-2 border-gray-500 text-gray-800 hover:bg-gray-200 font-semibold transition-all duration-200 bg-gray-100">Cancel</button>
                    <button type="button" id="saveAndAddExpenseBtn" class="px-6 py-3 rounded-lg font-bold text-white bg-green-600 hover:bg-green-800 border-2 border-green-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center">
                        <span>Proceed to Add Expense</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Track Record Modal -->
    <div id="trackRecordModal" class="fixed inset-0 z-50 bg-black bg-opacity-40 hidden transition">
        <div class="bg-white rounded-2xl shadow-xl animate-fadeInUp" style="position: fixed; top: 5%; left: 5%; right: 5%; bottom: 5%; display: flex; flex-direction: column;">
            <!-- Fixed Header -->
            <div class="p-6 border-b border-gray-200 relative flex-shrink-0">
                <button id="closeTrackRecordModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200 z-10">&times;</button>
                <h2 id="trackRecordTitle" class="text-2xl font-bold text-gray-800 flex items-center pr-12">
                    Project Track Records
                </h2>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center mt-4">
                    <div class="text-gray-600">
                        <span id="selectedProjectsInfo">Selected Projects: 0</span>
                    </div>
                    <div class="flex space-x-3">
                        <button id="printAllBtn" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                            Print All
                        </button>
                        <button id="showReceiptsBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                            View Receipts
                        </button>
                        
                    </div>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-6" style="min-height: 0;">
                <div id="projectsContainer">
                    <!-- Will be populated by JavaScript -->
                </div>
                <!-- Extra padding at bottom for better scrolling -->
                <div style="height: 20px;"></div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="fixed inset-0 z-50 bg-black bg-opacity-40 hidden transition" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div class="bg-white rounded-2xl shadow-xl animate-fadeInUp" style="position: fixed; top: 5%; left: 5%; right: 5%; bottom: 5%; display: flex; flex-direction: column;">
            <!-- Fixed Header -->
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-semibold text-gray-800">Project Receipt</h3>
                <div class="flex items-center space-x-3">
                    <button onclick="printReceipt()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2">
                    <span>Print</span>
                    </button>
                    <button onclick="downloadReceiptPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2">
                        <span>Download</span>
                    </button>
                    <button id="closeReceiptModal" class="text-gray-400 hover:text-red-500 text-2xl bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">&times;</button>
                </div>
            </div>
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <div id="receiptContent">
                    <!-- Receipt content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <style>
/* Apply dashboard-style hover effects to glass cards on project page */
.glass-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    border-radius: 20px;
    transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
    opacity: 1;
    position: relative;
    overflow: hidden;
}

/* Dashboard-style hover effect - same as dashboard glass cards */
.glass-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 12px 32px 0 #00c6ff55;
    background: rgba(255,255,255,0.22);
}
    transition: width 1s ease-in-out;
    border-radius: 999px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
/* Enhanced Button Styles */
.action-button {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.action-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}
.action-button:hover::before {
    width: 300px;
    height: 300px;
}
/* Shimmer Effect for Loading States */
.shimmer {
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.1) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
/* Status Badge Animations */
.status-badge {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
/* Responsive Grid Improvements */
@media (max-width: 640px) {
    .project-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
    }
    
    .project-card {
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    .project-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
}
@media (min-width: 641px) and (max-width: 1024px) {
    .project-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}
@media (min-width: 1025px) {
    .project-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
}
</style>

    <script>
        // Pass admin status to JavaScript
        const isUserAdmin = @json(auth()->user()->is_admin);

        // Centralized Modal Management System
        const ModalManager = {
            modalStack: [],

            // Register when a modal is opened
            openModal: function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal && modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    // Add to stack if not already there
                    if (!this.modalStack.includes(modalId)) {
                        this.modalStack.push(modalId);
                    }
                    console.log('Modal opened:', modalId, 'Stack:', this.modalStack);
                }
            },

            // Register when a modal is closed
            closeModal: function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    // Remove from stack
                    const index = this.modalStack.indexOf(modalId);
                    if (index > -1) {
                        this.modalStack.splice(index, 1);
                    }
                    console.log('Modal closed:', modalId, 'Stack:', this.modalStack);
                }
            },

            // Close the most recently opened modal
            closeTopModal: function() {
                if (this.modalStack.length > 0) {
                    const topModalId = this.modalStack[this.modalStack.length - 1];
                    this.closeModal(topModalId);
                    return true;
                }
                return false;
            }
        };

        // Single ESC key handler for all modals and search
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // First priority: Close the most recently opened modal
                const closed = ModalManager.closeTopModal();
                if (closed) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }

                // Second priority: Clear search if no modals are open
                const searchInput = document.getElementById('projectSearch');
                if (searchInput && searchInput.value !== '') {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    e.preventDefault();
                }
            }
        });

        // Global variables
        let currentReceiptData = null;



        // Modal functionality
        const addProjectModal = document.getElementById('addProjectModal');
        const openAddProjectBtn = document.getElementById('openAddProjectModal');
        const openAddProjectBtnEmpty = document.getElementById('openAddProjectModalEmpty');
        const closeAddProjectBtn = document.getElementById('closeAddProjectModal');
        const cancelProjectBtn = document.getElementById('cancelProjectBtn');
        const projectForm = document.getElementById('projectForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitBtnText = document.getElementById('submitBtnText');

        // Add Expense Modal functionality
        const addExpenseModal = document.getElementById('addExpenseModal');
        const closeAddExpenseBtn = document.getElementById('closeAddExpenseModal');
        const addExpenseForm = document.getElementById('addExpenseForm');



        // Custom centered notification with blur effect
        function showCenteredNotification(message, type = 'success', duration = 1000) {
            // Create backdrop overlay with blur
            const overlay = document.createElement('div');
            overlay.className = 'notification-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(6px);
                z-index: 9998;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;

            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            notification.className = `notification-center ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg`;
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.8);
                z-index: 9999;
                min-width: 300px;
                max-width: 500px;
                transition: all 0.3s ease;
                opacity: 0;
            `;
            notification.innerHTML = `
                <div class="flex items-center justify-center">
                    <span class="font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(overlay);
            document.body.appendChild(notification);

            // Animate in
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
                notification.style.opacity = '1';
                notification.style.transform = 'translate(-50%, -50%) scale(1)';
            });

            // Auto remove after duration
            setTimeout(() => {
                overlay.style.opacity = '0';
                notification.style.opacity = '0';
                notification.style.transform = 'translate(-50%, -50%) scale(0.8)';

                setTimeout(() => {
                    if (document.body.contains(overlay)) {
                        document.body.removeChild(overlay);
                    }
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, duration);
        }

        // Load project engineers into dropdown
        function loadProjectEngineers() {
            fetch('/api/engineers')
                .then(response => response.json())
                .then(data => {
                    const engineers = data.data || data; // Handle different response formats
                    const select = document.getElementById('projectEngineer');
                    // Clear existing options except the first one
                    select.innerHTML = '<option value="">Select an engineer...</option>';

                    // Filter to only show engineers who can be project engineers
                    const projectEngineers = engineers.filter(engineer => engineer.can_be_project_engineer);

                    projectEngineers.forEach(engineer => {
                        const option = document.createElement('option');
                        option.value = engineer.id;
                        option.textContent = `${engineer.name}`;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading engineers:', error);   
                    // Fallback: show message in dropdown
                    const select = document.getElementById('projectEngineer');
                    select.innerHTML = '<option value="">No engineers available</option>';
                });
        }

        // Open modal for new project
        function openAddModal() {
            modalTitle.textContent = 'Add New Project';
            submitBtnText.textContent = 'Add Project';
            projectForm.reset();
            document.getElementById('projectId').value = '';
            loadProjectEngineers(); // Load engineers when opening modal
            ModalManager.openModal('addProjectModal');
        }

        // Open modal for editing project
        function openEditModal(id, name, budget, fppCode = '', engineerId = '') {
            modalTitle.textContent = 'Edit Project';
            submitBtnText.textContent = 'Update Project';
            document.getElementById('projectId').value = id;
            document.getElementById('projectName').value = name;
            document.getElementById('projectBudget').value = budget;
            document.getElementById('projectFppCode').value = fppCode || '';

            // Load engineers and then set the selected engineer
            loadProjectEngineers();
            setTimeout(() => {
                document.getElementById('projectEngineer').value = engineerId || '';
            }, 100); // Small delay to ensure options are loaded

            ModalManager.openModal('addProjectModal');
        }

        // Close modal
        function closeModal() {
            ModalManager.closeModal('addProjectModal');
        }

        // Open expense modal
        function openExpenseModal(projectId, projectName) {
            document.getElementById('expenseProjectId').value = projectId;
            document.getElementById('expenseProjectName').textContent = projectName;
            document.getElementById('expenseDate').value = new Date().toISOString().split('T')[0]; // Set today's date
            addExpenseForm.reset();
            document.getElementById('expenseProjectId').value = projectId; // Reset clears this, so set it again
            ModalManager.openModal('addExpenseModal');
        }

        // Close expense modal
        function closeExpenseModal() {
            ModalManager.closeModal('addExpenseModal');
        }

        // Event listeners - wrapped in DOMContentLoaded to ensure elements exist
        document.addEventListener('DOMContentLoaded', function() {
            // Project modal event listeners
            if (openAddProjectBtn) {
                openAddProjectBtn.addEventListener('click', openAddModal);
            }
            if (openAddProjectBtnEmpty) {
                openAddProjectBtnEmpty.addEventListener('click', openAddModal);
            }
            if (closeAddProjectBtn) {
        closeAddProjectBtn.addEventListener('click', closeModal);
            }
            if (cancelProjectBtn) {
        cancelProjectBtn.addEventListener('click', closeModal);
            }

        // Add Expense Modal event listeners - using correct button IDs
        const closeAddExpenseBtn = document.getElementById('closeAddExpenseModal');
        const backToProjectBtn = document.getElementById('backToProjectBtn');
        
        if (closeAddExpenseBtn) {
        closeAddExpenseBtn.addEventListener('click', closeExpenseModal);
        }
        if (backToProjectBtn) {
            backToProjectBtn.addEventListener('click', function() {
                // Close expense modal and open project modal
                ModalManager.closeModal('addExpenseModal');
                setTimeout(() => {
                    ModalManager.openModal('addProjectModal');
                }, 300);
            });
        }

        // ESC handler removed - using centralized modal manager

        // Edit project buttons
        document.querySelectorAll('.edit-project-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.projectId;
                const name = this.dataset.projectName;
                const budget = this.dataset.projectBudget;
                const fppCode = this.dataset.projectFppCode;
                const engineerId = this.dataset.projectEngineerId;
                openEditModal(id, name, budget, fppCode, engineerId);
            });
        });

        // Add Expense buttons
        document.querySelectorAll('.add-expense-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const projectId = this.dataset.projectId;
                const projectName = this.dataset.projectName;
                openExpenseModal(projectId, projectName);
            });
        });

        // Archive project buttons
        document.querySelectorAll('.archive-project-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const id = this.dataset.projectId;
                const name = this.dataset.projectName;
                console.log('Archive button clicked:', { id, name, isUserAdmin });

                // Add a small delay to ensure the click event is fully processed
                setTimeout(() => {
                    showCenteredConfirm(
                        `Are you sure you want to archive "${name}"? Archived projects can be viewed in the Archive section.`,
                        () => archiveProject(id, name)
                    );
                }, 10);
            });
        });

        // Delete project buttons
        document.querySelectorAll('.delete-project-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const id = this.dataset.projectId;
                const name = this.dataset.projectName;
                console.log('Delete button clicked:', { id, name, isUserAdmin });

                // Add a small delay to ensure the click event is fully processed
                setTimeout(() => {
                    showCenteredConfirm(
                        `Are you sure you want to delete "${name}"? You can restore it from Recently Deleted if needed.`,
                        () => deleteProject(id, name)
                    );
                }, 10);
            });
        });

        // Add Salary Modal functionality
        const addSalaryBtn = document.getElementById('addSalaryBtn');
        const closeAddSalaryBtn = document.getElementById('closeAddSalaryModal');
        const cancelSalaryBtn = document.getElementById('cancelSalaryBtn');
        const addSalaryForm = document.getElementById('addSalaryForm');

        if (addSalaryBtn) {
            addSalaryBtn.addEventListener('click', async function() {
                // First save the expenses
                const expenseForm = document.getElementById('addExpenseForm');
                const formData = new FormData(expenseForm);
                
                try {
                    // Show loading state
                    const originalText = addSalaryBtn.innerHTML;
                    addSalaryBtn.disabled = true;
                    addSalaryBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Saving...';
                    
                    // Send expense data
                    const response = await fetch('/expenses', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        // Show success message
                        showCenteredNotification('Expenses saved successfully!', 'success', 1000);
                        
                        // Get project info from the expense modal
                        const projectId = document.getElementById('expenseProjectId').value;
                        const projectName = document.getElementById('expenseProjectName').textContent;
                        
                        // Set project info in salary modal
                        document.getElementById('salaryProjectId').value = projectId;
                        document.getElementById('salaryProjectName').textContent = projectName;
                        
                        // Close expense modal and open salary modal
                        ModalManager.closeModal('addExpenseModal');
                        setTimeout(() => {
                            ModalManager.openModal('addSalaryModal');
                            // Initialize UX after modal is opened
                            setTimeout(() => {
                                if (window.initializeSalaryModalUX) {
                                    window.initializeSalaryModalUX();
                                }
                            }, 200);
                        }, 300);
                    } else {
                        throw new Error(data.message || 'Failed to save expenses');
                    }
                } catch (error) {
                    console.error('Error saving expenses:', error);
                    showCenteredNotification(error.message || 'An error occurred while saving expenses', 'error', 1000);
                } finally {
                    // Reset button state
                    addSalaryBtn.disabled = false;
                    addSalaryBtn.innerHTML = originalText;
                }
            });
        }

        if (closeAddSalaryBtn) {
            closeAddSalaryBtn.addEventListener('click', function() {
                ModalManager.closeModal('addSalaryModal');
            });
        }

        if (cancelSalaryBtn) {
            cancelSalaryBtn.addEventListener('click', function() {
                ModalManager.closeModal('addSalaryModal');
                // Add a small delay to ensure the modal is fully closed before refreshing
                setTimeout(() => {
                    window.location.reload();
                }, 100);
            });
        }

        // Handle salary form submission
        if (addSalaryForm) {
            addSalaryForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                console.log('Salary form submission started');
                
                // Get the form submit button and save its original state
                const submitBtn = addSalaryForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Saving...';
                
                try {
                    // Get all form data
                    const formData = new FormData(addSalaryForm);
                    
                    // Debug: Log the initial form data
                    console.log('Initial FormData entries:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    
                    // Get all checked team members (salary is optional)
                    const checkedMembers = [];
                    const checkboxes = document.querySelectorAll('.team-member-checkbox:checked');
                    
                    checkboxes.forEach(checkbox => {
                        const engineerId = checkbox.value;
                        const salaryInput = document.querySelector(`.individual-salary-input[data-engineer-id="${engineerId}"]`);
                        const salary = salaryInput ? parseFloat(salaryInput.value) || 0 : 0;
                        
                        // Include all checked members, even with 0 salary
                        checkedMembers.push(engineerId);
                        formData.append('individual_salaries[' + engineerId + ']', salary);
                        console.log(`Adding engineer ${engineerId} with salary ${salary}`);
                    });
                    
                    // Clear any existing team members from form data
                    formData.delete('team_members[]');
                    
                    // Add team members to form data (empty array is allowed)
                    checkedMembers.forEach(id => formData.append('team_members[]', id));
                    
                    // Debug: Log the final form data before submission
                    console.log('Final FormData entries before submission:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    
                    console.log('Checked members:', checkedMembers);
                    console.log('Submitting form with project_engineer_id:', formData.get('project_engineer_id'));
                    
                    // The form can be submitted with just the project engineer and no team members
                    
                    // Submit the form data
                    const response = await fetch('/monthly-assignments/create-salary', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        const message = checkedMembers.length > 0 
                            ? 'Team and salary assignment saved successfully!' 
                            : 'Project engineer updated successfully!';
                        
                        showCenteredNotification(message, 'success', 1000);
                        
                        // Close modal and reload page
                        ModalManager.closeModal('addSalaryModal');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to save salary assignment');
                    }
                } catch (error) {
                    console.error('Error saving salary assignment:', error);
                    showCenteredNotification(
                        error.message || 'An error occurred while saving the assignment', 
                        'error', 
                        1000
                    );
                } finally {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // Enhanced Salary Modal UX - Team Member Management
        window.initializeSalaryModalUX = function() {
            console.log('Initializing salary modal UX...');
            
            const teamMemberCheckboxes = document.querySelectorAll('.team-member-checkbox');
            const teamHeadRadios = document.querySelectorAll('.team-head-radio');
            const individualSalaryInputs = document.querySelectorAll('.individual-salary-input');
            const teamSummary = document.getElementById('teamSummary');
            const teamHeadName = document.getElementById('teamHeadName');
            const teamMembersCount = document.getElementById('teamMembersCount');
            const totalSalaryDisplay = document.getElementById('totalSalaryDisplay');
            const projectEngineerSelect = document.getElementById('projectEngineer');
            
            console.log('Found elements:', {
                teamMemberCheckboxes: teamMemberCheckboxes.length,
                teamHeadRadios: teamHeadRadios.length,
                individualSalaryInputs: individualSalaryInputs.length,
                teamSummary: !!teamSummary,
                teamHeadName: !!teamHeadName,
                teamMembersCount: !!teamMembersCount,
                totalSalaryDisplay: !!totalSalaryDisplay
            });

            // Handle team member checkbox changes
            teamMemberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Team member checkbox changed:', this.value, this.checked);
                    const engineerId = this.value;
                    const teamHeadRadio = document.getElementById(`team_head_${engineerId}`);
                    const salaryInput = document.querySelector(`input[name="individual_salaries[${engineerId}]"]`);
                    const teamMemberItem = this.closest('.team-member-item');

                    if (this.checked) {
                        // Enable team head radio and salary input
                        teamHeadRadio.disabled = false;
                        salaryInput.disabled = false;
                        teamMemberItem.classList.add('bg-blue-50', 'border-blue-300');
                        teamMemberItem.classList.remove('bg-gray-50', 'border-gray-200');
                        console.log('Enabled team head radio and salary input for engineer:', engineerId);
                    } else {
                        // Disable team head radio and salary input
                        teamHeadRadio.disabled = true;
                        teamHeadRadio.checked = false;
                        salaryInput.disabled = true;
                        salaryInput.value = '';
                        teamMemberItem.classList.remove('bg-blue-50', 'border-blue-300');
                        teamMemberItem.classList.add('bg-gray-50', 'border-gray-200');
                        console.log('Disabled team head radio and salary input for engineer:', engineerId);
                    }
                    
                    updateTeamSummary();
                });
            });

            // Handle team head radio changes
            teamHeadRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    console.log('Team head radio changed:', this.value, this.checked);
                    if (this.checked) {
                        // Uncheck other team head radios
                        teamHeadRadios.forEach(otherRadio => {
                            if (otherRadio !== this) {
                                otherRadio.checked = false;
                            }
                        });
                        
                        // Highlight the selected team head
                        const teamMemberItem = this.closest('.team-member-item');
                        teamMemberItem.classList.add('bg-green-50', 'border-green-300');
                        teamMemberItem.classList.remove('bg-blue-50', 'border-blue-300');
                        console.log('Set team head to engineer:', this.value);
                    }
                    
                    updateTeamSummary();
                });
            });

            // Handle individual salary input changes
            individualSalaryInputs.forEach(input => {
                input.addEventListener('input', function() {
                    console.log('Salary input changed:', this.name, this.value);
                    updateTeamSummary();
                });
            });

            // Update team summary
            function updateTeamSummary() {
                const selectedMembers = Array.from(teamMemberCheckboxes).filter(cb => cb.checked);
                const selectedTeamHead = Array.from(teamHeadRadios).find(radio => radio.checked);
                let totalSalary = 0;

                // Count team members
                const memberCount = selectedMembers.length;
                teamMembersCount.textContent = memberCount;

                // Get team head name
                if (selectedTeamHead) {
                    const teamHeadLabel = document.querySelector(`label[for="team_head_${selectedTeamHead.value}"]`);
                    teamHeadName.textContent = teamHeadLabel ? teamHeadLabel.textContent : 'Unknown';
                } else {
                    teamHeadName.textContent = '-';
                }

                // Calculate total salary
                selectedMembers.forEach(checkbox => {
                    const engineerId = checkbox.value;
                    const salaryInput = document.querySelector(`input[name="individual_salaries[${engineerId}]"]`);
                    const salary = parseFloat(salaryInput.value) || 0;
                    totalSalary += salary;
                });

                // Update display
                totalSalaryDisplay.textContent = `₱${totalSalary.toFixed(2)}`;

                // Show/hide team summary
                if (memberCount > 0) {
                    teamSummary.classList.remove('hidden');
                } else {
                    teamSummary.classList.add('hidden');
                }

                // Validate form
                validateSalaryForm();
            }

            // Validate the form
            function validateSalaryForm() {
                const selectedMembers = Array.from(teamMemberCheckboxes).filter(cb => cb.checked);
                const selectedTeamHead = Array.from(teamHeadRadios).find(radio => radio.checked);
                const projectEngineer = document.getElementById('projectEngineer').value;
                const submitBtn = document.querySelector('#addSalaryForm button[type="submit"]');

                let isValid = true;
                let errorMessage = '';

                // Check if project engineer is selected (only required field)
                if (!projectEngineer) {
                    isValid = false;
                    errorMessage = 'Please select a Project Engineer';
                }
                // Only validate team members and salaries if at least one member is selected
                else if (selectedMembers.length > 0) {
                    // Check if team head is selected when there are team members
                    if (!selectedTeamHead) {
                        isValid = false;
                        errorMessage = 'Please select a team head';
                    }
                    // Check if all selected members have valid salaries
                    else {
                        const hasEmptySalaries = selectedMembers.some(checkbox => {
                            const engineerId = checkbox.value;
                            const salaryInput = document.querySelector(`input[name="individual_salaries[${engineerId}]"]`);
                            // Only validate salary if the member is checked
                            return checkbox.checked && (!salaryInput.value || parseFloat(salaryInput.value) <= 0);
                        });

                        if (hasEmptySalaries) {
                            isValid = false;
                            errorMessage = 'Please enter salary amounts for all selected team members';
                        }
                    }
                }

                // Update submit button state
                if (submitBtn) {
                    submitBtn.disabled = !isValid;
                    if (!isValid) {
                        submitBtn.title = errorMessage;
                    } else {
                        submitBtn.title = '';
                    }
                }

                return isValid;
            }

            
            // Handle project engineer change
            if (projectEngineerSelect) {
                projectEngineerSelect.addEventListener('change', function() {
                    const selectedEngineerId = this.value;
                    
                    // Reset all team member checkboxes, radios, and inputs
                    teamMemberCheckboxes.forEach(checkbox => {
                        const engineerId = checkbox.value;
                        const teamHeadRadio = document.getElementById(`team_head_${engineerId}`);
                        const salaryInput = document.querySelector(`input[name="individual_salaries[${engineerId}]"]`);
                        const teamMemberItem = checkbox.closest('.team-member-item');
                        
                        if (engineerId === selectedEngineerId) {
                            // Disable and uncheck the project engineer
                            checkbox.disabled = true;
                            checkbox.checked = false;
                            teamHeadRadio.disabled = true;
                            teamHeadRadio.checked = false;
                            salaryInput.disabled = true;
                            salaryInput.value = '';
                            teamMemberItem.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            // Re-enable other engineers if they were previously disabled
                            if (checkbox.disabled) {
                                checkbox.disabled = false;
                                teamMemberItem.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        }
                    });
                    
                    updateTeamSummary();
                });
                
                // Trigger change event in case a project engineer is pre-selected
                if (projectEngineerSelect.value) {
                    projectEngineerSelect.dispatchEvent(new Event('change'));
                }
            }

            // Initialize validation
            validateSalaryForm();
        };

        // Initialize salary modal UX when modal opens
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize when salary modal is opened
            const addSalaryBtn = document.getElementById('addSalaryBtn');
            if (addSalaryBtn) {
                addSalaryBtn.addEventListener('click', function() {
                    // Initialize UX after a short delay to ensure modal is open
                    setTimeout(() => {
                        initializeSalaryModalUX();
                    }, 100);
                });
            }
            
            // Also initialize when modal is opened via ModalManager
            const salaryModal = document.getElementById('addSalaryModal');
            if (salaryModal) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            if (!salaryModal.classList.contains('hidden')) {
                                // Modal is now visible, initialize UX
                                setTimeout(() => {
                                    initializeSalaryModalUX();
                                }, 100);
                            }
                        }
                    });
                });
                
                observer.observe(salaryModal, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
            
            // Initialize edit salaries modal event listeners
            const closeEditSalariesBtn = document.getElementById('closeEditSalariesModal');
            const cancelEditSalariesBtn = document.getElementById('cancelEditSalariesBtn');
            
            if (closeEditSalariesBtn) {
                closeEditSalariesBtn.addEventListener('click', function() {
                    ModalManager.closeModal('editSalariesModal');
                });
            }
            
            if (cancelEditSalariesBtn) {
                cancelEditSalariesBtn.addEventListener('click', function() {
                    ModalManager.closeModal('editSalariesModal');
                });
            }
        });

        // Format date as Month Year (e.g., "August 2023")
        function formatMonthYear(dateString) {
            if (!dateString) return 'Current Month';
            const date = new Date(dateString);
            return date.toLocaleString('default', { month: 'long', year: 'numeric' });
        }

        // Format a date string to a readable format
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        // Format currency
        function formatCurrency(amount) {
            if (amount === null || amount === undefined) return '₱0.00';
            return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
        }

        // Edit Salaries Modal Functionality
        window.openEditSalariesModal = function(projectId) {
            console.log('Opening edit salaries modal for project:', projectId);
            
            // Set loading state
            const teamContainer = document.getElementById('teamDisplayContainer');
            teamContainer.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <p>Loading team information...</p>
                </div>
            `;
            
            // Set current time
            document.getElementById('lastUpdatedTime').textContent = 'Just now';
            
            // Open modal immediately for better UX
            ModalManager.openModal('editSalariesModal');
            
            // Fetch current team data
            fetch(`/projects/${projectId}/monthly-assignments`)
                .then(response => response.json())
                .then(data => {
                    console.log('Team data:', data);
                    
                    // Set project name
                    document.getElementById('editSalariesProjectName').textContent = data.project_name || 'Unknown Project';
                    
                    // Set period
                    const periodElement = document.getElementById('editSalariesPeriod');
                    periodElement.textContent = formatMonthYear(data.period) || 'Current Month';
                    
                    // Render team members
                    renderTeamMembers(data.monthly_assignments || []);
                    
                                         // Set up refresh button
                     const refreshBtn = document.getElementById('refreshTeamBtn');
                     if (refreshBtn) {
                         refreshBtn.onclick = function() {
                             openEditSalariesModal(projectId);
                         };
                     }
                     
                     // Set up test refresh button
                     const testRefreshBtn = document.getElementById('testRefreshBtn');
                     if (testRefreshBtn) {
                         testRefreshBtn.onclick = function() {
                             console.log('Test refresh button clicked');
                             const currentSelectedProjects = window.currentSelectedProjects || [];
                             if (currentSelectedProjects.length > 0) {
                                 console.log('Test refreshing with projects:', currentSelectedProjects);
                                 openMultipleTrackRecordModal(currentSelectedProjects);
                             } else {
                                 console.log('No projects to refresh');
                             }
                         };
                     }
                     
                     // Set up debug cost button
                     const debugCostBtn = document.getElementById('debugCostBtn');
                     if (debugCostBtn) {
                         debugCostBtn.onclick = function() {
                             console.log('Debug cost button clicked');
                             const currentSelectedProjects = window.currentSelectedProjects || [];
                             if (currentSelectedProjects.length > 0) {
                                 const projectId = currentSelectedProjects[0].id;
                                 console.log('Debugging cost for project:', projectId);
                                 fetch(`/projects/${projectId}/debug-engineering-cost`)
                                     .then(response => response.json())
                                     .then(data => {
                                         console.log('Debug cost data:', data);
                                         alert(`Debug Cost Data:\n\nProject: ${data.project_name}\nDetailed Engineering Cost: ₱${data.detailed_engineering_cost}\nTotal Spent: ₱${data.total_spent}\nTotal with Engineering: ₱${data.total_spent_with_engineering}\nRemaining Budget: ₱${data.remaining_budget}\n\nMonthly Assignments: ${data.monthly_assignments.length} engineers`);
                                     })
                                     .catch(error => {
                                         console.error('Error fetching debug data:', error);
                                         alert('Error fetching debug data');
                                     });
                             } else {
                                 console.log('No projects to debug');
                             }
                         };
                     }
                    
                    // Set up close button
                    const closeBtn = document.getElementById('closeEditSalariesBtn');
                    if (closeBtn) {
                        closeBtn.onclick = function() {
                            ModalManager.closeModal('editSalariesModal');
                        };
                    }
                })
                .catch(error => {
                    console.error('Error fetching team data:', error);
                    teamContainer.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <div class="text-4xl mb-2">⚠️</div>
                            <p>Failed to load team information.</p>
                            <button onclick="openEditSalariesModal(${projectId})" 
                                    class="mt-2 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                Try Again
                            </button>
                        </div>
                    `;
                });
        };
        
        // Render detailed engineering team in the modal
        function renderDetailedEngineeringTeam(assignments) {
            const container = document.getElementById('engineeringTeamList');
            
            if (!assignments || assignments.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <div class="text-4xl mb-2">👥</div>
                        <p>No team members assigned to this project yet.</p>
                    </div>
                `;
                return;
            }
            
            // Group assignments by role
            const teamHead = assignments.find(a => a.is_team_head);
            const projectEngineer = assignments.find(a => a.is_project_engineer);
            const teamMembers = assignments.filter(a => !a.is_team_head && !a.is_project_engineer);
            
            let html = '';
            
            // Project Engineer Section
            if (projectEngineer) {
                html += `
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Project Engineer
                        </h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-medium text-green-800">${projectEngineer.engineer_name || 'N/A'}</div>
                                    <div class="text-sm text-green-600 mt-1">${projectEngineer.engineer_specialization || 'No specialization'}</div>
                                    <div class="text-xs text-green-500 mt-1">Assigned: ${formatDate(projectEngineer.assigned_at)}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-700">Salary</div>
                                    <div class="text-lg font-bold text-green-800">${formatCurrency(projectEngineer.salary)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Team Head Section
            if (teamHead) {
                html += `
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Team Head
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-medium text-blue-800">${teamHead.engineer_name || 'N/A'}</div>
                                    <div class="text-sm text-blue-600 mt-1">${teamHead.engineer_specialization || 'No specialization'}</div>
                                    <div class="text-xs text-blue-500 mt-1">Assigned: ${formatDate(teamHead.assigned_at)}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-blue-700">Salary</div>
                                    <div class="text-lg font-bold text-blue-800">${formatCurrency(teamHead.salary)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Team Members Section
            if (teamMembers.length > 0) {
                html += `
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Team Members (${teamMembers.length})
                        </h3>
                        <div class="space-y-3" id="teamMembersList">
                `;
                
                teamMembers.forEach(member => {
                    html += `
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 team-member-row" data-engineer-id="${member.engineer_id}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="font-medium text-purple-800">${member.engineer_name || 'N/A'}</div>
                                        <button class="text-red-500 hover:text-red-700 delete-engineer-btn" 
                                                data-engineer-id="${member.engineer_id}" 
                                                data-engineer-name="${member.engineer_name}"
                                                title="Remove engineer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="text-sm text-purple-600 mt-1">${member.engineer_specialization || 'No specialization'}</div>
                                    <div class="text-xs text-purple-500 mt-1">Assigned: ${formatDate(member.assigned_at)}</div>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-sm font-medium text-purple-700">Salary</div>
                                    <div class="text-lg font-bold text-purple-800">${formatCurrency(member.salary)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            // Total Salary
            const totalSalary = assignments.reduce((sum, member) => sum + (parseFloat(member.salary) || 0), 0);
            html += `
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Total Monthly Salary:</span>
                        <span class="text-xl font-bold text-blue-700">${formatCurrency(totalSalary)}</span>
                    </div>
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    <p class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <span>Project Engineer</span>
                    </p>
                    <p class="flex items-center mt-1">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        <span>Team Head</span>
                    </p>
                    <p class="flex items-center mt-1">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                        <span>Team Member</span>
                    </p>
                </div>
            `;
            
            container.innerHTML = html;
        }
        
        // Render team members in the modal
        function renderTeamMembers(assignments) {
            const container = document.getElementById('teamDisplayContainer');
            
            if (!assignments || assignments.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <div class="text-4xl mb-2">👥</div>
                        <p>No team members assigned to this project yet.</p>
                    </div>
                `;
                return;
            }
            
            // Separate team head and members
            const teamHead = assignments.find(a => a.is_team_head);
            const teamMembers = assignments.filter(a => !a.is_team_head);
            
            let html = '';
            
            // Team Head Section
            if (teamHead) {
                html += `
                    <div class="mb-6">
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Team Head
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-medium text-blue-800">${teamHead.engineer_name || 'N/A'}</div>
                                    <div class="text-sm text-blue-600 mt-1">${teamHead.engineer_specialization || 'No specialization'}</div>
                                    <div class="text-xs text-blue-500 mt-1">Assigned: ${formatDate(teamHead.assigned_at)}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-blue-700">Salary</div>
                                    <div class="text-lg font-bold text-blue-800">${formatCurrency(teamHead.salary)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Team Members Section
            if (teamMembers.length > 0) {
                html += `
                    <div>
                        <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Team Members (${teamMembers.length})
                        </h3>
                        <div class="space-y-3">
                `;
                
                teamMembers.forEach(member => {
                    html += `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-medium text-green-800">${member.engineer_name || 'N/A'}</div>
                                    <div class="text-sm text-green-600 mt-1">${member.engineer_specialization || 'No specialization'}</div>
                                    <div class="text-xs text-green-500 mt-1">Assigned: ${formatDate(member.assigned_at)}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-700">Salary</div>
                                    <div class="text-lg font-bold text-green-800">${formatCurrency(member.salary)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            // Total Salary
            const totalSalary = assignments.reduce((sum, member) => sum + (parseFloat(member.salary) || 0), 0);
            html += `
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Total Monthly Salary:</span>
                        <span class="text-xl font-bold text-blue-700">${formatCurrency(totalSalary)}</span>
                    </div>
                </div>
            `;
            
            container.innerHTML = html;
        }

        // Populate edit team members
        function populateEditTeamMembers(assignments) {
            const container = document.getElementById('editTeamMembersContainer');
            let html = '';
            
            assignments.forEach(assignment => {
                html += `
                    <div class="edit-team-member-item flex items-center space-x-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-200" data-engineer-id="${assignment.engineer_id}">
                        <div class="flex items-center space-x-2 flex-1">
                            <input type="checkbox" id="edit_team_member_${assignment.engineer_id}" name="edit_team_members[]" value="${assignment.engineer_id}" 
                                   class="edit-team-member-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4" checked>
                            <label for="edit_team_member_${assignment.engineer_id}" class="text-sm font-medium text-gray-700 flex-1">${assignment.engineer_name}</label>
                        </div>
                        
                        <!-- Team Head Selection -->
                        <div class="flex items-center space-x-1">
                            <input type="radio" name="edit_team_head" value="${assignment.engineer_id}" id="edit_team_head_${assignment.engineer_id}" 
                                   class="edit-team-head-radio rounded-full border-gray-300 text-green-600 focus:ring-green-500 h-3 w-3" ${assignment.is_team_head ? 'checked' : ''}>
                            <label for="edit_team_head_${assignment.engineer_id}" class="text-xs text-green-600 font-medium">Head</label>
                        </div>
                        
                        <!-- Individual Salary Input -->
                        <div class="flex items-center space-x-1">
                            <span class="text-xs text-gray-500 font-medium">₱</span>
                            <input type="number" name="edit_individual_salaries[${assignment.engineer_id}]" 
                                   class="edit-individual-salary-input w-20 rounded px-2 py-1 border border-gray-300 text-xs focus:outline-none focus:border-blue-500" 
                                   placeholder="0.00" min="0" step="0.01" value="${assignment.salary || 0}">
                        </div>
                        
                        <!-- Remove Button -->
                        <button type="button" onclick="removeTeamMember(${assignment.engineer_id})" 
                                class="text-red-600 hover:text-red-800 text-xs font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors duration-150">
                            ✕
                        </button>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Remove team member function
        window.removeTeamMember = function(engineerId) {
            const memberItem = document.querySelector(`[data-engineer-id="${engineerId}"]`);
            if (memberItem) {
                memberItem.remove();
                updateEditTeamSummary();
            }
        };

        // Initialize Edit Salaries UX
        window.initializeEditSalariesUX = function() {
            console.log('Initializing edit salaries UX...');
            
            const editTeamMemberCheckboxes = document.querySelectorAll('.edit-team-member-checkbox');
            const editTeamHeadRadios = document.querySelectorAll('.edit-team-head-radio');
            const editIndividualSalaryInputs = document.querySelectorAll('.edit-individual-salary-input');
            const editTeamSummary = document.getElementById('editTeamSummary');
            const editTeamHeadName = document.getElementById('editTeamHeadName');
            const editTeamMembersCount = document.getElementById('editTeamMembersCount');
            const editTotalSalaryDisplay = document.getElementById('editTotalSalaryDisplay');

            // Handle team member checkbox changes
            editTeamMemberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    console.log('Edit team member checkbox changed:', this.value, this.checked);
                    const engineerId = this.value;
                    const teamHeadRadio = document.getElementById(`edit_team_head_${engineerId}`);
                    const salaryInput = document.querySelector(`input[name="edit_individual_salaries[${engineerId}]"]`);
                    const teamMemberItem = this.closest('.edit-team-member-item');

                    if (this.checked) {
                        // Enable team head radio and salary input
                        teamHeadRadio.disabled = false;
                        salaryInput.disabled = false;
                        teamMemberItem.classList.add('bg-blue-50', 'border-blue-300');
                        teamMemberItem.classList.remove('bg-gray-50', 'border-gray-200');
                    } else {
                        // Disable team head radio and salary input
                        teamHeadRadio.disabled = true;
                        teamHeadRadio.checked = false;
                        salaryInput.disabled = true;
                        salaryInput.value = '';
                        teamMemberItem.classList.remove('bg-blue-50', 'border-blue-300');
                        teamMemberItem.classList.add('bg-gray-50', 'border-gray-200');
                    }
                    
                    updateEditTeamSummary();
                });
            });

            // Handle team head radio changes
            editTeamHeadRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    console.log('Edit team head radio changed:', this.value, this.checked);
                    if (this.checked) {
                        // Uncheck other team head radios
                        editTeamHeadRadios.forEach(otherRadio => {
                            if (otherRadio !== this) {
                                otherRadio.checked = false;
                            }
                        });
                        
                        // Highlight the selected team head
                        const teamMemberItem = this.closest('.edit-team-member-item');
                        teamMemberItem.classList.add('bg-green-50', 'border-green-300');
                        teamMemberItem.classList.remove('bg-blue-50', 'border-blue-300');
                    }
                    
                    updateEditTeamSummary();
                });
            });

            // Handle individual salary input changes
            editIndividualSalaryInputs.forEach(input => {
                input.addEventListener('input', function() {
                    console.log('Edit salary input changed:', this.name, this.value);
                    updateEditTeamSummary();
                });
            });

            // Update edit team summary
            function updateEditTeamSummary() {
                const selectedMembers = Array.from(editTeamMemberCheckboxes).filter(cb => cb.checked);
                const selectedTeamHead = Array.from(editTeamHeadRadios).find(radio => radio.checked);
                let totalSalary = 0;

                // Count team members
                const memberCount = selectedMembers.length;
                editTeamMembersCount.textContent = memberCount;

                // Get team head name
                if (selectedTeamHead) {
                    const teamHeadLabel = document.querySelector(`label[for="edit_team_head_${selectedTeamHead.value}"]`);
                    editTeamHeadName.textContent = teamHeadLabel ? teamHeadLabel.textContent : 'Unknown';
                } else {
                    editTeamHeadName.textContent = '-';
                }

                // Calculate total salary
                selectedMembers.forEach(checkbox => {
                    const engineerId = checkbox.value;
                    const salaryInput = document.querySelector(`input[name="edit_individual_salaries[${engineerId}]"]`);
                    const salary = parseFloat(salaryInput.value) || 0;
                    totalSalary += salary;
                });

                // Update display
                editTotalSalaryDisplay.textContent = `₱${totalSalary.toFixed(2)}`;

                // Show/hide team summary
                if (memberCount > 0) {
                    editTeamSummary.classList.remove('hidden');
                } else {
                    editTeamSummary.classList.add('hidden');
                }

                // Validate form
                validateEditSalariesForm();
            }

            // Validate the edit form
            function validateEditSalariesForm() {
                const selectedMembers = Array.from(editTeamMemberCheckboxes).filter(cb => cb.checked);
                const selectedTeamHead = Array.from(editTeamHeadRadios).find(radio => radio.checked);
                const projectEngineer = document.getElementById('editProjectEngineer').value;
                const submitBtn = document.querySelector('#editSalariesForm button[type="submit"]');

                let isValid = true;
                let errorMessage = '';

                // Check if project engineer is selected
                if (!projectEngineer) {
                    isValid = false;
                    errorMessage = 'Please select a Project Engineer';
                }
                // Check if at least one team member is selected
                else if (selectedMembers.length === 0) {
                    isValid = false;
                    errorMessage = 'Please select at least one team member';
                }
                // Check if team head is selected
                else if (!selectedTeamHead) {
                    isValid = false;
                    errorMessage = 'Please select a team head';
                }
                // Check if all selected members have salaries
                else {
                    const hasEmptySalaries = selectedMembers.some(checkbox => {
                        const engineerId = checkbox.value;
                        const salaryInput = document.querySelector(`input[name="edit_individual_salaries[${engineerId}]"]`);
                        return !salaryInput.value || parseFloat(salaryInput.value) <= 0;
                    });

                    if (hasEmptySalaries) {
                        isValid = false;
                        errorMessage = 'Please enter salary amounts for all team members';
                    }
                }

                // Update submit button state
                if (submitBtn) {
                    submitBtn.disabled = !isValid;
                    if (!isValid) {
                        submitBtn.title = errorMessage;
                    } else {
                        submitBtn.title = '';
                    }
                }

                return isValid;
            }

            // Initialize validation
            validateEditSalariesForm();
        };

        // Handle edit salaries form submission
        document.addEventListener('DOMContentLoaded', function() {
            const editSalariesForm = document.getElementById('editSalariesForm');
            if (editSalariesForm) {
                editSalariesForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    console.log('Edit salaries form submission started');
                    
                    // Validate form before submission
                    if (!editSalariesForm.checkValidity()) {
                        console.log('Form validation failed');
                        editSalariesForm.reportValidity();
                        return;
                    }
                    
                    const submitBtn = editSalariesForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    try {
                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Updating...';
                        
                        const formData = new FormData(editSalariesForm);
                        
                        const response = await fetch('/monthly-assignments/update-salary', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            showCenteredNotification(data.message, 'success', 1000);
                            
                            // Close the salary edit modal
                            ModalManager.closeModal('editSalariesModal');
                            
                            // Get the project ID from the form
                            const projectId = formData.get('project_id');
                            
                            // Refresh the track record data
                            await refreshTrackRecord(projectId);
                            
                            // Small delay to ensure the UI updates smoothly
                            await new Promise(resolve => setTimeout(resolve, 500));
                        } else {
                            throw new Error(data.message || 'Failed to update salary assignment');
                        }
                    } catch (error) {
                        console.error('Error updating salary assignment:', error);
                        showCenteredNotification(error.message || 'An error occurred while updating salary assignment', 'error', 1000);
                    } finally {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }
        });
        }); // Close DOMContentLoaded event listener

        // Form submission
        if (projectForm) {
        projectForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const projectId = document.getElementById('projectId').value;
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            try {
                // Show loading spinner
                showButtonLoading(submitButton, projectId ? 'Updating...' : 'Creating...');
                
                let url = '/projects';
                let method = 'POST';
                
                if (projectId) {
                    url = `/projects/${projectId}`;
                    method = 'PUT';
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: 'POST', // Always POST for Laravel
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('Success:', data.message);

                    // Show centered success message
                    showCenteredNotification(data.message, 'success', 1000);

                    // Close modal and reload page
                    closeModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || 'Error saving project. Please try again.';
                    showCenteredNotification(errorMessage, 'error', 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification('Error saving project. Please try again.', 'error', 1000);
            } finally {
                // Hide loading spinner
                hideButtonLoading(submitButton, originalText);
            }
        });
        }

        // Add Expense Form submission
        if (addExpenseForm) {
        addExpenseForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;

            try {
                // Show loading spinner
                showButtonLoading(submitButton, 'Adding Expense...');
                
                const response = await fetch('/expenses', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('Expense added successfully!:', data);

                    // Show success message
                    showCenteredNotification('Expense added successfully!', 'success', 1000);

                    // Close modal and reset form
                    closeExpenseModal();
                    addExpenseForm.reset();

                    // Reload page to show updated project data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    let errorMessage = 'Error adding expense. Please try again.';

                    if (errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData.errors) {
                        errorMessage = Object.values(errorData.errors).flat().join(', ');
                    }

                    showCenteredNotification(errorMessage, 'error', 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification('Error adding expense. Please try again.', 'error', 1000);
            } finally {
                // Hide loading spinner
                hideButtonLoading(submitButton, originalText);
            }
        });
        }

        // Archive project function
        async function archiveProject(id, name) {
            console.log('archiveProject function called:', { id, name, isUserAdmin });

            if (!isUserAdmin) {
                showCenteredNotification('You do not have permission to archive projects.', 'error', 1000);
                return;
            }

            const archiveButton = document.querySelector(`[data-project-id="${id}"].archive-project-btn`);
            const originalText = archiveButton ? archiveButton.textContent : 'Archive';

            try {
                // Show loading spinner on archive button
                if (archiveButton) {
                    showButtonLoading(archiveButton, 'Archiving...');
                }
                
                const response = await fetch(`/projects/${id}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log('Success:', data.message);

                    // Show centered success message
                    showCenteredNotification(data.message, 'success', 1000);

                    // Reload page
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || 'Error archiving project. Please try again.';
                    showCenteredNotification(errorMessage, 'error', 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification('Error archiving project. Please try again.', 'error', 1000);
            } finally {
                // Hide loading spinner
                if (archiveButton) {
                    hideButtonLoading(archiveButton, originalText);
                }
            }
        }

        // Delete project function
        async function deleteProject(id, name, skipNotification = false) {
            console.log('deleteProject function called:', { id, name, isUserAdmin });

            if (!isUserAdmin) {
                showCenteredNotification('You do not have permission to delete projects.', 'error', 1000);
                return;
            }

            const deleteButton = document.querySelector(`[data-project-id="${id}"].delete-project-btn`);
            const originalText = deleteButton ? deleteButton.textContent : 'Delete';

            try {
                // Show loading spinner on delete button
                if (deleteButton) {
                    showButtonLoading(deleteButton, 'Deleting...');
                }
                
                const response = await fetch(`/projects/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: '_method=DELETE'
                }); 

                if (response.ok) {
                    const data = await response.json();
                    console.log('Success:', data.message);

                    // Only show notification if not called from delete selected
                    if (!skipNotification) {
                        showCenteredNotification(`${data.message}. You can restore it from Recently Deleted.`, 'error', 1000);
                        // Reload page after notification
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    const errorMessage = errorData.message || 'Error deleting project. Please try again.';
                    showCenteredNotification(errorMessage, 'error', 1000);
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification('Error deleting project. Please try again.', 'error', 1000);
            } finally {
                // Hide loading spinner
                if (deleteButton) {
                    hideButtonLoading(deleteButton, originalText);
                }
            }
        }

        // Search functionality
        const searchInput = document.getElementById('projectSearch');
        const projectCards = document.querySelectorAll('.project-card');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            projectCards.forEach(card => {
                const projectName = card.querySelector('h3').textContent.toLowerCase();
                const shouldShow = projectName.includes(searchTerm);

                if (shouldShow) {
                    card.style.display = 'block';
                    card.style.opacity = '1';
                } else {
                    card.style.display = 'none';
                    card.style.opacity = '0';
                }
            });

            // Show/hide empty state
            const visibleCards = Array.from(projectCards).filter(card => card.style.display !== 'none');
            const emptyState = document.querySelector('.col-span-full');

            if (visibleCards.length === 0 && searchTerm !== '') {
                // Show "no results" message
                if (!document.getElementById('noSearchResults')) {
                    const noResults = document.createElement('div');
                    noResults.id = 'noSearchResults';
                    noResults.className = 'col-span-full text-center py-12';
                    noResults.innerHTML = `
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-2xl font-bold text-white mb-2">No Projects Found</h3>
                        <p class="text-blue-200 mb-6">No projects match your search for "${searchTerm}".</p>
                    `;
                    document.getElementById('projectsGrid').appendChild(noResults);
                }
            } else {
                // Remove "no results" message
                const noResults = document.getElementById('noSearchResults');
                if (noResults) {
                    noResults.remove();
                }
            }
        });

        // Project Selection functionality
        const projectCheckboxes = document.querySelectorAll('.project-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const trackRecordBtn = document.getElementById('trackRecordBtn');
        const selectedCountSpan = document.getElementById('selectedCount');
        const trackRecordModal = document.getElementById('trackRecordModal');
        const closeTrackRecordBtn = document.getElementById('closeTrackRecordModal');

        // Update selected count and checkbox state
        function updateSelectionState() {
            const selectedCheckboxes = document.querySelectorAll('.project-checkbox:checked');
            const count = selectedCheckboxes.length;
            const totalCheckboxes = projectCheckboxes.length;

            selectedCountSpan.textContent = count;
            if (deleteSelectedCountSpan) {
                deleteSelectedCountSpan.textContent = count;
            }
            trackRecordBtn.disabled = count === 0;
            if (deleteSelectedBtn) {
                deleteSelectedBtn.disabled = count === 0;
            }

            // Update select all checkbox state
            if (count === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
                trackRecordBtn.classList.add('opacity-50', 'cursor-not-allowed');
                if (deleteSelectedBtn) {
                    deleteSelectedBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else if (count === totalCheckboxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
                trackRecordBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                if (deleteSelectedBtn) {
                    deleteSelectedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true; // Show indeterminate state for partial selection
                trackRecordBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                if (deleteSelectedBtn) {
                    deleteSelectedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // Handle individual checkbox changes
        projectCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectionState);
        });

        // Select All Checkbox functionality
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;

            // Set all project checkboxes to match the select all checkbox
            projectCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            updateSelectionState();
        });


        // Global variable to store currently selected projects
        let currentSelectedProjects = [];

        // Track Record button functionality
        trackRecordBtn.addEventListener('click', async function() {
            const selectedCheckboxes = document.querySelectorAll('.project-checkbox:checked');
            if (selectedCheckboxes.length === 0) return;

            currentSelectedProjects = Array.from(selectedCheckboxes).map(cb => {
                // Find the project card that contains this checkbox
                const projectCard = cb.closest('.project-card');
                let engineerInfo = null;
                
                // Try to get engineer info from the project card if available
                if (projectCard) {
                    const engineerElement = projectCard.querySelector('.engineer-name');
                    if (engineerElement) {
                        engineerInfo = {
                            name: engineerElement.textContent.trim(),
                            specialization: engineerElement.nextElementSibling?.classList.contains('text-gray-300') ? 
                                engineerElement.nextElementSibling.textContent.replace(/[()]/g, '').trim() : null
                        };
                    }
                }
                
                return {
                    id: cb.dataset.projectId,
                    name: cb.dataset.projectName,
                    engineer: engineerInfo
                };
            });

            await openMultipleTrackRecordModal(currentSelectedProjects);
        });

        // Delete Selected button functionality (Admin only)
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const deleteSelectedCountSpan = document.getElementById('deleteSelectedCount');

        if (deleteSelectedBtn) {
            deleteSelectedBtn.addEventListener('click', async function() {
            const selectedCheckboxes = document.querySelectorAll('.project-checkbox:checked');
            if (selectedCheckboxes.length === 0) return;

            const selectedProjects = Array.from(selectedCheckboxes).map(cb => ({
                id: cb.dataset.projectId,
                name: cb.dataset.projectName
            }));

            // Show confirmation dialog
            const projectNames = selectedProjects.map(p => p.name).join(', ');
            const confirmMessage = `Are you sure you want to delete the following project${selectedProjects.length > 1 ? 's' : ''}?\n\n${projectNames}\n\nThis action cannot be undone and will permanently delete all associated data including expenses, team assignments, and project records.`;

            if (confirm(confirmMessage)) {
                try {
                    // Show loading state
                    deleteSelectedBtn.disabled = true;
                    deleteSelectedBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Deleting...';

                    // Delete each project
                    for (const project of selectedProjects) {
                        await deleteProject(project.id, project.name, true); // Skip individual notifications
                    }

                    // Show success message with longer display time
                    showCenteredNotification(`Successfully deleted ${selectedProjects.length} project${selectedProjects.length > 1 ? 's' : ''}`, 'success', 1000);

                    // Reload the page to refresh the project list after the notification has been shown
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);

                } catch (error) {
                    console.error('Error deleting projects:', error);
                    showCenteredNotification('An error occurred while deleting projects. Please try again.', 'error', 1000);
                } finally {
                    // Reset button state
                    deleteSelectedBtn.disabled = false;
                    deleteSelectedBtn.innerHTML = `Delete Selected (<span id="deleteSelectedCount">${selectedProjects.length}</span>)`;
                }
            }
        });
        }

        // Close track record modal
        closeTrackRecordBtn.addEventListener('click', function() {
            // Check if we're in edit mode and prompt to save
            const editForm = document.querySelector('.project-edit-form');
            if (editForm && confirm('You have unsaved changes. Are you sure you want to cancel?')) {
                // If confirmed, reset the form and close the modal
                const projectCard = editForm.closest('.project-card');
                if (projectCard) {
                    projectCard.querySelector('.project-details').classList.remove('hidden');
                    projectCard.querySelector('.project-edit-form').remove();
                }
                ModalManager.closeModal('trackRecordModal');
            } else if (!editForm) {
                ModalManager.closeModal('trackRecordModal');
            }
        });

        // ESC handler removed - using centralized modal manager

        // Edit Expense Modal functionality
        const editExpenseModal = document.getElementById('editExpenseModal');
        const closeEditExpenseBtn = document.getElementById('closeEditExpenseModal');
        const cancelEditExpenseBtn = document.getElementById('cancelEditExpense');
        const editExpenseForm = document.getElementById('editExpenseForm');

        // Close edit expense modal (only for admins)
        if (closeEditExpenseBtn) {
            closeEditExpenseBtn.addEventListener('click', function() {
                ModalManager.closeModal('editExpenseModal');
            });
        }

        if (cancelEditExpenseBtn) {
            cancelEditExpenseBtn.addEventListener('click', function() {
                ModalManager.closeModal('editExpenseModal');
            });
        }

        // Handle edit expense form submission (only for admins)
        if (editExpenseForm) {
            editExpenseForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const expenseId = document.getElementById('editExpenseId').value;
            const description = document.getElementById('editExpenseDescription').value.trim();
            const amount = parseFloat(document.getElementById('editExpenseAmount').value);
            const date = document.getElementById('editExpenseDate').value;
            const projectId = this.dataset.projectId;

            // Validate inputs
            if (!description) {
                showCenteredNotification('Description cannot be empty', 'error', 1000);
                return;
            }

            if (isNaN(amount) || amount <= 0) {
                showCenteredNotification('Please enter a valid amount', 'error', 1000);
                return;
            }

            if (!date) {
                showCenteredNotification('Please select a date', 'error', 1000);
                return;
            }

            if (!projectId) {
                showCenteredNotification('Project ID is missing', 'error', 1000);
                return;
            }

            // Update expense via API
            await updateExpense(expenseId, description, amount, date, projectId);

            // Close modal
            ModalManager.closeModal('editExpenseModal');
            });
        }

        // Open multiple projects track record modal
        async function openMultipleTrackRecordModal(selectedProjects) {
            console.log('openMultipleTrackRecordModal called with:', selectedProjects);
            
            // Store selected projects globally for refresh purposes
            window.currentSelectedProjects = selectedProjects;
            try {
                // Update modal title
                const projectNames = selectedProjects.length === 1
                    ? selectedProjects[0].name
                    : `${selectedProjects.length} Projects`;

                document.getElementById('trackRecordTitle').innerHTML = `
                    ${projectNames} - Track Records
                `;

                // Update selected projects info
                document.getElementById('selectedProjectsInfo').textContent =
                    `Selected Projects: ${selectedProjects.length}`;

                // Show modal using modal manager
                ModalManager.openModal('trackRecordModal');

                // Show loading spinner while fetching data
                const modalContent = document.getElementById('projectsContainer');
                showOverlayLoading(modalContent, `Loading track records for ${selectedProjects.length} project${selectedProjects.length > 1 ? 's' : ''}...`);

                // Fetch data for all selected projects
                const projectsData = await Promise.all(
                    selectedProjects.map(async (project) => {
                        console.log(`Fetching track record for project: ${project.name} (ID: ${project.id})`);
                        const response = await fetch(`/projects/${project.id}/track-record?t=${Date.now()}&refresh=true`);
                        const data = await response.json();
                        console.log(`Track record data for ${project.name}:`, data);
                        
                        // Preserve the original project data including engineer info
                        const projectData = {
                            ...data,
                            project: {
                                ...data.project,
                                // Use project_engineer if available, otherwise fall back to existing engineer data
                                engineer: data.project && data.project.project_engineer 
                                    ? data.project.project_engineer 
                                    : (project.engineer || (data.project && data.project.engineer) || null),
                                fpp_code: data.project ? (data.project.fpp_code || '') : ''
                            },
                            projectName: project.name
                        };
                        
                        console.log('Project data for', project.name, ':', projectData);
                        
                        return projectData;
                    })
                );

                // Hide loading spinner
                hideOverlayLoading(modalContent);

                // Generate project cards

                // Display projects container
                let projectsHtml = '';

                projectsData.forEach((data, index) => {
                    const project = data.project || {};
                    const expenses = data.expenses || [];
                    const summary = data.summary || {};
                    const engineer = project.engineer || null;
                    const projectId = project.id || data.id; // Get project ID from either project object or data

                    projectsHtml += `
                        <div class="bg-gradient-to-r from-blue-100 to-indigo-100 rounded-xl p-6 mb-6 border-2 border-blue-300 shadow-lg relative project-card" data-project-id="${projectId}">
                            <!-- Project name with edit button in corner -->
                            <div class="relative mb-4">
                                <h3 class="text-xl font-bold text-gray-900 pr-8 break-words">${data.projectName || 'Unnamed Project'}</h3>
                                <!-- Edit button -->
                                <button class="absolute top-0 right-0 p-1 text-gray-500 hover:text-blue-600 transition-colors" 
                                        onclick="editProjectDetails(this, '${projectId}')"
                                        title="Edit Project">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="project-details">
                                <div class="fpp-code mb-2">
                                    ${project.fpp_code ? `<p class="text-sm text-gray-700 font-medium">F/P/P Code: <span class="fpp-code-value">${project.fpp_code}</span></p>` : ''}
                                </div>
                                <div class="project-budget mb-2">
                                    <p class="text-sm text-gray-700 font-medium">Budget: <span class="budget-value">₱${summary.total_budget ? summary.total_budget.toLocaleString() : '0'}</span></p>
                                </div>
                                <div class="project-engineer">
                                    ${engineer ? `
                                        <p class="text-sm text-gray-700 font-medium mt-1 mb-2">
                                            <span class="font-semibold">Engineer:</span> 
                                            <span class="engineer-name">${engineer.name || 'N/A'}</span>
                                            ${engineer.specialization ? `<span class="text-gray-500 ml-1 engineer-specialization">(${engineer.specialization})</span>` : ''}
                                        </p>
                                    ` : '<p class="text-sm text-gray-500 font-medium mb-2">No engineer assigned</p>'}
                                </div>
                            </div>
                    
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                <div class="text-center bg-green-50 border border-green-200 rounded-lg p-4 shadow-sm">
                                    <div class="text-lg font-bold text-green-700">₱${summary.total_budget.toLocaleString()}</div>
                                    <div class="text-xs text-green-600 font-medium">Budget</div>
                                </div>
                                <div class="text-center bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm">
                                    <div class="text-lg font-bold text-red-700">₱${summary.total_spent.toLocaleString()}</div>
                                    <div class="text-xs text-red-600 font-medium">Spent</div>
                                </div>
                                <div class="text-center ${summary.remaining_budget >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg p-4 shadow-sm">
                                    <div class="text-lg font-bold ${summary.remaining_budget >= 0 ? 'text-green-700' : 'text-red-700'}">₱${summary.remaining_budget.toLocaleString()}</div>
                                    <div class="text-xs ${summary.remaining_budget >= 0 ? 'text-green-600' : 'text-red-600'} font-medium">Remaining</div>
                                </div>
                                <div class="text-center bg-purple-50 border border-purple-200 rounded-lg p-4 shadow-sm">
                                    <div class="text-lg font-bold text-purple-700">${summary.percent_used.toFixed(1)}%</div>
                                    <div class="text-xs text-purple-600 font-medium">Used</div>
                                </div>
                            </div>
                        
                            <!-- Expenses Table -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 border-b border-gray-200 pb-2">Expenses (${expenses.length} items)</h4>
                                ${expenses.length > 0 ? `
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full">
                                            <thead class="bg-blue-50 border-b-2 border-blue-200">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date</th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Description</th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Amount</th>
                                                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-300">
                                                ${expenses.map(expense => {
                                                    const escapedDescription = expense.description.replace(/'/g, "\\'").replace(/"/g, '\\"');
                                                    const isVirtual = expense.is_virtual || false;
                                                    const isDetailedEngineering = expense.description === 'Detailed Engineering';
                                                    const rowClass = isVirtual ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-blue-50';
                                                    const descriptionClass = isVirtual ? 'text-yellow-800 font-semibold' : 'text-gray-900';

                                                    return `
                                                    <tr class="${rowClass} transition-colors duration-150">
                                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">${new Date(expense.date).toLocaleDateString()}</td>
                                                        <td class="px-4 py-3 text-sm ${descriptionClass}">
                                                            ${expense.description}
                                                            ${isVirtual ? '<span class="ml-2 text-xs text-yellow-800 px-2 py-1 rounded-full"></span>' : ''}
                                                        </td>
                                                        <td class="px-4 py-3 text-sm font-bold text-red-700">₱${parseFloat(expense.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                        <td class="px-4 py-3 text-sm">
                                                            ${isUserAdmin ? `
                                                                ${isDetailedEngineering ? `
                                                                    <button 
                                                                        onclick="openEditDetailedEngineeringModal(${project.id}, '${project.name.replace(/'/g, "\\'").replace(/"/g, '\\"')}')"
                                                                        class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md mr-2 font-medium text-xs transition-colors duration-150"
                                                                    >
                                                                        Edit
                                                                    </button>
                                                                ` : `
                                                                    <button class="edit-expense-btn bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md mr-2 font-medium text-xs transition-colors duration-150"
                                                                        data-expense-id="${isVirtual ? '' : expense.id}"
                                                                        data-description="${escapedDescription}"
                                                                        data-amount="${isVirtual ? '0' : expense.amount}"
                                                                        data-date="${expense.date || new Date().toISOString().split('T')[0]}"
                                                                        data-project-id="${project.id}"
                                                                        data-is-virtual="${isVirtual ? 'true' : 'false'}">
                                                                        ${isVirtual ? 'Add' : 'Edit'}
                                                                    </button>
                                                                    ${!isVirtual ? `
                                                                    <button class="delete-expense-btn bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md font-medium text-xs transition-colors duration-150"
                                                                        data-expense-id="${expense.id}"
                                                                        data-description="${escapedDescription}">
                                                                        Delete
                                                                    </button>` : ''}
                                                                `}
                                                            ` : '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-md text-xs font-medium">View Only</span>'}
                                                        </td>
                                                    </tr>
                                                    `;
                                                }).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                ` : `
                                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                        <div class="text-6xl mb-4">📝</div>
                                        <p class="text-gray-600 font-medium text-lg">No expenses recorded for this project</p>
                                        <p class="text-gray-500 text-sm mt-2">Expenses will appear here once they are added</p>
                                    </div>
                                `}
                            </div>
                        </div>
                    `;
                });

                document.getElementById('projectsContainer').innerHTML = projectsHtml;
                
                // Add click handlers for edit buttons
                document.querySelectorAll('.edit-project-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const projectId = this.dataset.projectId;
                        editProjectDetails(this, projectId);
                    });
                });
                
                // Load engineers for the project engineer dropdown
                loadEngineersForProjectEdit();

// Make the function globally available
window.editProjectDetails = async function(button, projectId) {
    console.log('editProjectDetails called with projectId:', projectId, 'button:', button);
    console.log('Window object:', window);
    console.log('editProjectDetails function exists:', typeof window.editProjectDetails);
    const projectCard = button.closest('.project-card');
    if (!projectCard) {
        console.error('Could not find project card');
        return;
    }
    
    // Get project data with better error handling
    const projectNameElement = projectCard.querySelector('.project-name h3, .project-name');
    const fppCodeElement = projectCard.querySelector('.fpp-code-value, [data-field="fpp_code"]');
    const budgetElement = projectCard.querySelector('.budget-value, [data-field="budget"]');
    const engineerNameElement = projectCard.querySelector('.engineer-name, [data-field="engineer_name"]');
    const engineerSpecializationElement = projectCard.querySelector('.engineer-specialization, [data-field="engineer_specialization"]');
    
    const projectName = projectNameElement ? projectNameElement.textContent.trim() : '';
    const fppCode = fppCodeElement ? fppCodeElement.textContent.trim() : '';
    const budget = budgetElement ? budgetElement.textContent.replace(/[^0-9.]/g, '') : '0';
    const engineerName = engineerNameElement ? engineerNameElement.textContent.trim() : '';
    const engineerSpecialization = engineerSpecializationElement ? 
        engineerSpecializationElement.textContent.replace(/[()]/g, '').trim() : '';
        
    console.log('Project data:', { projectName, fppCode, budget, engineerName, engineerSpecialization });
    
    // Create edit form HTML
    const editForm = document.createElement('div');
    editForm.className = 'project-edit-form bg-white p-6 rounded-lg border border-blue-200';
    editForm.innerHTML = `
        <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Project Details</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                <input type="text" class="project-name-input w-full px-3 py-2 border border-gray-300 rounded-md" 
                       value="${projectName.replace(/"/g, '&quot;')}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">F/P/P Code</label>
                <input type="text" class="fpp-code-input w-full px-3 py-2 border border-gray-300 rounded-md" 
                       value="${fppCode.replace(/"/g, '&quot;')}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Budget (₱)</label>
                <input type="number" step="0.01" min="0" 
                       class="budget-input w-full px-3 py-2 border border-gray-300 rounded-md" 
                       value="${parseFloat(budget).toFixed(2)}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Engineer</label>
                <select class="engineer-select w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Select Engineer</option>
                    <!-- Will be populated by loadEngineersForProjectEdit() -->
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" class="cancel-edit-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
                <button type="button" class="save-edit-btn px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </div>
    `;
    
    // Hide the details and show the form
    projectCard.querySelector('.project-details').classList.add('hidden');
    projectCard.insertBefore(editForm, projectCard.querySelector('.project-details'));
    
    // Load engineers for the dropdown
    await loadEngineersForProjectEdit(projectId);
    
    // Set the selected engineer if exists
    if (engineerName) {
        const engineerSelect = editForm.querySelector('.engineer-select');
        if (engineerSelect) {
            const options = Array.from(engineerSelect.options);
            const engineerOption = options.find(option => 
                option.text.includes(engineerName) || 
                (engineerSpecialization && option.text.includes(engineerSpecialization))
            );
            if (engineerOption) engineerOption.selected = true;
        }
    }
    
    // Add event listeners
    editForm.querySelector('.cancel-edit-btn').addEventListener('click', () => {
        projectCard.querySelector('.project-details').classList.remove('hidden');
        editForm.remove();
    });
    
    const saveButton = editForm.querySelector('.save-edit-btn');
    saveButton.addEventListener('click', async () => {
        // Disable button and show loading state
        const originalButtonText = saveButton.innerHTML;
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        
        try {
            await saveProjectDetails(projectId, projectCard, editForm);
            
            // If save is successful, close the edit form and show success message
            projectCard.querySelector('.project-details').classList.remove('hidden');
            editForm.remove();
            showCenteredNotification('Project updated successfully!', 'success', 1000);
            
            // Refresh track records if the modal is open
            const trackRecordModal = document.getElementById('trackRecordModal');
            if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                const projectId = projectCard.getAttribute('data-project-id');
                if (window.currentSelectedProjects && window.currentSelectedProjects.length > 0) {
                    openMultipleTrackRecordModal(window.currentSelectedProjects);
                } else if (window.openTrackRecordModal) {
                    window.openTrackRecordModal(projectId);
                } else if (window.refreshTrackRecord) {
                    window.refreshTrackRecord(projectId);
                }
            }
            
        } catch (error) {
            console.error('Error saving project details:', error);
            // Re-enable button and show error
            saveButton.disabled = false;
            saveButton.innerHTML = originalButtonText;
            showCenteredNotification(error.message || 'Failed to save changes. Please try again.', 'error', 3000);
        }
    });
}

// Load engineers for the project edit form
async function loadEngineersForProjectEdit(projectId = null) {
    try {
        const response = await fetch('/api/engineers');
        if (!response.ok) throw new Error('Failed to load engineers');
        
        const engineers = await response.json();
        const engineerSelects = document.querySelectorAll('.engineer-select');
        
        engineerSelects.forEach(select => {
            // Save current selection
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select Engineer</option>';
            
            engineers.forEach(engineer => {
                const option = document.createElement('option');
                option.value = engineer.id;
                option.textContent = `${engineer.name}${engineer.specialization ? ` (${engineer.specialization})` : ''}`;
                select.appendChild(option);
            });
            
            // Restore selection if it exists
            if (currentValue) {
                select.value = currentValue;
            } else if (projectId) {
                // Try to select the current project's engineer if available
                const currentEngineer = document.querySelector(`[data-project-id="${projectId}"] .engineer-name`);
                if (currentEngineer) {
                    const engineerName = currentEngineer.textContent.trim();
                    const options = Array.from(select.options);
                    const matchingOption = options.find(opt => opt.text.includes(engineerName));
                    if (matchingOption) matchingOption.selected = true;
                }
            }
        });
    } catch (error) {
        console.error('Error loading engineers:', error);
        showCenteredNotification('Failed to load engineers', 'error', 1000);
    }
}

// Save project details
async function saveProjectDetails(projectId, projectCard, editForm) {
    const projectName = editForm.querySelector('.project-name-input').value.trim();
    const fppCode = editForm.querySelector('.fpp-code-input').value.trim();
    const budget = parseFloat(editForm.querySelector('.budget-input').value) || 0;
    const engineerId = editForm.querySelector('.engineer-select').value;
    
    // Check if track record modal is open and for this project
    const trackRecordModal = document.getElementById('trackRecordModal');
    const isTrackRecordOpen = trackRecordModal && !trackRecordModal.classList.contains('hidden');
    const trackRecordProjectId = isTrackRecordOpen ? trackRecordModal.getAttribute('data-project-id') : null;
    
    if (!projectName) {
        showCenteredNotification('Project name is required', 'error', 1000);
        return;
    }
    
    // Show loading state
    const saveButton = editForm.querySelector('.save-edit-btn');
    const originalButtonText = saveButton.innerHTML;
    saveButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;
    saveButton.disabled = true;
    
    try {
        const response = await fetch(`/projects/${projectId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: projectName,
                fpp_code: fppCode,
                budget: budget,
                project_engineer_id: engineerId || null
            })
        });
        
        if (!response.ok) {
            let errorMessage = 'Failed to update project';
            try {
                const errorData = await response.json();
                console.error('Server error response:', errorData);
                
                // Handle validation errors
                if (errorData.errors) {
                    errorMessage = Object.values(errorData.errors).flat().join('\n');
                } else if (errorData.message) {
                    errorMessage = errorData.message;
                }
            } catch (e) {
                console.error('Error parsing error response:', e);
            }
            throw new Error(errorMessage);
        }
        
        const result = await response.json();
        
        // Update the UI
        projectCard.querySelector('.project-name h3').textContent = projectName;
        
        const fppCodeElement = projectCard.querySelector('.fpp-code-value');
        if (fppCodeElement) {
            fppCodeElement.textContent = fppCode;
        } else if (fppCode) {
            const fppCodeContainer = projectCard.querySelector('.fpp-code');
            if (fppCodeContainer) {
                fppCodeContainer.innerHTML = `<p class="text-sm text-gray-700 font-medium">F/P/P Code: <span class="fpp-code-value">${fppCode}</span></p>`;
            }
        }
        
        projectCard.querySelector('.budget-value').textContent = budget.toLocaleString();
        
        // Update engineer info
        const engineerSelect = editForm.querySelector('.engineer-select');
        const selectedOption = engineerSelect.options[engineerSelect.selectedIndex];
        const engineerName = selectedOption ? selectedOption.text.split(' (')[0] : '';
        const engineerSpecialization = selectedOption && selectedOption.text.includes('(') 
            ? selectedOption.text.match(/\(([^)]+)\)/)[1] 
            : '';
            
        const engineerContainer = projectCard.querySelector('.project-engineer');
        if (engineerContainer) {
            engineerContainer.innerHTML = `
                <p class="text-sm text-gray-700 font-medium mt-1">
                    <span class="font-semibold">Engineer:</span> 
                    <span class="engineer-name">${engineerName || 'N/A'}</span>
                    ${engineerSpecialization ? `<span class="text-gray-500 ml-1 engineer-specialization">(${engineerSpecialization})</span>` : ''}
                </p>
            `;
        }
        
        // Update the budget display in the summary cards
        const budgetCard = projectCard.querySelector('.bg-green-50 .text-lg');
        if (budgetCard) {
            budgetCard.textContent = `₱${budget.toLocaleString()}`;
        }
        
        // Show success message and close the form
        showCenteredNotification('Project updated successfully', 'success', 1000);
        projectCard.querySelector('.project-details').classList.remove('hidden');
        editForm.remove();
        
        // Update the current selected projects data if it exists
        if (window.currentSelectedProjects) {
            const projectIndex = window.currentSelectedProjects.findIndex(p => p.id == projectId);
            if (projectIndex !== -1) {
                window.currentSelectedProjects[projectIndex].name = projectName;
                if (!window.currentSelectedProjects[projectIndex].engineer && engineerName) {
                    window.currentSelectedProjects[projectIndex].engineer = {
                        name: engineerName,
                        specialization: engineerSpecialization
                    };
                }
            }
        }
        
    } catch (error) {
        console.error('Error updating project:', error);
        showCenteredNotification(error.message || 'Failed to update project', 'error', 1000);
    }
}

// Re-bind Edit and Delete expense buttons after dynamic HTML injection
setTimeout(() => {
    // Use event delegation for dynamically added edit/delete buttons
    document.addEventListener('click', function(e) {
        // Handle edit button clicks
        const editBtn = e.target.closest('.edit-expense-btn');
        if (editBtn) {
            e.preventDefault();
            console.log('Edit button clicked', editBtn.dataset);
            const expenseId = editBtn.dataset.expenseId;
            const description = editBtn.dataset.description;
            const amount = editBtn.dataset.amount;
            const date = editBtn.dataset.date;
            const projectId = editBtn.dataset.projectId;
            const isVirtual = editBtn.dataset.isVirtual === 'true';
            
            console.log('Calling editExpense with:', { expenseId, description, amount, date, projectId, isVirtual });
            window.editExpense(expenseId, description, amount, date, projectId, isVirtual);
            return;
        }
        
        // Handle delete button clicks
        const deleteBtn = e.target.closest('.delete-expense-btn');
        if (deleteBtn) {
            e.preventDefault();
            const expenseId = deleteBtn.dataset.expenseId;
            const description = deleteBtn.dataset.description;
            deleteExpense(expenseId, description);
            return;
        }
    });
    
    console.log('Expense Edit/Delete event listeners rebound');
}, 0);

// Ensure editExpense is globally available
window.editExpense = function(expenseId, description, amount, date, projectId, isVirtual) {
    console.log('Setting up edit form with:', { expenseId, description, amount, date, projectId, isVirtual });
    
    // Check if this is a Detailed Engineering expense
    if (description === 'Detailed Engineering') {
        console.log('Detailed Engineering expense detected, opening team modal');
        console.log('Project ID:', projectId);
        
        // Find the project name from the current track record data
        let projectName = 'Unknown Project';
        const projectCard = document.querySelector('.bg-gradient-to-r.from-blue-100.to-indigo-100 h3');
        if (projectCard) {
            projectName = projectCard.textContent.trim();
            console.log('Found project name:', projectName);
        } else {
            console.log('Could not find project card, using default name');
        }
        
        // Open the detailed engineering modal instead
        openEditDetailedEngineeringModal(projectId, projectName);
        return;
    }
    
    // For virtual expenses (not yet added), we need to create a new expense
    if (isVirtual === 'true' || isVirtual === true) {
        console.log('Virtual expense detected, setting up form for new expense');
        document.getElementById('editExpenseId').value = ''; // Empty ID for new expense
        document.getElementById('editExpenseDescription').value = description; // Pre-fill the description
        document.getElementById('editExpenseAmount').value = ''; // Clear amount for new entry
        document.getElementById('editExpenseDate').value = new Date().toISOString().split('T')[0]; // Today's date
        document.getElementById('editExpenseForm').dataset.projectId = projectId;
        
        // Update the form title to indicate we're adding a new expense
        const modalTitle = document.querySelector('#editExpenseModal h2');
        if (modalTitle) {
            modalTitle.innerHTML = `<span class="text-2xl">➕</span> Add ${description} Expense`;
        }
    } else {
        // Regular expense editing for existing expenses
        console.log('Editing existing expense');
        document.getElementById('editExpenseId').value = expenseId;
        document.getElementById('editExpenseDescription').value = description;
        document.getElementById('editExpenseAmount').value = amount;
        
        // Set the hidden date field value
        const dateInput = document.getElementById('editExpenseDate');
        dateInput.value = date;
        // Store the original date in a data attribute in case we need it
        dateInput.dataset.originalDate = date;
        document.getElementById('editExpenseForm').dataset.projectId = projectId;
        
        // Update the form title to indicate we're editing
        const modalTitle = document.querySelector('#editExpenseModal h2');
        if (modalTitle) {
            modalTitle.innerHTML = `<span class="text-2xl">✏️</span> Edit ${description} Expense`;
        }
    }

    // Open the modal
    ModalManager.openModal('editExpenseModal');
};

// Edit Expense Modal functionality
const editExpenseModal = document.getElementById('editExpenseModal');
const closeEditExpenseBtn = document.getElementById('closeEditExpenseModal');
const cancelEditExpenseBtn = document.getElementById('cancelEditExpenseBtn');
const editExpenseForm = document.getElementById('editExpenseForm');

function closeEditExpenseModalAndRestore() {
    const editModal = document.getElementById('editExpenseModal');
    if (editModal) {
        editModal.classList.add('hidden');
        // Remove from stack
        const index = ModalManager.modalStack.indexOf('editExpenseModal');
        if (index > -1) {
            ModalManager.modalStack.splice(index, 1);
            console.log('Modal closed with stack:', ModalManager.modalStack);
        }
    }
    editExpenseForm.reset();
}
if (closeEditExpenseBtn) closeEditExpenseBtn.addEventListener('click', closeEditExpenseModalAndRestore);
if (cancelEditExpenseBtn) cancelEditExpenseBtn.addEventListener('click', closeEditExpenseModalAndRestore);

// Submit handler for Edit Expense
if (editExpenseForm) {
    editExpenseForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const expenseId = document.getElementById('editExpenseId').value;
        const description = document.getElementById('editExpenseDescription').value;
        const amount = document.getElementById('editExpenseAmount').value;
        const date = document.getElementById('editExpenseDate').value;
        const projectId = editExpenseForm.dataset.projectId;
        const submitBtn = editExpenseForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="animate-spin mr-2"></span>Updating...';
        updateExpense(expenseId, description, amount, date, projectId)
            .then((result) => {
                closeEditExpenseModalAndRestore();
                
                // Check if track record modal is open and refresh it
                const trackRecordModal = document.getElementById('trackRecordModal');
                if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                    // Use existing refresh mechanism if available
                    if (window.currentSelectedProjects && window.currentSelectedProjects.length > 0) {
                        openMultipleTrackRecordModal(window.currentSelectedProjects);
                    } else {
                        const projectId = trackRecordModal.getAttribute('data-project-id');
                        if (projectId && window.openTrackRecordModal) {
                            window.openTrackRecordModal(projectId);
                        }
                    }
                }
                
                return result;
            })
            .catch(error => {
                console.error('Error updating expense:', error);
                showCenteredNotification('Failed to update expense. Please try again.', 'error', 1000);
                throw error;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });
}

                                 // Setup action buttons
                const printAllBtn = document.getElementById('printAllBtn');
                const showReceiptsBtn = document.getElementById('showReceiptsBtn');

                printAllBtn.onclick = function() {
                    printAllProjects(currentSelectedProjects);
                };

                showReceiptsBtn.onclick = function() {
                    openReceiptModal(null, true, currentSelectedProjects.map(p => p.id));
                };

                
                

            } catch (error) {
                console.error('Error loading track records:', error);
                
                // Hide loading spinner in case of error
                const modalContent = document.getElementById('projectsContainer');
                hideOverlayLoading(modalContent);
                
                showCenteredNotification('Error loading project track records. Please try again.', 'error', 1000);
                ModalManager.closeModal('trackRecordModal');
            }
        }



        // Print all selected projects
        function printAllProjects(selectedProjects) {
            if (selectedProjects.length === 0) return;

            if (selectedProjects.length === 1) {
                // Single project - open in modal and trigger print
                openReceiptModal(selectedProjects[0].id);
                // Wait a bit for modal to load, then trigger print
                setTimeout(() => {
                    printReceipt();
                }, 1000);
                showCenteredNotification(`Preparing to print receipt for ${selectedProjects[0].name}...`, 'info', 1000);
            } else {
                // Multiple projects - open combined receipt in modal and trigger print
                const projectIds = selectedProjects.map(p => p.id);
                openReceiptModal(null, true, projectIds);
                // Wait a bit for modal to load, then trigger print
                setTimeout(() => {
                    printReceipt();
                }, 1000);
                showCenteredNotification(`Preparing to print combined receipt for ${selectedProjects.length} projects...`, 'info', 1000);
            }
        }

        // View all receipts
        function viewAllReceipts(selectedProjects) {
            if (selectedProjects.length === 0) return;

            if (selectedProjects.length === 1) {
                // Single project - open in modal
                openReceiptModal(selectedProjects[0].id);
                showCenteredNotification(`Opening receipt for ${selectedProjects[0].name}...`, 'info', 1000);
            } else {
                // Multiple projects - open combined receipt in modal
                const projectIds = selectedProjects.map(p => p.id);
                openReceiptModal(null, true, projectIds);
                showCenteredNotification(`Opening combined receipt for ${selectedProjects.length} projects...`, 'info', 1000);
            }
        }

        // Edit expense function
        function editExpense(expenseId, description, amount, date, projectId) {
            // Populate the edit form
            document.getElementById('editExpenseId').value = expenseId;
            document.getElementById('editExpenseDescription').value = description;
            document.getElementById('editExpenseAmount').value = amount;
            document.getElementById('editExpenseDate').value = date;

            // Store project ID for the update
            document.getElementById('editExpenseForm').dataset.projectId = projectId;

            // Show the edit modal without closing the track record modal
            const editModal = document.getElementById('editExpenseModal');
            if (editModal) {
                editModal.classList.remove('hidden');
                // Add to stack if not already there
                if (!ModalManager.modalStack.includes('editExpenseModal')) {
                    ModalManager.modalStack.push('editExpenseModal');
                    console.log('Modal opened with stack:', ModalManager.modalStack);
                }
            }
        }

        // Delete expense function
        function deleteExpense(expenseId, description) {
            if (confirm(`Are you sure you want to delete the expense "${description}"?`)) {
                deleteExpenseFromAPI(expenseId);
            }
        }

        // Track if a notification is currently being shown
        let isNotificationActive = false;

        // Refresh track record data via AJAX
        async function refreshTrackRecord(projectId) {
            try {
                const response = await fetch(`/api/projects/${projectId}/fresh-track-record`);
                const data = await response.json();
                
                if (data.success) {
                    // Update the detailed engineering cost display
                    const engineeringCostElement = document.querySelector('[data-engineering-cost]');
                    if (engineeringCostElement) {
                        engineeringCostElement.textContent = `₱${parseFloat(data.detailed_engineering_cost).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    }
                    
                    // Update the total spent display
                    const totalSpentElement = document.querySelector('[data-total-spent]');
                    if (totalSpentElement) {
                        totalSpentElement.textContent = `₱${parseFloat(data.total_spent_with_engineering).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    }
                    
                    // Update the remaining budget display
                    const remainingBudgetElement = document.querySelector('[data-remaining-budget]');
                    if (remainingBudgetElement) {
                        remainingBudgetElement.textContent = `₱${parseFloat(data.remaining_budget).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    }
                    
                    return true;
                }
                return false;
            } catch (error) {
                console.error('Error refreshing track record:', error);
                return false;
            }
        }

        // Update expense API call - handles both regular and virtual (auto-generated) expenses
        async function updateExpense(expenseId, description, amount, date, projectId, isVirtual = false) {
            // Prevent multiple notifications
            if (isNotificationActive) return;
            
            // Show loading state
            const loadingElement = document.querySelector(`[data-expense-id="${expenseId}"]`);
            if (loadingElement) {
                loadingElement.classList.add('opacity-50', 'pointer-events-none');
            }
            
            try {
                isNotificationActive = true;
                console.log('Updating expense:', { expenseId, description, amount, date, projectId, isVirtual });

                // Prepare the request data
                const requestData = {
                    project_id: parseInt(projectId),
                    description: description,
                    amount: parseFloat(amount) || 0,
                    date: date,
                    is_virtual: isVirtual
                };

                let response;
                
                if (isVirtual || !expenseId) {
                    // For virtual expenses or when no ID is provided, create a new expense
                    console.log('Creating new expense for virtual/zero-amount expense');
                    response = await fetch(`{{ url('/expenses') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });
                } else {
                    // For existing expenses, update the specific one
                    console.log('Updating existing expense');
                    response = await fetch(`{{ url('/expenses') }}/${expenseId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });
                }

                console.log('Response status:', response.status);

                if (response.ok) {
                    const result = await response.json();
                    console.log('Expense saved successfully:', result);
                    
                    // Show success notification
                    showNotification('Expense updated successfully', 'success');
                
                // Close the edit modal if open
                const modal = document.getElementById('editExpenseModal');
                if (modal) {
                    modal.classList.add('hidden');
                }
                
                // Refresh the track record data
                try {
                    await refreshTrackRecord(projectId);
                } catch (error) {
                    console.error('Error refreshing track record:', error);
                }
                    
                    // Update the UI immediately
                    const trackRecordModal = document.getElementById('trackRecordModal');
                    if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                        // Find the expense row to update
                        const expenseRow = isVirtual 
                            ? null // Will be added as new row
                            : document.querySelector(`tr[data-expense-id="${expenseId}"]`);
                        
                        if (isVirtual) {
                            // For virtual expenses, add a new row at the top
                            const tbody = document.querySelector('#projectsContainer tbody');
                            if (tbody) {
                                const newRow = document.createElement('tr');
                                newRow.className = 'hover:bg-blue-50 transition-colors duration-150';
                                newRow.setAttribute('data-expense-id', result.id || '');
                                newRow.innerHTML = `
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${new Date(date).toLocaleDateString()}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                        ${description}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-red-700">₱${parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <button class="edit-expense-btn bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md mr-2 font-medium text-xs transition-colors duration-150"
                                            data-expense-id="${result.id || ''}"
                                            data-description="${description.replace(/"/g, '&quot;')}"
                                            data-amount="${amount}"
                                            data-date="${date}"
                                            data-project-id="${projectId}"
                                            data-is-virtual="false">
                                            Edit
                                        </button>
                                        <button class="delete-expense-btn bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md font-medium text-xs transition-colors duration-150"
                                            data-expense-id="${result.id || ''}"
                                            data-description="${description.replace(/"/g, '&quot;')}">
                                            Delete
                                        </button>
                                    </td>
                                `;
                                tbody.insertBefore(newRow, tbody.firstChild);
                            }
                        } else if (expenseRow) {
                            // For existing expenses, update the row
                            const cells = expenseRow.querySelectorAll('td');
                            if (cells.length >= 3) {
                                // Update date
                                cells[0].textContent = new Date(date).toLocaleDateString();
                                // Update description
                                const descCell = cells[1];
                                if (descCell) {
                                    descCell.innerHTML = `
                                        ${description}
                                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Updated</span>
                                    `;
                                }
                                // Update amount
                                cells[2].innerHTML = `₱${parseFloat(amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                
                                // Update action buttons
                                if (cells[3]) {
                                    cells[3].innerHTML = `
                                        <button class="edit-expense-btn bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md mr-2 font-medium text-xs transition-colors duration-150"
                                            data-expense-id="${result.id || expenseId}"
                                            data-description="${description.replace(/"/g, '&quot;')}"
                                            data-amount="${amount}"
                                            data-date="${date}"
                                            data-project-id="${projectId}"
                                            data-is-virtual="false">
                                            Edit
                                        </button>
                                        <button class="delete-expense-btn bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md font-medium text-xs transition-colors duration-150"
                                            data-expense-id="${result.id || expenseId}"
                                            data-description="${description.replace(/"/g, '&quot;')}">
                                            Delete
                                        </button>
                                    `;
                                }
                                
                                // Add visual feedback
                                expenseRow.classList.add('bg-green-50');
                                setTimeout(() => {
                                    expenseRow.classList.remove('bg-green-50');
                                    if (descCell) {
                                        descCell.innerHTML = description;
                                    }
                                }, 1000);
                            }
                        }
                        
                        // Update summary cards if they exist
                        const summaryCards = document.querySelectorAll('.summary-card');
                        if (summaryCards.length > 0) {
                            // Trigger a custom event to update the summary cards
                            const event = new CustomEvent('expenseUpdated', { 
                                detail: { 
                                    expenseId: result.id || expenseId,
                                    amount: parseFloat(amount),
                                    isNew: isVirtual
                                } 
                            });
                            document.dispatchEvent(event);
                        }
                    }
                    
                    isNotificationActive = false;
                } else {
                    const errorText = await response.text();
                    console.error('Save/Update failed:', response.status, errorText);
                    let errorMessage = 'Error saving expense';
                    try {
                        const errorData = JSON.parse(errorText);
                        errorMessage = errorData.message || errorMessage;
                        // Show validation errors if available
                        if (errorData.errors) {
                            const firstError = Object.values(errorData.errors)[0];
                            if (firstError && firstError.length > 0) {
                                errorMessage = firstError[0];
                            }
                        }
                    } catch (e) {
                        // If not JSON, use the text as error message
                        errorMessage = errorText || errorMessage;
                    }
                    if (!isNotificationActive) {
                        showCenteredNotification(errorMessage, 'error', 1000);
                    }
                    isNotificationActive = false;
                }
            } catch (error) {
                console.error('Error updating expense:', error);
                if (!isNotificationActive) {
                    showCenteredNotification('Error updating expense. Please try again.', 'error', 1000);
                }
                isNotificationActive = false;
            }
        }

        // Delete expense API call
        async function deleteExpenseFromAPI(expenseId) {
            try {
                const response = await fetch(`/expenses/${expenseId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    showCenteredNotification('Expense deleted successfully!', 'success', 1000);
                    // Refresh the track record modal
                    setTimeout(() => {
                        openMultipleTrackRecordModal(currentSelectedProjects);
                    }, 1000);
                } else {
                    const errorData = await response.json().catch(() => ({}));
                    showCenteredNotification(errorData.message || 'Error deleting expense', 'error', 1000);
                }
            } catch (error) {
                console.error('Error deleting expense:', error);
                showCenteredNotification('Error deleting expense. Please try again.', 'error', 1000);
            }
        }

        // Receipt Modal Functions
        function openReceiptModal(projectId, isMultiple = false, projectIds = null) {
            console.log('openReceiptModal called with:', { projectId, isMultiple, projectIds });

            const receiptModal = document.getElementById('receiptModal');
            const receiptContent = document.getElementById('receiptContent');

            if (!receiptModal) {
                console.error('Receipt modal not found!');
                return;
            }

            if (!receiptContent) {
                console.error('Receipt content container not found!');
                return;
            }

            // Show loading state
            receiptContent.innerHTML = `
                <div class="flex items-center justify-center py-20">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Loading receipt...</p>
                    </div>
                </div>
            `;

            ModalManager.openModal('receiptModal');
            console.log('Receipt modal opened');

            // Load receipt content
            if (isMultiple && projectIds) {
                console.log('Loading multiple receipts for:', projectIds);
                loadMultipleReceipts(projectIds);
            } else {
                console.log('Loading single receipt for project:', projectId);
                loadSingleReceipt(projectId);
            }
        }

        async function loadSingleReceipt(projectId) {
            try {
                console.log('Loading receipt for project ID:', projectId);
                const response = await fetch(`/projects/${projectId}/receipt`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const html = await response.text();
                console.log('Receipt HTML received, length:', html.length);

                // Extract the receipt container content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const receiptContainer = doc.querySelector('#receiptContainer');

                if (receiptContainer) {
                    document.getElementById('receiptContent').innerHTML = receiptContainer.outerHTML;
                    currentReceiptData = { type: 'single', projectId: projectId };
                    console.log('Receipt loaded successfully!');
                } else {
                    console.error('receiptContainer element not found in HTML');
                    console.log('HTML preview:', html.substring(0, 500));
                    throw new Error('Receipt content not found');
                }
            } catch (error) {
                console.error('Error loading receipt:', error);
                document.getElementById('receiptContent').innerHTML = `
                    <div class="text-center py-20">
                        <div class="text-red-600 text-6xl mb-4">⚠️</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Error Loading Receipt</h3>
                        <p class="text-gray-600 mb-4">Unable to load the receipt. Please try again.</p>
                        <p class="text-gray-500 text-sm mb-4">Error: ${error.message}</p>
                        <button onclick="closeReceiptModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Close</button>
                    </div>
                `;
            }
        }

        async function loadMultipleReceipts(projectIds) {
            try {
                console.log('Loading multiple receipts for project IDs:', projectIds);
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                projectIds.forEach(id => formData.append('project_ids[]', id));

                const response = await fetch('/projects/multiple-receipts', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const html = await response.text();
                console.log('Multiple receipts HTML received, length:', html.length);

                // Extract the main content (skip the action buttons)
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const mainContent = doc.querySelector('body').innerHTML;

                // Remove the action buttons section and keep only the receipt content
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = mainContent;
                const actionButtons = tempDiv.querySelector('.no-print');
                if (actionButtons) {
                    actionButtons.remove();
                }

                document.getElementById('receiptContent').innerHTML = tempDiv.innerHTML;
                currentReceiptData = { type: 'multiple', projectIds: projectIds };
                console.log('Multiple receipts loaded successfully!');
            } catch (error) {
                console.error('Error loading multiple receipts:', error);
                document.getElementById('receiptContent').innerHTML = `
                    <div class="text-center py-20">
                        <div class="text-red-600 text-6xl mb-4">⚠️</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Error Loading Receipts</h3>
                        <p class="text-gray-600 mb-4">Unable to load the receipts. Please try again.</p>
                        <p class="text-gray-500 text-sm mb-4">Error: ${error.message}</p>
                        <button onclick="closeReceiptModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Close</button>
                    </div>
                `;
            }
        }

        function closeReceiptModal() {
            ModalManager.closeModal('receiptModal');
            currentReceiptData = null;
        }

        function printReceipt() {
            const receiptContent = document.getElementById('receiptContent');
            if (receiptContent) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Project Receipt</title>
                        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                        <style>
                            @media print {
                                body { background: white !important; }
                                .print-container { box-shadow: none !important; margin: 0 !important; }
                            }
                        </style>
                    </head>
                    <body>
                        ${receiptContent.innerHTML}
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 1000);
            }
        }

        function downloadReceiptPDF() {
            if (currentReceiptData) {
                if (currentReceiptData.type === 'single') {
                    window.open(`/projects/${currentReceiptData.projectId}/receipt`, '_blank');
                } else {
                    // For multiple receipts, we need to submit a form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/projects/multiple-receipts';
                    form.target = '_blank';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);

                    currentReceiptData.projectIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'project_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            }
        }

        function downloadReceiptExcel() {
            // This would trigger the Excel download functionality from the receipt pages
            if (currentReceiptData) {
                showCenteredNotification('Excel download functionality will be implemented in the receipt view.', 'info', 1000);
            }
        }

        // Close modal when clicking outside
        document.getElementById('receiptModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReceiptModal();
            }
        });

        // ESC handler removed - using centralized modal manager

        // Close button event listener
        document.getElementById('closeReceiptModal').addEventListener('click', closeReceiptModal);

        // Project Search Functionality
        document.getElementById('projectSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const projectCards = document.querySelectorAll('.project-card');
            let visibleCount = 0;

            projectCards.forEach(card => {
                // Get project name from h3 element
                const projectName = card.querySelector('h3')?.textContent.toLowerCase() || '';
                
                // Get FPP code from the p element containing 'F/P/P:'
                const fppElement = Array.from(card.querySelectorAll('p')).find(p => p.textContent.includes('F/P/P:'));
                const fppCode = fppElement ? fppElement.textContent.toLowerCase() : '';
                
                // Get engineer information if available
                const engineerElement = card.querySelector('.engineer-name');
                const engineerName = engineerElement ? engineerElement.textContent.toLowerCase() : '';
                const engineerSpecialization = engineerElement && engineerElement.nextElementSibling && 
                                            engineerElement.nextElementSibling.classList.contains('text-gray-300') ? 
                                            engineerElement.nextElementSibling.textContent.toLowerCase() : '';

                // Search in project name, F/P/P code, engineer name, and specialization
                const isMatch = projectName.includes(searchTerm) || 
                               fppCode.includes(searchTerm) || 
                               engineerName.includes(searchTerm) ||
                               engineerSpecialization.includes(searchTerm);

                if (isMatch || searchTerm === '') {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update the total projects count in the summary card
            const totalProjectsCard = document.getElementById('totalProjectsCard');
            if (totalProjectsCard) {
                totalProjectsCard.textContent = visibleCount;
            }

            // Show "No results" message if no projects match
            let noResultsMessage = document.getElementById('noSearchResults');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.id = 'noSearchResults';
                    noResultsMessage.className = 'col-span-full text-center py-12 bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl border border-white border-opacity-20';
                    noResultsMessage.innerHTML = `
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-bold text-white mb-2">No projects found</h3>
                        <p class="text-blue-200">Try adjusting your search terms or check the spelling.</p>
                        <p class="text-blue-300 text-sm mt-2">Search by project name, F/P/P code, or engineer name.</p>
                    `;
                    document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3.gap-8').appendChild(noResultsMessage);
                }
                noResultsMessage.style.display = 'block';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        });

        // Keyboard shortcuts (Ctrl+K for search)
        document.addEventListener('keydown', function(e) {
            // Ctrl+K or Cmd+K for quick search
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('projectSearch');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
                return;
            }
            // ESC handling removed - using centralized modal manager only
        });
        
        // Mobile Navigation and Sidebar Functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');
        const mobileAddProjectBtn = document.getElementById('mobileAddProjectBtn');
        
        // Open sidebar
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileSidebar.classList.add('open');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }
        
        // Close sidebar
        function closeSidebar() {
            mobileSidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', closeSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
        
        // Mobile Add Project button
        if (mobileAddProjectBtn) {
            mobileAddProjectBtn.addEventListener('click', function() {
                closeSidebar();
                setTimeout(() => {
                    if (openAddProjectBtn) {
                        openAddProjectBtn.click();
                    }
                }, 300);
            });
        }
        
        // Touch/Swipe Gesture Support for Project Cards
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        let currentSwipeCard = null;
        
        // Hover effects disabled for project cards

        // Add touch event listeners to project cards
        function addSwipeListeners() {
            const projectCards = document.querySelectorAll('.project-card');

            projectCards.forEach(card => {
                // Add swipe indicator for mobile
                if (window.innerWidth <= 768 && !card.querySelector('.swipe-indicator')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'swipe-indicator';
                    indicator.innerHTML = '← Swipe';
                    card.style.position = 'relative';
                    card.appendChild(indicator);
                }
                
                card.addEventListener('touchstart', handleTouchStart, { passive: true });
                card.addEventListener('touchmove', handleTouchMove, { passive: false });
                card.addEventListener('touchend', handleTouchEnd, { passive: true });
            });
        }
        
        function handleTouchStart(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            currentSwipeCard = e.currentTarget;
        }
        
        function handleTouchMove(e) {
            if (!currentSwipeCard) return;
            
            touchEndX = e.touches[0].clientX;
            touchEndY = e.touches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Only handle horizontal swipes
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 30) {
                e.preventDefault();
                
                // Apply visual feedback during swipe
                if (deltaX > 0) {
                    currentSwipeCard.style.transform = `translateX(${Math.min(deltaX * 0.3, 50)}px)`;
                    currentSwipeCard.style.opacity = Math.max(1 - (deltaX * 0.002), 0.7);
                } else {
                    currentSwipeCard.style.transform = `translateX(${Math.max(deltaX * 0.3, -50)}px)`;
                    currentSwipeCard.style.opacity = Math.max(1 + (deltaX * 0.002), 0.7);
                }
            }
        }
        
        function handleTouchEnd(e) {
            if (!currentSwipeCard) return;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Reset visual state
            currentSwipeCard.style.transform = '';
            currentSwipeCard.style.opacity = '';
            
            // Only handle horizontal swipes with sufficient distance
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 100) {
                const projectId = currentSwipeCard.querySelector('.project-checkbox')?.dataset.projectId;
                const projectName = currentSwipeCard.querySelector('.project-checkbox')?.dataset.projectName;
                
                if (deltaX > 0) {
                    // Swipe right - Add Expense
                    if (projectId && projectName) {
                        openExpenseModal(projectId, projectName);
                    }
                } else {
                    // Swipe left - Edit Project
                    const editBtn = currentSwipeCard.querySelector('.edit-project-btn');
                    if (editBtn) {
                        editBtn.click();
                    }
                }
            }
            
            currentSwipeCard = null;
            touchStartX = 0;
            touchStartY = 0;
            touchEndX = 0;
            touchEndY = 0;
        }
        
        // Mobile-optimized modal handling
        function optimizeModalsForMobile() {
            const modals = document.querySelectorAll('.fixed.inset-0');
            
            modals.forEach(modal => {
                const modalContent = modal.querySelector('div:first-child');
                if (modalContent && window.innerWidth <= 768) {
                    modalContent.classList.add('modal-content');
                }
            });
        }
        
        // Initialize mobile optimizations
        window.addEventListener('resize', function() {
            optimizeModalsForMobile();
            
            // Re-add swipe indicators on resize
            if (window.innerWidth <= 768) {
                addSwipeListeners();
            } else {
                // Remove swipe indicators on desktop
                document.querySelectorAll('.swipe-indicator').forEach(indicator => {
                    indicator.remove();
                });
            }
        });
        
        // Initialize on load
        setTimeout(() => {
            addSwipeListeners();
            optimizeModalsForMobile();
        }, 500);
        
        // AGGRESSIVE MOBILE DETECTION AND STYLING
        function forceMobileStyles() {
            const isMobile = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                console.log('MOBILE DETECTED - Applying mobile styles');
                
                // Force mobile navigation to show
                const mobileNav = document.querySelector('.mobile-nav');
                if (mobileNav) {
                    mobileNav.style.display = 'flex';
                    mobileNav.style.position = 'fixed';
                    mobileNav.style.top = '0';
                    mobileNav.style.left = '0';
                    mobileNav.style.right = '0';
                    mobileNav.style.zIndex = '9999';
                    mobileNav.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                    mobileNav.style.height = '60px';
                    mobileNav.style.padding = '12px 16px';
                    mobileNav.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
                    mobileNav.style.borderBottom = '3px solid #16a34a';
                }
                
                // Hide desktop navigation
                const desktopNav = document.querySelector('.desktop-nav');
                if (desktopNav) {
                    desktopNav.style.display = 'none';
                }
                
                // Adjust body for mobile nav
                document.body.style.paddingTop = '70px';
                document.body.style.paddingLeft = '8px';
                document.body.style.paddingRight = '8px';
                
                // Force single column layout
                const grids = document.querySelectorAll('.grid');
                grids.forEach(grid => {
                    grid.style.gridTemplateColumns = '1fr';
                    grid.style.gap = '12px';
                });
                
                // Make cards mobile-friendly
                const cards = document.querySelectorAll('.glass-card');
                cards.forEach(card => {
                    card.style.marginBottom = '16px';
                    card.style.padding = '16px';
                    card.style.border = '2px solid rgba(255, 255, 255, 0.4)';
                    card.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.4)';
                });
                
                // Make buttons touch-friendly
                const buttons = document.querySelectorAll('button');
                buttons.forEach(button => {
                    button.style.minHeight = '44px';
                    button.style.fontSize = '16px';
                    button.style.padding = '12px 16px';
                });
                
                // Make inputs touch-friendly
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.style.fontSize = '16px';
                    input.style.minHeight = '44px';
                    input.style.padding = '12px';
                });
                
                // Enhance search input
                const searchInput = document.getElementById('projectSearch');
                if (searchInput) {
                    searchInput.style.fontSize = '18px';
                    searchInput.style.padding = '16px';
                    searchInput.style.border = '3px solid #10b981';
                    searchInput.style.borderRadius = '12px';
                }
                
                // Add mobile indicator to body
                document.body.classList.add('mobile-optimized');
                document.body.style.setProperty('--mobile-detected', '"YES"');
                
                // Add visual mobile indicator
                const mobileIndicator = document.createElement('div');
                mobileIndicator.innerHTML = '📱 Mobile Mode Active';
                mobileIndicator.style.cssText = `
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: #10b981;
                    color: white;
                    padding: 8px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                    z-index: 10000;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                    animation: bounce 2s infinite;
                `;
                document.body.appendChild(mobileIndicator);
                
                // Remove indicator after 5 seconds
                setTimeout(() => {
                    if (mobileIndicator.parentNode) {
                        mobileIndicator.remove();
                    }
                }, 1000);
                
                console.log('Mobile styles applied successfully!');
            }
        }
        
        // Apply mobile styles immediately and on resize
        forceMobileStyles();
        window.addEventListener('resize', forceMobileStyles);
        window.addEventListener('orientationchange', forceMobileStyles);
        
        // Apply mobile styles when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', forceMobileStyles);
        } else {
            forceMobileStyles();
        }
        

        
        // Event listener for expense updates to refresh summary cards
        document.addEventListener('expenseUpdated', function(e) {
            const { amount, isNew } = e.detail;
            const summaryCards = document.querySelectorAll('.summary-card');
            
            summaryCards.forEach(card => {
                // Update total spent
                const spentElement = card.querySelector('.spent-amount');
                if (spentElement) {
                    const currentSpent = parseFloat(spentElement.textContent.replace(/[^\d.-]/g, '')) || 0;
                    const newSpent = isNew ? currentSpent + amount : currentSpent;
                    spentElement.textContent = newSpent.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    // Update remaining budget
                    const totalBudget = parseFloat(card.getAttribute('data-budget') || 0);
                    if (totalBudget) {
                        const remainingElement = card.querySelector('.remaining-amount');
                        if (remainingElement) {
                            const remaining = totalBudget - newSpent;
                            remainingElement.textContent = remaining.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            
                            // Update color based on remaining amount
                            remainingElement.classList.remove('text-green-700', 'text-red-700');
                            remainingElement.classList.add(remaining >= 0 ? 'text-green-700' : 'text-red-700');
                            
                            // Update percentage used
                            const percentUsed = (newSpent / totalBudget) * 100;
                            const percentElement = card.querySelector('.percent-used');
                            if (percentElement) {
                                percentElement.textContent = percentUsed.toFixed(1);
                            }
                        }
                    }
                }
            });
        });
        
        // Skeleton Loading Management
        class SkeletonLoader {
            constructor() {
                this.isInitialLoad = true;
                this.loadingStates = {
                    summaryCards: false,
                    projectsGrid: false,
                    trackRecord: false,
                    forms: false
                };
            }
            
            showSkeleton(type, duration = 1500) {
                const skeletonElement = document.getElementById(`skeleton${this.capitalize(type)}`);
                const actualElement = document.getElementById(`actual${this.capitalize(type)}`);
                
                if (skeletonElement && actualElement) {
                    this.loadingStates[type] = true;
                    skeletonElement.style.display = 'grid';
                    actualElement.style.display = 'none';
                    
                    // Auto-hide after duration (fallback)
                    setTimeout(() => {
                        if (this.loadingStates[type]) {
                            this.hideSkeleton(type);
                        }
                    }, duration);
                }
            }
            
            hideSkeleton(type) {
                const skeletonElement = document.getElementById(`skeleton${this.capitalize(type)}`);
                const actualElement = document.getElementById(`actual${this.capitalize(type)}`);
                
                if (skeletonElement && actualElement) {
                    this.loadingStates[type] = false;
                    
                    // Smooth transition
                    skeletonElement.style.opacity = '0';
                    setTimeout(() => {
                        skeletonElement.style.display = 'none';
                        skeletonElement.style.opacity = '1';
                        actualElement.style.display = 'grid';
                        actualElement.style.opacity = '0';
                        
                        // Fade in actual content
                        setTimeout(() => {
                            actualElement.style.opacity = '1';
                        }, 50);
                    }, 300);
                }
            }
            
            showModalSkeleton(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const modalContent = modal.querySelector('.bg-white, .bg-gray-800');
                    if (modalContent) {
                        // Create skeleton overlay
                        const skeletonOverlay = document.createElement('div');
                        skeletonOverlay.className = 'skeleton-modal-overlay absolute inset-0 bg-white z-10';
                        skeletonOverlay.innerHTML = `
                            <div class="skeleton-modal">
                                <div class="skeleton-form-field">
                                    <div class="skeleton-label skeleton"></div>
                                    <div class="skeleton-input skeleton"></div>
                                </div>
                                <div class="skeleton-form-field">
                                    <div class="skeleton-label skeleton"></div>
                                    <div class="skeleton-input skeleton"></div>
                                </div>
                                <div class="skeleton-form-field">
                                    <div class="skeleton-label skeleton"></div>
                                    <div class="skeleton-input skeleton"></div>
                                </div>
                                <div class="flex space-x-4 mt-6">
                                    <div class="skeleton-button skeleton"></div>
                                    <div class="skeleton-button skeleton"></div>
                                </div>
                            </div>
                        `;
                        
                        modalContent.style.position = 'relative';
                        modalContent.appendChild(skeletonOverlay);
                        
                        // Auto-remove after 2 seconds
                        setTimeout(() => {
                            if (skeletonOverlay.parentNode) {
                                skeletonOverlay.remove();
                            }
                        }, 1000);
                    }
                }
            }
            
            hideModalSkeleton(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const skeletonOverlay = modal.querySelector('.skeleton-modal-overlay');
                    if (skeletonOverlay) {
                        skeletonOverlay.style.opacity = '0';
                        setTimeout(() => {
                            skeletonOverlay.remove();
                        }, 300);
                    }
                }
            }
            
            capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
            
            // Initialize skeleton states on page load
            initializeSkeletons() {
                if (this.isInitialLoad) {
                    // Show skeletons for initial page load
                    this.showSkeleton('summaryCards', 800);
                    this.showSkeleton('projectsGrid', 1200);
                    
                    // Mark as loaded after initial display
                    setTimeout(() => {
                        this.isInitialLoad = false;
                        document.body.classList.add('content-loaded');
                    }, 1500);
                }
            }
        }
        
        // Function to open Add Expense modal for a project
        function openAddExpenseModal(projectId, projectName) {
            console.log('Opening Add Expense modal for project:', projectId, projectName);
            
            // Set the project ID in the expense form
            const expenseForm = document.getElementById('addExpenseForm');
            const expenseProjectId = document.getElementById('expenseProjectId');
            const expenseProjectName = document.getElementById('expenseProjectName');
            
            if (expenseForm && expenseProjectId && expenseProjectName) {
                // Set the project ID in the hidden input
                expenseProjectId.value = projectId;
                
                // Update the project name display
                expenseProjectName.textContent = projectName;
                
                // Reset the form
                expenseForm.reset();
                
                // Open the modal with a slight delay to ensure smooth transition
                setTimeout(() => {
                    ModalManager.openModal('addExpenseModal');
                    
                    // Focus on the first input field for better UX
                    const firstInput = expenseForm.querySelector('input:not([type="hidden"]), select');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 100);
            } else {
                console.error('Expense form elements not found');
                showCenteredNotification('Could not open expense form. Please try again.', 'error', 1000);
            }
        }
        
        // Function to save project and open expense modal
        async function saveAndOpenExpense() {
            const form = document.getElementById('projectForm');
            const formData = new FormData(form);
            const url = form.action || '/projects';
            const method = form.dataset.method || 'POST';
            
            try {
                const submitBtn = document.getElementById('saveAndAddExpenseBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Saving...';
                
                // Convert FormData to JSON object
                const jsonData = {};
                formData.forEach((value, key) => {
                    jsonData[key] = value;
                });
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(jsonData)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Close the current modal
                    ModalManager.closeModal('addProjectModal');
                    
                    // Show success message
                    showCenteredNotification('Project saved successfully!', 'success', 1000);
                    
                    // Open the add expense modal for the new project after a short delay
                    setTimeout(() => {
                        openAddExpenseModal(data.project.id, data.project.name);
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to save project');
                }
            } catch (error) {
                console.error('Error:', error);
                showCenteredNotification(error.message || 'An error occurred while saving the project', 'error', 1000);
            } finally {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        }
        
        // Add event listeners for the Add Expense modal
        document.addEventListener('DOMContentLoaded', function() {
            // Add Project Modal
            const openAddProjectBtn = document.getElementById('openAddProjectModal');
            const closeAddProjectBtn = document.getElementById('closeAddProjectModal');
            const cancelProjectBtn = document.getElementById('cancelProjectBtn');
            
            if (openAddProjectBtn) {
                openAddProjectBtn.addEventListener('click', function() {
                    ModalManager.openModal('addProjectModal');
                    // Set today's date as default
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('projectDate').value = today;
                });
            }
            
            if (closeAddProjectBtn) {
                closeAddProjectBtn.addEventListener('click', function() {
                    ModalManager.closeModal('addProjectModal');
                });
            }
            
            if (cancelProjectBtn) {
                cancelProjectBtn.addEventListener('click', function() {
                    ModalManager.closeModal('addProjectModal');
                });
            }
            
            // Close modal when clicking outside
            const addProjectModal = document.getElementById('addProjectModal');
            if (addProjectModal) {
                addProjectModal.addEventListener('click', function(e) {
                    if (e.target === addProjectModal) {
                        ModalManager.closeModal('addProjectModal');
                    }
                });
            }
            
            // Close modal buttons
            const closeAddExpenseBtn = document.getElementById('closeAddExpenseModal');
            const addExpenseForm = document.getElementById('addExpenseForm');
            
            if (closeAddExpenseBtn) {
                closeAddExpenseBtn.addEventListener('click', function() {
                    ModalManager.closeModal('addExpenseModal');
                });
            }
            
            // Handle form submission
            if (addExpenseForm) {
                addExpenseForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(addExpenseForm);
                    const submitBtn = addExpenseForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    try {
                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Adding...';
                        
                        const response = await fetch('/expenses', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            showCenteredNotification('Expense added successfully!', 'success', 1000);
                            addExpenseForm.reset();
                            ModalManager.closeModal('addExpenseModal');
                            
                            // Refresh the page to show the new expense
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Failed to add expense');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showCenteredNotification(error.message || 'An error occurred while adding the expense', 'error', 1000);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                });
            }
            
            // Function to save project and open Add Expense modal
        async function saveAndOpenExpense() {
            const form = document.getElementById('projectForm');
            if (!form) {
                console.error('Project form not found');
                showCenteredNotification('Error: Project form not found', 'error', 1000);
                return;
            }

            const submitBtn = document.querySelector('#saveAndAddExpenseBtn');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            
            try {
                // Show loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Saving...';
                }
                
                // Create FormData and convert to plain object
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                
                // Send the request - bypassing HTML5 validation for required fields
                const response = await fetch(form.action || '/projects', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const responseData = await response.json();
                
                if (!response.ok) {
                    throw new Error(responseData.message || 'Failed to save project');
                }
                
                // Show success message
                showCenteredNotification('Project saved successfully!', 'success', 1000);
                
                // Close the project modal and open the expense modal
                ModalManager.closeModal('addProjectModal');
                
                // Small delay for better UX
                setTimeout(() => {
                    openAddExpenseModal(responseData.project.id, responseData.project.name);
                }, 300);
                
            } catch (error) {
                console.error('Error saving project:', error);
                showCenteredNotification(error.message || 'An error occurred while saving the project', 'error', 1000);
            } finally {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        }
        
        // Event delegation for Proceed to Add Expense button
        document.addEventListener('click', function(e) {
            if (e.target && e.target.matches('#saveAndAddExpenseBtn, #saveAndAddExpenseBtn *')) {
                e.preventDefault();
                const form = document.getElementById('projectForm');
                if (form && form.checkValidity()) {
                    saveAndOpenExpense();
                } else if (form) {
                    form.reportValidity();
                }
            }
        });
        
        // Render detailed engineering team in the modal
        function renderDetailedEngineeringTeam(assignments) {
            const teamList = document.getElementById('engineeringTeamList');
            const template = document.getElementById('teamMemberTemplate');
            const projectEngineerId = document.querySelector('#editDetailedEngineeringModal').getAttribute('data-project-engineer-id');
            
            if (!assignments || assignments.length === 0) {
                teamList.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users-slash text-3xl mb-2"></i>
                        <p>No engineers assigned to this project yet.</p>
                    </div>
                `;
                return;
            }
            
            // Clear the list
            teamList.innerHTML = '';
            
            // Add each team member
            assignments.forEach(assignment => {
                const clone = document.importNode(template.content, true);
                const row = clone.querySelector('.team-member-row');
                const nameElement = clone.querySelector('.engineer-name');
                const salaryInput = clone.querySelector('.salary-input');
                const roleSelect = clone.querySelector('.role-select');
                const teamHeadBadge = clone.querySelector('.team-head-badge');
                const projectEngineerBadge = clone.querySelector('.project-engineer-badge');
                const deleteBtn = clone.querySelector('.delete-engineer-btn');
                const saveBtn = clone.querySelector('.save-salary-btn');
                
                // Ensure salary input is enabled
                if (salaryInput) {
                    salaryInput.disabled = false;
                    salaryInput.classList.remove('disabled:bg-gray-100', 'disabled:text-gray-400');
                }
                
                row.setAttribute('data-engineer-id', assignment.engineer_id);
                nameElement.textContent = assignment.engineer_name;
                salaryInput.value = assignment.salary || '';
                
                // Set role
                if (assignment.is_team_head) {
                    roleSelect.value = 'head';
                    teamHeadBadge.classList.remove('hidden');
                } else {
                    roleSelect.value = 'member';
                    teamHeadBadge.classList.add('hidden');
                }
                
                // Disable role selection for project engineer
                if (assignment.engineer_id == projectEngineerId) {
                    roleSelect.disabled = true;
                    deleteBtn.style.display = 'none';
                    projectEngineerBadge.classList.remove('hidden');
                } else {
                    projectEngineerBadge.classList.add('hidden');
                }
                
                // Add event listeners
                roleSelect.addEventListener('change', function() {
                    const isTeamHead = this.value === 'head';
                    teamHeadBadge.classList.toggle('hidden', !isTeamHead);
                });
                
                deleteBtn.addEventListener('click', async function() {
                    const engineerId = row.getAttribute('data-engineer-id');
                    const engineerName = nameElement.textContent;
                    const projectId = document.querySelector('#editDetailedEngineeringModal').getAttribute('data-project-id');
                    
                    // Confirm deletion
                    if (!confirm(`Are you sure you want to remove ${engineerName} from this project?`)) {
                        return;
                    }
                    
                    try {
                        // Show loading state
                        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        deleteBtn.disabled = true;
                        
                        const now = new Date();
                        const response = await fetch(`/monthly-assignments/remove-engineer`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                project_id: projectId,
                                engineer_id: engineerId,
                                year: now.getFullYear(),
                                month: now.getMonth() + 1 // JavaScript months are 0-indexed
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to remove engineer');
                        }
                        
                        // Remove the row from the UI
                        row.remove();
                        showCenteredNotification('Engineer removed successfully!', 'success', 1000);
                        
                        // Refresh the track record modal if it's open
                        const trackRecordModal = document.getElementById('trackRecordModal');
                        if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                            const trackRecordProjectId = trackRecordModal.getAttribute('data-project-id');
                            if (trackRecordProjectId) {
                                if (window.currentSelectedProjects && window.currentSelectedProjects.length > 0) {
                                    openMultipleTrackRecordModal(window.currentSelectedProjects);
                                } else if (window.openTrackRecordModal) {
                                    window.openTrackRecordModal(trackRecordProjectId);
                                }
                            }
                        }
                        
                    } catch (error) {
                        console.error('Error removing engineer:', error);
                        showCenteredNotification('Failed to remove engineer. Please try again.', 'error', 1000);
                    } finally {
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                    }
                });
                
                teamList.appendChild(clone);
            });
        }
        
                 // Open Edit Detailed Engineering Modal
         window.openEditDetailedEngineeringModal = function(projectId, projectName) {
             console.log('Opening edit detailed engineering modal for project:', projectId, projectName);
             
             // Set the project ID and name in the modal
             const modal = document.getElementById('editDetailedEngineeringModal');
             if (!modal) {
                 console.error('Edit detailed engineering modal not found');
                 showCenteredNotification('Error: Could not open detailed engineering editor', 'error', 1000);
                 return;
             }
             
             // Set the project ID on the modal for later use
             modal.setAttribute('data-project-id', projectId);
             
             modal.setAttribute('data-project-id', projectId);
             modal.setAttribute('data-project-name', projectName);
             
             // Update modal title
             const modalTitle = modal.querySelector('h2 px-3');
             if (modalTitle) {
                 modalTitle.textContent = `Edit Detailed Engineering - ${projectName}`;
             }
             
             // Show loading state
             const teamList = document.getElementById('engineeringTeamList');
             teamList.innerHTML = `
                 <div class="text-center py-8 text-gray-400">
                     <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                     <p>Loading team information...</p>
                 </div>
             `;
             
             // Show the modal without closing the track record modal
             if (!modal.classList.contains('hidden')) return; // Already open
             
             modal.classList.remove('hidden');
             // Add to stack if not already there
             if (!ModalManager.modalStack.includes('editDetailedEngineeringModal')) {
                 ModalManager.modalStack.push('editDetailedEngineeringModal');
                 console.log('Modal opened with stack:', ModalManager.modalStack);
             }
             
             // Load the team data
             fetch(`/projects/${projectId}/monthly-assignments`)
                 .then(response => response.json())
                 .then(data => {
                     console.log('Team data loaded:', data);
                     renderDetailedEngineeringTeam(data.monthly_assignments || []);
                 })
                 .catch(error => {
                     console.error('Error loading team data:', error);
                     teamList.innerHTML = `
                         <div class="text-center py-8 text-red-500">
                             <div class="text-4xl mb-2">⚠️</div>
                             <p>Failed to load team information.</p>
                             <button onclick="openEditDetailedEngineeringModal(${projectId}, '${projectName}')" 
                                     class="mt-2 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                 Try Again
                             </button>
                         </div>
                     `;
                 });
         };
         
         // Load detailed engineering team data
         window.loadDetailedEngineeringTeam = function(projectId) {
            const modal = document.getElementById('editDetailedEngineeringModal');
            if (!modal) return;
            
            // Set the project ID in the modal
            modal.setAttribute('data-project-id', projectId);
            
            // Show loading state
            const teamList = document.getElementById('engineeringTeamList');
            teamList.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <p>Loading team information...</p>
                </div>
            `;
            
                         // Show the modal
             ModalManager.openModal('editDetailedEngineeringModal');
            
            // Load the team data
            fetch(`/projects/${projectId}/monthly-assignments`)
                .then(response => response.json())
                .then(data => {
                    // Store project engineer ID in the modal for reference
                    if (data.project_engineer_id) {
                        modal.setAttribute('data-project-engineer-id', data.project_engineer_id);
                    }
                    renderDetailedEngineeringTeam(data.monthly_assignments || []);
                })
                .catch(error => {
                    console.error('Error loading team data:', error);
                    teamList.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <div class="text-4xl mb-2">⚠️</div>
                            <p>Failed to load team information.</p>
                            <button onclick="loadDetailedEngineeringTeam(${projectId})" 
                                    class="mt-2 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                Try Again
                            </button>
                        </div>
                    `;
                });
        };
        
                 // Close detailed engineering modal when clicking the close button
         document.getElementById('closeEditDetailedEngineeringModal')?.addEventListener('click', function(e) {
             e.stopPropagation();
             const modal = document.getElementById('editDetailedEngineeringModal');
             if (modal) {
                 modal.classList.add('hidden');
                 // Remove from stack
                 const index = ModalManager.modalStack.indexOf('editDetailedEngineeringModal');
                 if (index > -1) {
                     ModalManager.modalStack.splice(index, 1);
                     console.log('Modal closed with stack:', ModalManager.modalStack);
                 }
             }
         });
         
         // Close modal when clicking outside the content
         document.getElementById('editDetailedEngineeringModal')?.addEventListener('click', function(e) {
             if (e.target === this) {
                 ModalManager.closeModal('editDetailedEngineeringModal');
             }
         });
         
         // Handle cancel button click
         document.getElementById('cancelEditDetailedEngineeringBtn')?.addEventListener('click', function() {
             ModalManager.closeModal('editDetailedEngineeringModal');
         });
        
                 // Handle save button click
         document.getElementById('saveDetailedEngineeringBtn')?.addEventListener('click', async function() {
             const modal = document.getElementById('editDetailedEngineeringModal');
             const projectId = modal.getAttribute('data-project-id');
             
             // Show loading state
             const originalButtonText = this.innerHTML;
             this.disabled = true;
             this.innerHTML = `
                 <i class="fas fa-spinner fa-spin mr-2"></i>
                 Saving...
             `;
             
             try {
                 // Get all engineer rows
                 const rows = document.querySelectorAll('#engineeringTeamList .team-member-row');
                 const engineers = [];
                 
                 if (rows.length === 0) {
                     throw new Error('Please add at least one engineer to the team');
                 }
                 
                 rows.forEach(row => {
                     const engineerId = row.getAttribute('data-engineer-id');
                     const salaryInput = row.querySelector('.salary-input');
                     const salary = salaryInput ? salaryInput.value : '';
                     const roleSelect = row.querySelector('.role-select');
                     const isTeamHead = roleSelect ? roleSelect.value === 'head' : false;
                     
                     // Only include engineers with a valid ID
                     if (engineerId) {
                         engineers.push({
                             engineer_id: engineerId,
                             salary: salary ? parseFloat(salary) : 0,
                             is_active: true,
                             is_team_head: isTeamHead
                         });
                     }
                 });
                 
                 if (engineers.length === 0) {
                     throw new Error('No valid engineers found in the team');
                 }
                 
                 // Format the data as expected by the backend
                const requestData = {
                    team: engineers.map(eng => ({
                        engineer_id: eng.engineer_id,
                        salary: eng.salary,
                        is_team_head: eng.is_team_head
                    }))
                };
                
                // Ensure we're sending proper JSON
                const jsonData = JSON.stringify(requestData);
                 
                 console.log('Sending data:', jsonData); // Debug log
               
               const response = await fetch(`/projects/${projectId}/update-detailed-engineering`, {
                   method: 'POST',
                   headers: {
                       'Content-Type': 'application/json',
                       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                       'X-Requested-With': 'XMLHttpRequest',
                       'Accept': 'application/json'
                   },
                   body: JSON.stringify(requestData)
                 });
                 
                 const responseData = await response.json();
                 
                 if (!response.ok) {
                     throw new Error(responseData.message || 'Failed to save changes');
                 }
                 
                 // Show success message
                 showCenteredNotification('Changes saved successfully!', 'success', 1000);
                 
                 // Close the modal after a short delay
                 setTimeout(() => {
                     ModalManager.closeModal('editDetailedEngineeringModal');
                     
                     // Refresh the track record for the current project
                     const projectId = modal.getAttribute('data-project-id');
                     if (projectId) {
                         refreshTrackRecord(projectId).then(() => {
                             // Show success notification
                             showNotification('Detailed engineering team updated successfully', 'success');
                         }).catch(error => {
                             console.error('Error refreshing track record:', error);
                         });
                     }
                     
                     // Also refresh the track record modal if it's open
                     const trackRecordModal = document.getElementById('trackRecordModal');
                     if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                         // Get the current project ID from the track record modal
                         const trackRecordProjectId = trackRecordModal.getAttribute('data-project-id');
                         if (trackRecordProjectId) {
                             // Reopen the track record modal to refresh the data
                             if (window.currentSelectedProjects && window.currentSelectedProjects.length > 0) {
                                 // If we have multiple projects selected, refresh the multi-track record view
                                 openMultipleTrackRecordModal(window.currentSelectedProjects);
                             } else if (window.openTrackRecordModal) {
                                 // If we have a single project, refresh the single track record view
                                 window.openTrackRecordModal(trackRecordProjectId);
                             } else {
                                 // Fallback: reload the page
                                 window.location.reload();
                             }
                         }
                     }
                 }, 500);
                 
             } catch (error) {
                 console.error('Error saving team data:', error);
                 showCenteredNotification(error.message || 'An error occurred while saving team data', 'error', 1000);
             } finally {
                 // Reset button state
                 this.disabled = false;
                 this.innerHTML = originalButtonText;
             }
         });
        
        // Initialize detailed engineering modal
        document.addEventListener('DOMContentLoaded', function() {
            // Event delegation for Apply button in Monthly Engineer Assignments page
            document.addEventListener('click', async function(e) {
                // Check if the clicked element is an Apply button or a child of an Apply button
                const applyBtn = e.target.closest('.apply-salary-btn');
                
                if (applyBtn) {
                    e.preventDefault();
                    
                    const row = applyBtn.closest('tr');
                    const engineerId = row.getAttribute('data-engineer-id');
                    const projectId = document.querySelector('#editDetailedEngineeringModal').getAttribute('data-project-id');
                    const salaryInput = row.querySelector('.salary-input');
                    const salary = salaryInput ? salaryInput.value : '';
                    
                    if (!engineerId || !projectId) {
                        showCenteredNotification('Invalid engineer or project', 'error', 1000);
                        return;
                    }
                    
                    // Show loading state
                    const originalText = applyBtn.innerHTML;
                    applyBtn.disabled = true;
                    applyBtn.innerHTML = '<span class="animate-spin">⏳</span>';
                    
                    try {
                        const response = await fetch('/monthly-assignments/update-salary', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                engineer_id: engineerId,
                                project_id: projectId,
                                salary: salary
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            showCenteredNotification(data.message || 'Salary updated successfully', 'success', 1000);
                            // Refresh the page to reflect changes
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            throw new Error(data.message || 'Failed to update salary');
                        }
                    } catch (error) {
                        console.error('Error updating salary:', error);
                        showCenteredNotification(error.message || 'An error occurred while updating the salary', 'error', 1000);
                    } finally {
                        // Reset button state
                        applyBtn.disabled = false;
                        applyBtn.innerHTML = originalText;
                    }
                }
            });
            // Handle Add Engineer button click - redirect to project's monthly assignments
            window.handleAddEngineerClick = function(e) {
                console.log('Add Engineer button clicked');
                e.preventDefault();
                // Get the project ID and name from the modal
                const modal = document.getElementById('editDetailedEngineeringModal');
                console.log('Modal element:', modal);
                const projectId = modal ? modal.getAttribute('data-project-id') : null;
                console.log('Project ID from modal:', projectId);
                
                if (projectId) {
                    console.log('Found project ID, proceeding with redirect');
                    // Close the modal
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    console.log('Modal instance:', modalInstance);
                    if (modalInstance) {
                        console.log('Hiding modal');
                        modalInstance.hide();
                    }
                    
                    // Redirect to the monthly assignments page with the project ID
                    window.location.href = `/monthly-assignments?project_id=${projectId}`;
                    return;
                }
                
                // Fallback to old behavior if project ID is not found
                const addEngineerForm = document.getElementById('addEngineerForm');
                if (addEngineerForm) {
                    addEngineerForm.classList.remove('hidden');
                    // Scroll to the form
                    addEngineerForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    // Focus the name input
                    const nameInput = document.getElementById('newEngineerName');
                    if (nameInput) nameInput.focus();
                }
            }
            
            // Handle cancel button in the add engineer form
            function handleCancelAddEngineer(e) {
                e.preventDefault();
                const addEngineerForm = document.getElementById('addEngineerForm');
                if (addEngineerForm) {
                    addEngineerForm.classList.add('hidden');
                    // Reset form
                    document.getElementById('addEngineerFormElement').reset();
                }
            }
            
            // Handle save new engineer button
            async function handleSaveNewEngineer(e) {
                e.preventDefault();
                
                const nameInput = document.getElementById('newEngineerName');
                const salaryInput = document.getElementById('newEngineerSalary');
                const isTeamHead = document.getElementById('newEngineerIsTeamHead').checked;
                
                if (!nameInput.value.trim()) {
                    showCenteredNotification('Please enter engineer name', 'error', 1000);
                    nameInput.focus();
                    return;
                }
                
                if (!salaryInput.value || parseFloat(salaryInput.value) <= 0) {
                    showCenteredNotification('Please enter a valid salary', 'error', 1000);
                    salaryInput.focus();
                    return;
                }
                
                try {
                    // Get the project ID from the modal
                    const modal = document.getElementById('editDetailedEngineeringModal');
                    const projectId = modal ? modal.getAttribute('data-project-id') : null;
                    
                    if (!projectId) {
                        throw new Error('Project ID not found');
                    }
                    
                    // Prepare the data for the API
                    const formData = new FormData();
                    formData.append('name', nameInput.value.trim());
                    formData.append('salary', parseFloat(salaryInput.value).toFixed(2));
                    formData.append('is_team_head', isTeamHead ? '1' : '0');
                    formData.append('project_id', projectId);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    // Make the API call to add the engineer
                    const response = await fetch('/api/projects/add-engineer', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to add engineer');
                    }
                    
                    // Add the new engineer to the UI
                    const template = document.getElementById('teamMemberTemplate');
                    const clone = document.importNode(template.content, true);
                    const row = clone.querySelector('.team-member-row');
                    row.setAttribute('data-engineer-id', data.engineer.id);
                    
                    const nameElement = clone.querySelector('.engineer-name');
                    const salaryInputField = clone.querySelector('.salary-input');
                    const roleSelect = clone.querySelector('.role-select');
                    
                    nameElement.textContent = data.engineer.name;
                    salaryInputField.value = parseFloat(data.engineer.salary).toFixed(2);
                    
                    if (data.engineer.is_team_head) {
                        roleSelect.value = 'head';
                        const teamHeadBadge = row.querySelector('.team-head-badge');
                        if (teamHeadBadge) teamHeadBadge.classList.remove('hidden');
                    }
                    
                    // Add to the team list
                    const teamList = document.getElementById('engineeringTeamList');
                    if (teamList) {
                        // Remove any loading message if present
                        if (teamList.querySelector('.text-center')) {
                            teamList.innerHTML = '';
                        }
                        teamList.prepend(row);
                    }
                    
                    // Hide and reset the form
                    const addEngineerForm = document.getElementById('addEngineerForm');
                    if (addEngineerForm) {
                        addEngineerForm.classList.add('hidden');
                        document.getElementById('addEngineerFormElement').reset();
                    }
                    
                    showCenteredNotification('Engineer added successfully', 'success', 1000);
                    
                } catch (error) {
                    console.error('Error adding engineer:', error);
                    showCenteredNotification(error.message || 'Failed to add engineer', 'error', 1000);
                }
            }
            
            // Set up event delegation for dynamically loaded content
            document.addEventListener('click', function(e) {
                // Handle Add Engineer button click - using closest to handle both button and icon clicks
                const addEngineerBtn = e.target.closest('#addEngineerBtn');
                if (addEngineerBtn) {
                    console.log('Add Engineer button clicked (global handler)');
                    e.preventDefault();
                    e.stopPropagation();
                    handleAddEngineerClick(e);
                }
                
                // Handle cancel button in the add engineer form
                if (e.target.closest('#cancelAddEngineer')) {
                    e.preventDefault();
                    handleCancelAddEngineer(e);
                }
                
                // Handle save new engineer button
                if (e.target.closest('#saveNewEngineer') || e.target.closest('#addEngineerFormElement')) {
                    e.preventDefault();
                    handleSaveNewEngineer(e);
                }
            });
            
            // Also set up direct event listeners when the modal is opened
            const editModal = document.getElementById('editDetailedEngineeringModal');
            if (editModal) {
                // Add click listener to the modal itself for better event delegation
                editModal.addEventListener('click', function(e) {
                    // Handle Add Engineer button click
                    const addEngineerBtn = e.target.closest('#addEngineerBtn');
                    if (addEngineerBtn) {
                        console.log('Add Engineer button clicked (modal handler)');
                        e.preventDefault();
                        e.stopPropagation();
                        handleAddEngineerClick(e);
                    }
                    
                    // Handle cancel button in the add engineer form
                    if (e.target.closest('#cancelAddEngineer')) {
                        e.preventDefault();
                        handleCancelAddEngineer(e);
                    }
                    
                    // Handle save new engineer button
                    if (e.target.closest('#saveNewEngineer') || e.target.closest('#addEngineerFormElement')) {
                        e.preventDefault();
                        handleSaveNewEngineer(e);
                    }
                });
            }
            
            const expenseForm = document.getElementById('addExpenseForm');
            if (!expenseForm) {
                console.error('Expense form not found');
                return;
            }
            
            // Format amount inputs to 2 decimal places on blur
            expenseForm.addEventListener('blur', function(e) {
                if (e.target.matches('input[type="number"]')) {
                    if (e.target.value && e.target.value.trim() !== '') {
                        e.target.value = parseFloat(e.target.value).toFixed(2);
                    }
                }
            }, true);
            
            // Handle form submission
            expenseForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                console.log('Expense form submission started');
                
                // Get all amount inputs
                const amountInputs = this.querySelectorAll('input[type="number"]');
                let hasAmount = false;
                
                // Allow zero or blank amounts - no validation needed
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                
                try {
                    // Show loading state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="animate-spin mr-2">⏳</span> Saving...';
                    }
                    
                    // Prepare form data
                    const formData = new FormData(this);
                    
                    // Send the request
                    const response = await fetch('/expenses', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        showCenteredNotification('Expenses saved successfully!', 'success', 1000);
                        
                        // Reset form and close modal
                        this.reset();
                        ModalManager.closeModal('addExpenseModal');
                        
                        // Reload the page to show updated data
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        throw new Error(data.message || 'Failed to save expenses');
                    }
                } catch (error) {
                    console.error('Error saving expenses:', error);
                    showCenteredNotification(error.message || 'An error occurred while saving expenses', 'error', 1000);
                } finally {
                    // Reset button state
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                }
            });
            

            
            // Close modal when clicking outside the modal content
            const modal = document.getElementById('addExpenseModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        ModalManager.closeModal('addExpenseModal');
                    }
                });
            }


        });
        });
        
        // Initialize skeleton loader
        const skeletonLoader = new SkeletonLoader();
        
        // Show skeletons on initial load
        document.addEventListener('DOMContentLoaded', function() {
            skeletonLoader.initializeSkeletons();
        });

        // Hide TO TOP button when modals are open
        function updateBackToTopButtonVisibility() {
            const backToTopBtn = document.getElementById('backToTopBtn');
            if (backToTopBtn) {
                const isAnyModalOpen = document.querySelector('.fixed.inset-0:not(.hidden)') !== null;
                if (isAnyModalOpen) {
                    backToTopBtn.style.display = 'none';
                } else {
                    backToTopBtn.style.display = 'block';
                }
            }
        }

        // Monitor modal state changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    updateBackToTopButtonVisibility();
                }
            });
        });

        // Observe all modal elements
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('.fixed.inset-0');
            modals.forEach(modal => {
                observer.observe(modal, { attributes: true });
            });
        });
        
        // Integrate skeleton loading with existing loading functions
        const originalShowLoading = window.showLoading || function() {};
        const originalHideLoading = window.hideLoading || function() {};
        
        window.showLoading = function(element, size = 'small') {
            // Show skeleton for modals
            if (element && element.closest('.fixed.inset-0')) {
                const modal = element.closest('.fixed.inset-0');
                const modalId = modal.id;
                if (modalId) {
                    skeletonLoader.showModalSkeleton(modalId);
                }
            }
            
            // Call original loading function
            originalShowLoading(element, size);
        };
        
        window.hideLoading = function(element) {
            // Hide skeleton for modals
            if (element && element.closest('.fixed.inset-0')) {
                const modal = element.closest('.fixed.inset-0');
                const modalId = modal.id;
                if (modalId) {
                    skeletonLoader.hideModalSkeleton(modalId);
                }
            }
            
            // Call original loading function
            originalHideLoading(element);
        };
        
        // Enhanced Track Record loading with skeleton
        const originalOpenTrackRecordModal = window.openTrackRecordModal;
        if (originalOpenTrackRecordModal) {
            window.openTrackRecordModal = function() {
                // Show skeleton in track record modal
                setTimeout(() => {
                    const trackRecordModal = document.getElementById('trackRecordModal');
                    if (trackRecordModal && !trackRecordModal.classList.contains('hidden')) {
                        skeletonLoader.showModalSkeleton('trackRecordModal');
                    }
                }, 100);
                
                // Call original function
                return originalOpenTrackRecordModal.apply(this, arguments);
            };
        }
        
        // Skeleton loading for search and filter operations
        const originalProjectSearch = document.getElementById('projectSearch');
        if (originalProjectSearch) {
            let searchTimeout;
            originalProjectSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                // Show skeleton during search
                if (this.value.length > 0) {
                    searchTimeout = setTimeout(() => {
                        skeletonLoader.showSkeleton('projectsGrid', 500);
                    }, 300);
                }
            });
        }
        
        // Skeleton loading for month/year changes
        const monthSelect = document.getElementById('monthSelect');
        const yearSelect = document.getElementById('yearSelect');
        
        if (monthSelect) {
            monthSelect.addEventListener('change', function() {
                skeletonLoader.showSkeleton('projectsGrid', 800);
            });
        }
        
        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                skeletonLoader.showSkeleton('projectsGrid', 800);
            });
        }

        // --- Helper for showing the modal using the new HTML structure ---
function showCenteredConfirm(message, onConfirm, onCancel = null) {
    console.log('showCenteredConfirm called with message:', message);

    // Create a completely isolated confirmation dialog
    const confirmDialog = document.createElement('div');
    confirmDialog.id = 'isolatedConfirmDialog';
    confirmDialog.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    `;

    const dialogBox = document.createElement('div');
    dialogBox.style.cssText = `
        background: white;
        border-radius: 16px;
        padding: 32px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: confirmDialogFadeIn 0.3s ease-out;
    `;

    dialogBox.innerHTML = `
        <div style="display: flex; align-items: center; margin-bottom: 16px;">
            <div style="font-size: 24px; margin-right: 12px;">⚠️</div>
            <h3 style="font-size: 18px; font-weight: 600; color: #374151; margin: 0;">Confirm Action</h3>
        </div>
        <p style="color: #6b7280; margin-bottom: 24px; line-height: 1.5;">${message}</p>
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button id="isolatedCancelBtn" style="
                padding: 8px 16px;
                background: #e5e7eb;
                color: #374151;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: background-color 0.2s;
            ">Cancel</button>
            <button id="isolatedConfirmBtn" style="
                padding: 8px 16px;
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: background-color 0.2s;
            ">Confirm</button>
        </div>
    `;

    confirmDialog.appendChild(dialogBox);
    document.body.appendChild(confirmDialog);

    // Add hover effects
    const cancelBtn = dialogBox.querySelector('#isolatedCancelBtn');
    const confirmBtn = dialogBox.querySelector('#isolatedConfirmBtn');

    cancelBtn.addEventListener('mouseenter', () => {
        cancelBtn.style.backgroundColor = '#d1d5db';
    });
    cancelBtn.addEventListener('mouseleave', () => {
        cancelBtn.style.backgroundColor = '#e5e7eb';
    });

    confirmBtn.addEventListener('mouseenter', () => {
        confirmBtn.style.backgroundColor = '#dc2626';
    });
    confirmBtn.addEventListener('mouseleave', () => {
        confirmBtn.style.backgroundColor = '#ef4444';
    });

    function closeDialog() {
        if (confirmDialog && confirmDialog.parentNode) {
            confirmDialog.parentNode.removeChild(confirmDialog);
        }
    }

    // Event handlers
    cancelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        console.log('Cancel button clicked');
        closeDialog();
        if (onCancel) onCancel();
    });

    confirmBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        console.log('Confirm button clicked');
        closeDialog();
        if (onConfirm) onConfirm();
    });

    // Close on overlay click
    confirmDialog.addEventListener('click', (e) => {
        if (e.target === confirmDialog) {
            console.log('Overlay clicked, closing dialog');
            closeDialog();
            if (onCancel) onCancel();
        }
    });

    // ESC key handler
    const escHandler = (e) => {
        if (e.key === 'Escape') {
            console.log('ESC pressed, closing confirmation dialog');
            closeDialog();
            if (onCancel) onCancel();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);

    console.log('Isolated confirmation dialog created and shown');
}

// Archive and Delete button handlers are already defined above in the main initialization section

    </script>

<script>
// Back to Top functionality
window.addEventListener('scroll', function() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.remove('opacity-0', 'invisible');
        backToTopBtn.classList.add('opacity-100', 'visible');
    } else {
        backToTopBtn.classList.remove('opacity-100', 'visible');
        backToTopBtn.classList.add('opacity-0', 'invisible');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Loading Spinner Functions
function showButtonLoading(buttonElement, originalText) {
    if (!buttonElement) return;
    
    // Store original text if not already stored
    if (!buttonElement.dataset.originalText) {
        buttonElement.dataset.originalText = originalText || buttonElement.innerHTML;
    }
    
    // Add loading class and spinner
    buttonElement.classList.add('btn-loading');
    buttonElement.innerHTML = '<div class="loading-spinner"></div>' + (originalText || 'Loading...');
    buttonElement.disabled = true;
}

function hideButtonLoading(buttonElement, newText) {
    if (!buttonElement) return;
    
    // Remove loading class and restore text
    buttonElement.classList.remove('btn-loading');
    buttonElement.innerHTML = newText || buttonElement.dataset.originalText || 'Submit';
    buttonElement.disabled = false;
}

function showOverlayLoading(containerElement, message = 'Loading...') {
    if (!containerElement) return;
    
    // Make container relative if not already
    if (getComputedStyle(containerElement).position === 'static') {
        containerElement.style.position = 'relative';
    }
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="text-center">
            <div class="loading-spinner-large mb-2"></div>
            <div class="text-gray-600 font-medium">${message}</div>
        </div>
    `;
    overlay.id = 'loadingOverlay';
    
    // Remove existing overlay if present
    const existingOverlay = containerElement.querySelector('#loadingOverlay');
    if (existingOverlay) {
        existingOverlay.remove();
    }
    
    containerElement.appendChild(overlay);
}

function hideOverlayLoading(containerElement) {
    if (!containerElement) return;
    
    const overlay = containerElement.querySelector('#loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Enhanced AJAX wrapper with loading states
function performAjaxWithLoading(options) {
    const {
        url,
        method = 'POST',
        data,
        button,
        overlay,
        buttonText,
        overlayMessage,
        onSuccess,
        onError,
        onComplete
    } = options;
    
    // Show loading states
    if (button) showButtonLoading(button, buttonText);
    if (overlay) showOverlayLoading(overlay, overlayMessage);
    
    // Perform AJAX request
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (onSuccess) onSuccess(data);
    })
    .catch(error => {
        console.error('Ajax error:', error);
        if (onError) onError(error);
    })
    .finally(() => {
        // Hide loading states
        if (button) hideButtonLoading(button);
        if (overlay) hideOverlayLoading(overlay);
        if (onComplete) onComplete();
    });
}






</script>

<!-- Centered Confirmation Modal -->
<div id="customConfirmOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
    <div class="custom-confirm-dialog bg-white rounded-2xl shadow-xl p-8 w-full max-w-md animate-fadeInUp">
        <div class="flex items-center mb-4">
            <div class="text-3xl mr-3">⚠️</div>
            <h3 class="text-lg font-semibold text-gray-800">Confirm Action</h3>
        </div>
        <p id="customConfirmMessage" class="text-gray-600 mb-6"></p>
        <div class="flex justify-end space-x-3">
            <button id="customConfirmCancel" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button id="customConfirmOk" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Confirm</button>
        </div>
    </div>
</div>

    </div> <!-- Close main content div -->
    
         @include('projects.partials.edit_detailed_engineering_modal')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.three-dot-menu')) {
                    document.querySelectorAll('.three-dot-menu + div').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });

            // Toggle dropdown menu
            document.querySelectorAll('.three-dot-menu').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const menu = this.nextElementSibling;
                    const isHidden = menu.classList.contains('hidden');
                    
                    // Hide all other open menus
                    document.querySelectorAll('.three-dot-menu + div').forEach(m => {
                        if (m !== menu) m.classList.add('hidden');
                    });
                    
                    // Toggle current menu
                    menu.classList.toggle('hidden', !isHidden);
                });
            });

            // Handle archive and delete button clicks
            document.querySelectorAll('.archive-project-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const projectId = this.dataset.projectId;
                    const projectName = this.dataset.projectName;
                    
                    // Show confirmation dialog
                    showConfirmationDialog(
                        `Are you sure you want to archive the project "${projectName}"?`,
                        () => {
                            // Close any open dropdown menus
                            document.querySelectorAll('.three-dot-menu + div').forEach(menu => {
                                menu.classList.add('hidden');
                            });
                            
                            // Submit the archive form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/projects/${projectId}/archive`;
                            form.innerHTML = `
                                @csrf
                                @method('PATCH')
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    );
                });
            });

            document.querySelectorAll('.delete-project-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const projectId = this.dataset.projectId;
                    const projectName = this.dataset.projectName;
                    
                    // Show confirmation dialog
                    showConfirmationDialog(
                        `Are you sure you want to delete the project "${projectName}"? This action cannot be undone.`,
                        () => {
                            // Close any open dropdown menus
                            document.querySelectorAll('.three-dot-menu + div').forEach(menu => {
                                menu.classList.add('hidden');
                            });
                            
                            // Submit the delete form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/projects/${projectId}`;
                            form.innerHTML = `
                                @csrf
                                @method('DELETE')
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    );
                });
            });
        });
    </script>
    
    <!-- Project Card Click Handler -->
    <script src="{{ asset('js/project-card-handler.js') }}"></script>
</body>
</html>