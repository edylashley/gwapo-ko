<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Management Dashboard - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body style="background: #064e3b;" class="min-h-screen transition-all duration-300">

    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Analytics Dashboard'])

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 transition-all duration-300" style="margin-left: 256px;"
         id="mainContent">

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

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        .glass-card.card-delay-1 { animation-delay: 0.1s; }
        .glass-card.card-delay-2 { animation-delay: 0.25s; }
        .glass-card.card-delay-3 { animation-delay: 0.4s; }
        .glass-card.card-delay-4 { animation-delay: 0.55s; }
        .glass-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 12px 32px 0 #00c6ff55;
            background: rgba(255,255,255,0.22);
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        .techy-btn {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .techy-btn:hover {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
            transform: translateY(-2px) scale(1.05);
        }

        /* Enhanced hover effects for main action buttons */
        .main-action-btn {
            position: relative;
            overflow: hidden;
        }

        .main-action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .main-action-btn:hover::before {
            left: 100%;
        }

        .main-action-btn:hover {
            cursor: pointer;
        }

        /* Add subtle pulse animation */
        @keyframes buttonPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
        }

        .main-action-btn:hover {
            animation: buttonPulse 1.5s infinite;
        }

        /* Custom scrollbar styling for engineers list */
        .engineers-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .engineers-scroll::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .engineers-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .engineers-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.4);
        }

        /* Firefox scrollbar */
        .engineers-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) rgba(0, 0, 0, 0.05);
        }

        /* Smooth scroll behavior */
        .engineers-scroll {
            scroll-behavior: smooth;
        }

    </style>

    <!-- Project Engineer Management Section -->
    <div class="max-w-7xl mx-auto mb-6">
        <!-- Engineer Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Engineers -->
            <div class="glass-card card-delay-1 flex items-center p-6">
                <div class="text-4xl mr-4">👷</div>
                <div>
                    <div class="text-2xl font-bold text-white" id="totalEngineersCard">0</div>
                    <div class="text-sm text-gray-300">Total Engineers</div>
                </div>
            </div>
            <!-- Active Projects -->
            <div class="glass-card card-delay-2 flex items-center p-6">
                <div class="text-4xl mr-4">📁</div>
                <div>
                    <div class="text-2xl font-bold text-white" id="activeProjectsCard">0</div>
                    <div class="text-sm text-gray-300">Active Projects</div>
                </div>
            </div>
            <!-- This Month Assignments -->
            <div class="glass-card card-delay-3 flex items-center p-6">
                <div class="text-4xl mr-4">📅</div>
                <div>
                    <div class="text-2xl font-bold text-white" id="thisMonthCard">0</div>
                    <div class="text-sm text-gray-300">Monthly Assignments</div>
                </div>
            </div>
            <!-- Average per Engineer -->
            <div class="glass-card card-delay-4 flex items-center p-6">
                <div class="text-4xl mr-4">📊</div>
                <div>
                    <div class="text-2xl font-bold text-white" id="averageCard">0</div>
                    <div class="text-sm text-gray-300">Avg per Engineer</div>
                </div>
            </div>
        </div>

        <!-- Quick Budget Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Active Projects Budget Status -->
            <div class="bg-white bg-opacity-20 rounded-2xl shadow-lg glass-card card-delay-1" style="height: 1151px;">
                <div class="p-6 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">Active Projects Budget Status</h2>
                        <span id="projectsCount" class="text-sm text-gray-300 bg-white bg-opacity-20 px-3 py-1 rounded-full font-medium">0</span>
                    </div>

                    <!-- Scrollable Projects Budget List -->
                    <div class="flex-1 overflow-y-auto bg-white bg-opacity-10 rounded-lg p-4 engineers-scroll min-h-0">
                        <div id="projectsBudgetSummary" class="space-y-3">
                            <!-- Budget summary will be loaded here -->
                            <div class="text-center text-white py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-3"></div>
                                <p class="text-sm">Loading projects...</p>
                            </div>
                        </div>

                        <!-- Scroll indicator (shows when scrollable) -->
                        <div id="projectsScrollIndicator" class="hidden text-center py-2 text-gray-500 text-xs border-t border-gray-300 mt-3">
                            <span>↕️ Scroll to see more projects</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Actions + Engineers -->
            <div class="flex flex-col gap-4">
                <!-- Quick Actions -->
                <div class="bg-white bg-opacity-20 rounded-2xl shadow-lg p-6 glass-card card-delay-2">
                    <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <a href="/projects" class="main-action-btn block w-full bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-300 text-center border border-emerald-600 hover:shadow-lg hover:shadow-emerald-500/50 hover:scale-105 hover:-translate-y-1 transform">
                            @if(auth()->user()->is_admin)
                                Manage Projects
                            @else
                                View Projects
                            @endif
                        </a>
                        <a href="/projects/archive" class="main-action-btn block w-full bg-slate-700 hover:bg-slate-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-300 text-center border border-slate-600 hover:shadow-lg hover:shadow-slate-500/50 hover:scale-105 hover:-translate-y-1 transform">
                            View Archive
                        </a>
                        @if(auth()->user()->is_admin)
                            <button id="addEngineerBtn" class="main-action-btn w-full bg-teal-700 hover:bg-teal-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-300 border border-teal-600 hover:shadow-lg hover:shadow-teal-500/50 hover:scale-105 hover:-translate-y-1 transform">
                                Add Engineer
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Engineers List -->
                <div class="bg-white bg-opacity-20 rounded-2xl shadow-lg glass-card card-delay-3" style="height: 867px;">
                    <div class="p-6 h-full flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">Engineers</h2>
                            <span id="engineersCount" class="text-sm text-gray-300 bg-white bg-opacity-20 px-3 py-1 rounded-full font-medium">0</span>
                        </div>

                        <!-- Scrollable Engineers List -->
                        <div class="flex-1 overflow-y-auto bg-blue-200 bg-opacity-10 rounded-lg p-4 engineers-scroll min-h-0">
                            <div id="engineersList" class="space-y-3 ">
                                <!-- Engineers will be loaded here -->
                                <div class="text-center text-white py-8 bg-blue-200 bg-opacity-10">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-3"></div>
                                    <p class="text-sm">Loading engineers...</p>
                                </div>
                            </div>

                            <!-- Scroll indicator (shows when scrollable) -->
                            <div id="scrollIndicator" class="hidden text-center py-2 text-gray-500 text-xs border-t border-gray-300 mt-3">
                                <span>↕️ Scroll to see more engineers</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Alerts & Recent Activity -->
        <div class="bg-white bg-opacity-20 rounded-2xl shadow-lg p-6 glass-card card-delay-3">
            <h2 class="text-xl font-bold text-white mb-4">Budget Alerts & Recent Activity</h2>
            <div id="budgetAlerts" class="space-y-3">
                <!-- Budget alerts and recent activity will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Add Engineer Modal -->
    <div id="addEngineerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md relative animate-fadeInUp">
            <button id="closeAddEngineerModal" class="absolute top-3 right-3 text-gray-600 hover:text-red-600 text-3xl font-bold hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-all duration-200">&times;</button>
            <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center">
                <span class="mr-2">👷</span> Add Engineer
            </h2>
            <form id="addEngineerForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Engineer Name</label>
                    <input type="text" id="engineerName" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="engineerEmail" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all duration-200" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Roles</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="canBeProjectEngineer" class="mr-2">
                            <span class="text-sm">Can be Project Engineer (Supervisor)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="canBeMonthlyEngineer" class="mr-2" checked>
                            <span class="text-sm">Can work Monthly Assignments</span>
                        </label>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <button type="button" id="cancelAddEngineer" class="flex-1 bg-slate-700 hover:bg-slate-800 text-black font-bold py-3 px-6 rounded-lg transition-all duration-200 border-2 border-slate-600 shadow-lg hover:shadow-xl">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-emerald-800 hover:bg-emerald-900 text-green-700 font-bold py-3 px-6 rounded-lg transition-all duration-200 border-2 border-emerald-700 shadow-lg hover:shadow-xl">
                        Add Engineer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass admin status to JavaScript
        const isUserAdmin = @json(auth()->user()->is_admin);

        // Simple Modal Management System for Dashboard
        const ModalManager = {
            modalStack: [],

            openModal: function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal && modal.classList.contains('hidden')) {
                    modal.classList.remove('hidden');
                    if (!this.modalStack.includes(modalId)) {
                        this.modalStack.push(modalId);
                    }
                }
            },

            closeModal: function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    const index = this.modalStack.indexOf(modalId);
                    if (index > -1) {
                        this.modalStack.splice(index, 1);
                    }
                }
            },

            closeTopModal: function() {
                if (this.modalStack.length > 0) {
                    const topModalId = this.modalStack[this.modalStack.length - 1];
                    this.closeModal(topModalId);
                    return true;
                }
                return false;
            }
        };

        // ESC key handler for modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const closed = ModalManager.closeTopModal();
                if (closed) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
        });

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
            setupEventListeners();
        });

        function initializeDashboard() {
            loadStatistics();
            loadProjectsBudgetSummary();
            loadBudgetAlerts();
        }

        function setupEventListeners() {
            // Add Engineer Modal
            document.getElementById('addEngineerBtn').addEventListener('click', function() {
                // Use ModalManager for ESC support
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.openModal('addEngineerModal');
                } else {
                    document.getElementById('addEngineerModal').classList.remove('hidden');
                }
            });

            document.getElementById('closeAddEngineerModal').addEventListener('click', function() {
                // Use ModalManager for ESC support
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.closeModal('addEngineerModal');
                } else {
                    document.getElementById('addEngineerModal').classList.add('hidden');
                }
            });

            document.getElementById('cancelAddEngineer').addEventListener('click', function() {
                // Use ModalManager for ESC support
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.closeModal('addEngineerModal');
                } else {
                    document.getElementById('addEngineerModal').classList.add('hidden');
                }
            });

            // Add Engineer Form
            document.getElementById('addEngineerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                addEngineer();
            });
        }

        function loadStatistics() {
            fetch('/api/dashboard/statistics')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalEngineersCard').textContent = data.totalEngineers;
                    document.getElementById('activeProjectsCard').textContent = data.activeProjects;
                    document.getElementById('thisMonthCard').textContent = data.thisMonth;
                    document.getElementById('averageCard').textContent = data.average;
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                    // Fallback to 0 values
                    document.getElementById('totalEngineersCard').textContent = '0';
                    document.getElementById('activeProjectsCard').textContent = '0';
                    document.getElementById('thisMonthCard').textContent = '0';
                    document.getElementById('averageCard').textContent = '0';
                });
        }

        function loadProjectsBudgetSummary() {
            fetch('/api/dashboard/budget-summary')
                .then(response => response.json())
                .then(projects => {
                    const container = document.getElementById('projectsBudgetSummary');
                    if (projects.length === 0) {
                        container.innerHTML = '<div class="text-center text-green-200 py-8">No active projects</div>';
                        return;
                    }

                    container.innerHTML = projects.map(project => {
                        const percentUsed = project.budget > 0 ? (project.spent / project.budget) * 100 : 0;
                        const isOverBudget = percentUsed > 100;
                        const isAtLimit = percentUsed >= 100 && percentUsed <= 100;
                        const isNearBudget = percentUsed >= 80 && percentUsed < 100;

                        let statusClass = 'bg-green-700';
                        let statusText = 'On Track';

                        if (isOverBudget) {
                            statusClass = 'bg-red-800';
                            statusText = 'Over Budget';
                        } else if (isAtLimit) {
                            statusClass = 'bg-red-500';
                            statusText = 'At Limit';
                        } else if (isNearBudget) {
                            statusClass = 'bg-yellow-500';
                            statusText = 'Near Limit';
                        }
                        
                        return `
                            <div class="bg-white bg-opacity-20 rounded-lg p-3 border border-white border-opacity-20 hover:bg-opacity-30 transition-all duration-200">
                                <div class="flex justify-between items-start mb-2">
                                    <h2 class="font-bold text-gray-800 text-lg">${project.name}</h2>
                                    <span class="px-2 py-1 rounded text-xs font-bold text-white ${statusClass}">${statusText}</span>
                                </div>
                                <div class="text-base text-gray-800 mb-2">
                                    Budget: ₱${project.budget.toLocaleString()} | Spent: ₱${project.spent.toLocaleString()}
                                </div>
                                <div class="w-full bg-gray-300 rounded-full h-2 mb-1">
                                    <div class="h-2 rounded-full ${statusClass}" style="width: ${Math.min(percentUsed, 100)}%"></div>
                                </div>
                                <div class="text-base text-gray-800">${percentUsed.toFixed(1)}% used</div>
                            </div>
                        `;
                    }).join('');
                })
                .catch(error => {
                    console.error('Error loading budget summary:', error);
                    document.getElementById('projectsBudgetSummary').innerHTML =
                        '<div class="text-center text-red-200 py-8">Error loading budget data</div>';
                });
        }

        function loadBudgetAlerts() {
            fetch('/api/dashboard/budget-alerts')
                .then(response => response.json())
                .then(alerts => {
                    const container = document.getElementById('budgetAlerts');
                    if (alerts.length === 0) {
                        container.innerHTML = '<div class="text-center text-green-200 py-8">No budget alerts</div>';
                        return;
                    }

                    container.innerHTML = alerts.map(alert => {
                        const iconClass = alert.type === 'danger' ? '🚨' : alert.type === 'warning' ? '⚠️' : 'ℹ️';
                        const bgClass = alert.type === 'danger' ? 'bg-red-500' :
                                       alert.type === 'warning' ? 'bg-red-500': 'bg-blue-500';

                        return `
                            <div class="flex items-start space-x-3 p-3 rounded-lg ${bgClass}">
                                <span class="text-lg">${iconClass}</span>
                                <div class="flex-1">
                                    <div class="font-bold text-white">${alert.title}</div>
                                    <div class="text-sm text-green-200">${alert.message}</div>
                                    <div class="text-xs text-green-300 mt-1">${alert.time}</div>
                                </div>
                            </div>
                        `;
                    }).join('');
                })
                .catch(error => {
                    console.error('Error loading budget alerts:', error);
                    document.getElementById('budgetAlerts').innerHTML =
                        '<div class="text-center text-red-200 py-8">Error loading alerts</div>';
                });
        }





        function addEngineer() {
            const name = document.getElementById('engineerName').value;
            const email = document.getElementById('engineerEmail').value;
            const canBeProjectEngineer = document.getElementById('canBeProjectEngineer').checked;
            const canBeMonthlyEngineer = document.getElementById('canBeMonthlyEngineer').checked;

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/engineers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    can_be_project_engineer: canBeProjectEngineer,
                    can_be_monthly_engineer: canBeMonthlyEngineer
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and reset form
                    if (typeof ModalManager !== 'undefined') {
                        ModalManager.closeModal('addEngineerModal');
                    } else {
                        document.getElementById('addEngineerModal').classList.add('hidden');
                    }
                    document.getElementById('addEngineerForm').reset();

                    // Refresh all dashboard data
                    loadStatistics();
                    loadProjectsBudgetSummary();
                    loadBudgetAlerts();
                    refreshEngineers();

                    // Show success message
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error adding engineer:', error);
                showNotification('Error adding engineer. Please try again.', 'error');
            });
        }

        function showNotification(message, type) {
            // Create backdrop overlay with blur
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                z-index: 9998;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;

            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.8);
                z-index: 9999;
                padding: 1rem 1.5rem;
                border-radius: 0.75rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                color: white;
                max-width: 24rem;
                transition: all 0.3s ease;
                opacity: 0;
                ${type === 'success' ? 'background-color: #059669;' : 'background-color: #dc2626;'}
            `;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${type === 'success' ? '✅' : '❌'}</span>
                    <span>${message}</span>
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

            // Remove notification after 1 seconds
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
            }, 1000);
        }

        // Load engineers list
        async function loadEngineers() {
            try {
                const response = await fetch('/api/engineers');
                const engineers = await response.json();

                const engineersList = document.getElementById('engineersList');
                const engineersCount = document.getElementById('engineersCount');

                if (engineers.length === 0) {
                    engineersList.innerHTML = `
                        <div class="text-center text-gray-600 py-8">
                            <div class="text-4xl mb-3">👥</div>
                            <p class="text-base font-medium mb-2">No engineers found</p>
                            <p class="text-sm text-gray-500 mb-4">${isUserAdmin ? 'Start by adding your first engineer to the team' : 'No engineers have been added yet'}</p>
                            ${isUserAdmin ? `<button onclick="document.getElementById('addEngineerBtn').click()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Add First Engineer
                            </button>` : ''}
                        </div>
                    `;
                    engineersCount.textContent = '0';
                    return;
                }

                engineersCount.textContent = engineers.length;

                engineersList.innerHTML = engineers.map(engineer => `
                    <div class="bg-green-100 hover:bg-green-100 rounded-xl p-4 transition-all duration-200 cursor-pointer group border border-gray-200 hover:border-gray-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md group-hover:shadow-lg transition-shadow">
                                    ${engineer.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div class="text-gray-800 font-semibold text-base group-hover:text-blue-700 transition-colors">${engineer.name}</div>
                                    <div class="text-gray-600 text-sm">${engineer.email || 'No email provided'}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex flex-col space-y-1">
                                    ${engineer.can_be_project_engineer ?
                                        '<div class="text-xs bg-green-300 text-green-700 px-2 py-1 rounded-full">Project Engineer</div>' :
                                        '<div class="text-xs bg-gray-300 text-gray-500 px-2 py-1 rounded-full">Team Member</div>'
                                    }
                                    ${engineer.can_be_monthly_engineer ?
                                        '<div class="text-xs bg-blue-300 text-blue-700 px-2 py-1 rounded-full">Monthly Work</div>' :
                                        ''
                                    }
                                </div>
                                <div class="text-black text-xs mt-2">Joined ${engineer.created_at ? new Date(engineer.created_at).toLocaleDateString() : 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Check if scrolling is needed and show indicator
                checkScrollIndicator();

            } catch (error) {
                console.error('Error loading engineers:', error);
                const engineersList = document.getElementById('engineersList');
                engineersList.innerHTML = `
                    <div class="text-center text-gray-600 py-8">
                        <div class="text-4xl mb-3">⚠️</div>
                        <p class="text-base font-medium mb-2 text-red-600">Error loading engineers</p>
                        <p class="text-sm text-gray-500 mb-4">Unable to fetch engineers list. Please try again.</p>
                        <button onclick="loadEngineers()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }

        // Load engineers when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadEngineers();
            addScrollBehavior();
        });

        // Check if scroll indicator should be shown
        function checkScrollIndicator() {
            const engineersContainer = document.querySelector('#engineersList').parentElement;
            const scrollIndicator = document.getElementById('scrollIndicator');

            if (engineersContainer && scrollIndicator) {
                // Check if content is scrollable
                const isScrollable = engineersContainer.scrollHeight > engineersContainer.clientHeight;

                if (isScrollable) {
                    scrollIndicator.classList.remove('hidden');

                    // Add scroll event listener to hide indicator when scrolled to bottom
                    engineersContainer.addEventListener('scroll', function() {
                        const isAtBottom = engineersContainer.scrollTop + engineersContainer.clientHeight >= engineersContainer.scrollHeight - 5;
                        if (isAtBottom) {
                            scrollIndicator.style.opacity = '1';
                            scrollIndicator.innerHTML = '<span>End of engineers list</span>';
                            scrollIndicator.style.color = 'white';
                        } else {
                            scrollIndicator.style.opacity = '1';
                            scrollIndicator.innerHTML = '<span>↕️ Scroll to see more engineers</span>';
                            scrollIndicator.style.color = 'black';
                        }
                    });
                } else {
                    scrollIndicator.classList.add('hidden');
                }
            }
        }

        // Add smooth scrolling behavior
        function addScrollBehavior() {
            const engineersContainer = document.querySelector('#engineersList').parentElement;
            if (engineersContainer) {
                // Add custom scrollbar styling
                engineersContainer.style.scrollBehavior = 'smooth';

                // Add scroll shadows for better visual feedback
                engineersContainer.addEventListener('scroll', function() {
                    const scrollTop = this.scrollTop;
                    const scrollHeight = this.scrollHeight;
                    const clientHeight = this.clientHeight;

                    // Add shadow at top when scrolled down
                    if (scrollTop > 0) {
                        this.style.boxShadow = 'inset 0 10px 10px -10px rgba(0,0,0,0.3)';
                    } else {
                        this.style.boxShadow = 'none';
                    }

                    // Add shadow at bottom when not at bottom
                    if (scrollTop + clientHeight < scrollHeight - 5) {
                        this.style.boxShadow += ', inset 0 -10px 10px -10px rgba(0,0,0,0.3)';
                    }
                });
            }
        }

        // Refresh engineers list when a new engineer is added
        function refreshEngineers() {
            loadEngineers();
        }
    </script>

    </div> <!-- Close main content div -->
</body>
</html>