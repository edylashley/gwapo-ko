<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Archive - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);
            min-height: 100vh;
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
        .glass-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 12px 32px 0 #ef444455;
            background: rgba(255,255,255,0.22);
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .techy-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .techy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card-delay-1 { animation-delay: 0.1s; }
        .card-delay-2 { animation-delay: 0.2s; }
        .card-delay-3 { animation-delay: 0.3s; }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .glass-card {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Custom Confirmation Dialog Styles */
        .confirmation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .confirmation-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .confirmation-dialog {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.8) translateY(20px);
            transition: all 0.3s ease;
        }

        .confirmation-overlay.show .confirmation-dialog {
            transform: scale(1) translateY(0);
        }

        .confirmation-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1rem;
            text-align: center;
        }

        .confirmation-message {
            color: #4b5563;
            margin-bottom: 2rem;
            text-align: center;
            line-height: 1.5;
        }

        .confirmation-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .confirmation-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 100px;
        }

        .confirmation-btn-confirm {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .confirmation-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .confirmation-btn-cancel {
            background: rgba(107, 114, 128, 0.1);
            color: #4b5563;
            border: 1px solid rgba(107, 114, 128, 0.3);
        }

        .confirmation-btn-cancel:hover {
            background: rgba(107, 114, 128, 0.2);
            transform: translateY(-1px);
        }

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
    </style>
</head>
<body style="background: #064e3b;;" class="min-h-screen">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Project Archive'])

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 transition-all duration-300" style="margin-left: 256px;" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Info Banner -->
        <div class="glass-card card-delay-1 p-6 mb-8">
            <div>
                <h2 class="text-xl font-bold text-white mb-2">Completed Projects Archive</h2>
                <p class="text-green-100">Projects that have reached 100% or more budget utilization or have been manually archived. These projects are completed and archived for reference.</p>
            </div>
        </div>

        <!-- Archive Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-card card-delay-1 p-6 text-center">
                <div class="text-2xl font-bold text-white">{{ $totalArchivedProjects }}</div>
                <div class="text-green-200 text-sm">Archived Projects</div>
            </div>
            <div class="glass-card card-delay-2 p-6 text-center">
                <div class="text-2xl font-bold text-white">₱{{ number_format($totalArchivedBudget, 2) }}</div>
                <div class="text-green-200 text-sm">Total Budget</div>
            </div>
            <div class="glass-card card-delay-3 p-6 text-center">
                <div class="text-2xl font-bold text-white">₱{{ number_format($totalArchivedSpent, 2) }}</div>
                <div class="text-green-200 text-sm">Total Spent</div>
            </div>
            <div class="glass-card card-delay-1 p-6 text-center">
                <div class="text-2xl font-bold text-white">
                    {{ $totalArchivedBudget > 0 ? number_format(($totalArchivedSpent / $totalArchivedBudget) * 100, 1) : 0 }}%
                </div>
                <div class="text-green-200 text-sm">Avg. Utilization</div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="glass-card card-delay-2 p-6 mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search archived projects by name or F/P/P code..."
                           class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 border border-white border-opacity-30 text-white placeholder-green-200 focus:outline-none focus:border-green-300 focus:bg-opacity-30 transition-all duration-200">
                </div>
                <button id="clearSearch" class="techy-btn px-6 py-3 text-white font-semibold rounded-lg">
                    Clear
                </button>
            </div>
        </div>

        <!-- Archived Projects Grid -->
        @if($archivedProjects->count() > 0)
            <div id="archivedProjectsGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($archivedProjects as $project)
                    @php
                        $totalSpent = $project->totalSpentWithDetailedEngineering();
                        $remaining = $project->remainingBudgetWithDetailedEngineering();
                        $percentUsed = $project->budget > 0 ? ($totalSpent / $project->budget) * 100 : 0;
                        $completionStatus = $project->getCompletionStatus();
                        $isOverBudget = $totalSpent > $project->budget;
                    @endphp
                    
                    <div class="glass-card card-delay-{{ ($loop->index % 3) + 1 }} p-6 archive-project-card"
                         data-project-id="{{ $project->id }}"
                         data-project-name="{{ strtolower($project->name) }}"
                         data-fpp-code="{{ strtolower($project->fpp_code ?? '') }}">
                        
                        <!-- Project Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white mb-2">{{ $project->name }}</h3>
                                @if($project->fpp_code)
                                    <div class="text-green-200 text-sm mb-2">
                                        <span class="font-semibold">F/P/P Code:</span> {{ $project->fpp_code }}
                                    </div>
                                @endif
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $completionStatus['class'] }} bg-black ">
                                        {{ $completionStatus['message'] }}
                                    </span>
                                    <span class="text-yellow font-bold">{{ number_format($percentUsed, 1) }}%</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-white">₱{{ number_format($project->budget, 2) }}</div>
                                <div class="text-green-200 text-sm">Total Budget</div>
                            </div>
                        </div>

                        <!-- Budget Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-green-200 mb-1">
                                <span>Budget Utilization</span>
                                <span>{{ number_format($percentUsed, 1) }}%</span>
                            </div>
                            <div class="w-full bg-white bg-opacity-20 rounded-full h-3">
                                <div class="h-3 rounded-full {{ $isOverBudget ? 'bg-red-500' : 'bg-green-500' }}" 
                                     style="width: {{ min($percentUsed, 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Budget Details -->
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="text-center bg-white bg-opacity-10 rounded-lg p-3">
                                <div class="text-lg font-bold {{ $isOverBudget ? 'text-red-300' : 'text-yellow-200' }}">
                                    ₱{{ number_format($totalSpent, 2) }}
                                </div>
                                <div class="text-xs text-blue-200">Total Spent</div>
                            </div>
                            <div class="text-center bg-white bg-opacity-10 rounded-lg p-3">
                                <div class="text-lg font-bold {{ $remaining < 0 ? 'text-red-300' : 'text-green-200' }}">
                                    ₱{{ number_format($remaining, 2) }}
                                </div>
                                <div class="text-xs text-blue-200">Remaining</div>
                            </div>
                            <div class="text-center bg-white bg-opacity-10 rounded-lg p-3">
                                <div class="text-lg font-bold text-blue-200">
                                    {{ $project->expenses->count() }}
                                </div>
                                <div class="text-xs text-blue-200">Expenses</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-5">
    <button type="button" class="receipt-btn flex-1 bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 border border-slate-600 ease-in-out transform hover:-translate-y-1"
        data-project-id="{{ $project->id }}">
        Receipt
    </button>
    @if(auth()->user()->is_admin)
    <button type="button"
        class="unarchive-btn flex-1 bg-teal-700 hover:bg-teal-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 border border-teal-600 ease-in-out transform hover:-translate-y-1"
        data-project-id="{{ $project->id }}"
        data-project-name="{{ $project->name }}">
        Unarchive
    </button>
@endif
</div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card card-delay-1 text-center py-16">
                <h3 class="text-2xl font-bold text-white mb-4">No Archived Projects Yet</h3>
                <p class="text-green-200 mb-6">Projects will appear here automatically when they reach 100% budget utilization or when manually archived.</p>
                <a href="/projects" class="techy-btn px-8 py-3 text-white font-bold rounded-lg">
                    View Active Projects
                </a>
            </div>
        @endif
    </div>

    <script>
// Global variable for receipt modal state
var currentReceiptData = null;
// --- Receipt Modal Logic (copied from index.blade.php, exact match for archive page) ---

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
    receiptModal.classList.remove('hidden');
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
document.addEventListener('DOMContentLoaded', function() {
    // --- Search Bar Functionality ---
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const projectCards = document.querySelectorAll('.archive-project-card');
    if (searchInput && clearSearchBtn && projectCards.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            projectCards.forEach(card => {
                const projectName = card.dataset.projectName;
                const fppCode = card.dataset.fppCode;
                if (projectName.includes(searchTerm) || fppCode.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            projectCards.forEach(card => {
                card.style.display = 'block';
            });
        });
    }

    // --- Receipt and Unarchive Button Event Delegation ---
    (function() {
        var grid = document.getElementById('archivedProjectsGrid');
        if (grid) {
            grid.addEventListener('click', function(e) {
                const receiptBtn = e.target.closest('.receipt-btn');
                if (receiptBtn) {
                    const projectId = receiptBtn.dataset.projectId;
                    openReceiptModal(projectId);
                    return;
                }
                const unarchiveBtn = e.target.closest('.unarchive-btn');
                if (unarchiveBtn) {
                    const projectId = unarchiveBtn.dataset.projectId;
                    const projectName = unarchiveBtn.dataset.projectName;
                    if (projectId && projectName) {
                        showConfirmation(
                            `Are you sure you want to unarchive "${projectName}"? It will be moved back to active projects.`,
                            () => unarchiveProject(projectId, projectName)
                        );
                    } else {
                        console.error('Unarchive button missing projectId or projectName');
                    }
                    return;
                }
            });
        } else {
            console.warn('archivedProjectsGrid not found: Unarchive and Receipt buttons will not work.');
        }
        // Defensive: Remove any global calls to showConfirmation or unarchiveProject outside event handlers
    })();

    // Fallback: Direct event listener for all .unarchive-btn buttons
    document.querySelectorAll('.unarchive-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const projectId = btn.dataset.projectId;
            const projectName = btn.dataset.projectName;
            console.log('Direct .unarchive-btn click:', projectId, projectName);
            if (projectId && projectName) {
                showConfirmation(
                    `Are you sure you want to unarchive "${projectName}"? It will be moved back to active projects.`,
                    () => unarchiveProject(projectId, projectName)
                );
            } else {
                console.error('Unarchive button missing projectId or projectName');
            }
        });
    });

    // Defensive: Attach logout handler only if element exists
    var logoutForm = document.getElementById('logout-form');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function() {
            var spinner = document.getElementById('logout-spinner');
            var btnText = document.getElementById('logout-btn-text');
            var btn = document.getElementById('logout-btn');
            if (spinner) spinner.classList.remove('hidden');
            if (btnText) btnText.textContent = 'Logging out...';
            if (btn) btn.setAttribute('disabled', 'disabled');
        });
    }
    
    // ...existing code...
});

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

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
    
}
// --- End Receipt Modal Logic ---


// Print, PDF, Excel export functions (match main project page)
function printReceipt() {
    const receiptContent = document.getElementById('receiptContent');
    if (receiptContent) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Project Receipt</title>
                <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
                <style>
                    @media print {
                        .no-print { display: none !important; }
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
    if (window.currentReceiptData) {
        if (currentReceiptData.type === 'single') {
            window.open(`/projects/${currentReceiptData.projectId}/receipt`, '_blank');
        } else if (currentReceiptData.type === 'multiple') {
            // For multiple receipts, submit a form
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
            if (window.currentReceiptData) {
                // Show info notification; Excel export is handled in the receipt view
                showNotification('Excel download functionality will be implemented in the receipt view.', 'info', 1000);
            }
        }



        // Success notification with blur effect
        function showNotification(message, type = 'success', duration = 1000) {
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

            const notification = document.createElement('div');
            const bgColor = type === 'success' ? '#059669' : type === 'error' ? '#dc2626' : '#2563eb';
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';

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
                background-color: ${bgColor};
            `;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${icon}</span>
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

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearch');
            const projectCards = document.querySelectorAll('.archive-project-card');

            if (searchInput && clearSearchBtn && projectCards.length > 0) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    projectCards.forEach(card => {
                        const projectName = card.dataset.projectName;
                        const fppCode = card.dataset.fppCode;
                        if (projectName.includes(searchTerm) || fppCode.includes(searchTerm)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });

                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    projectCards.forEach(card => {
                        card.style.display = 'block';
                    });
                });
            } else {
                console.warn('Search bar or project cards not found.');
            }
        });




 

        // Unarchive project function (admin only)
// Always define the function, but check admin at runtime
async function unarchiveProject(projectId, projectName) {
    // Only allow if user is admin
    var isAdmin = "{{ auth()->user()->is_admin ? 'true' : 'false' }}";
    if (isAdmin !== "true") {
        showNotification('You do not have permission to unarchive projects.', 'error');
        console.error('Unarchive attempted by non-admin user.');
        return;
    }

    try {
        // Find the button and disable it
        const btn = document.querySelector(`.unarchive-btn[data-project-id="${projectId}"]`);
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Unarchiving...';
        }
        const response = await fetch(`/projects/${projectId}/unarchive`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        });

        console.log('Unarchive response:', response);
        if (!response.ok) {
            throw new Error(`Failed to unarchive: ${response.status}`);
        }

        showNotification(`Project "${projectName}" has been unarchived successfully!`);
        // Remove any lingering confirmation overlays
        document.querySelectorAll('.confirmation-overlay').forEach(el => el.remove());
        // Remove the project card from DOM
        const card = document.querySelector(`.archive-project-card[data-project-id="${projectId}"]`);
        if (card) {
            card.remove();
            // If no more archived project cards, show empty state
            if (document.querySelectorAll('.archive-project-card').length === 0) {
                const grid = document.getElementById('archivedProjectsGrid');
                if (grid) {
                    grid.outerHTML = `
                        <div class="glass-card card-delay-1 text-center py-16">
                            <h3 class="text-2xl font-bold text-white mb-4">No Archived Projects Yet</h3>
                            <p class="text-green-200 mb-6">Projects will appear here automatically when they reach 100% budget utilization or when manually archived.</p>
                            <a href="/projects" class="techy-btn px-8 py-3 text-white font-bold rounded-lg">
                                View Active Projects
                            </a>
                        </div>
                    `;
                }
            }
        } else {
            showNotification('Project card not found in DOM.', 'error');
            console.error('Project card not found for removal:', projectId);
        }
    } catch (error) {
        console.error('Unarchive error:', error);
        showNotification(`Failed to unarchive "${projectName}".`, 'error');
    } finally {
        // Restore button state
        const btn = document.querySelector(`.unarchive-btn[data-project-id="${projectId}"]`);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Unarchive';
        }
    }
}


        // Logout form handling
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function() {
                const spinner = document.getElementById('logout-spinner');
                const btnText = document.getElementById('logout-btn-text');
                const btn = document.getElementById('logout-btn');
                if (spinner) spinner.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Logging out...';
                if (btn) btn.setAttribute('disabled', 'disabled');
            });
        }

    
        // Receipt Modal Close Button Handler
        document.addEventListener('DOMContentLoaded', function() {

            var closeBtn = document.getElementById('closeReceiptModal');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeReceiptModal);
            }
            // Fallback: event delegation for close button
            var receiptModal = document.getElementById('receiptModal');
            if (receiptModal) {
                receiptModal.addEventListener('click', function(e) {
                    if (e.target && e.target.id === 'closeReceiptModal') {
                        closeReceiptModal();
                    }
                });
            }
        });

        // Global keyboard shortcuts for modals and search
        document.addEventListener('keydown', function(e) {
            // ESC closes any open modal
            if (e.key === 'Escape') {
                // Find all open modals (class 'fixed inset-0' and not hidden)
                document.querySelectorAll('.fixed.inset-0').forEach(function(modal) {
                    if (!modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
            }
            // Ctrl+K or Cmd+K focuses archive search bar
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                var searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select && searchInput.select();
                }
            }
        });
    // Custom centered confirmation dialog for archive page
    // Custom centered confirmation dialog for archive page
    function showConfirmation(message, onConfirm, onCancel = null) {
        const overlay = document.createElement('div');
        overlay.className = 'confirmation-overlay fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40';
        overlay.innerHTML = `
            <div class="confirmation-dialog">
                <div class="flex items-center mb-4">
                    <div class="text-3xl mr-3">⚠️</div>
                    <h3 class="text-lg font-semibold text-gray-800">Confirm Action</h3>
                </div>
                <p class="confirmation-message">${message}</p>
                <div class="confirmation-buttons">
                    <button class="confirmation-btn confirmation-btn-cancel">Cancel</button>
                    <button class="confirmation-btn confirmation-btn-confirm">Confirm</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
        overlay.classList.add('show');
        // Cancel button
        overlay.querySelector('.confirmation-btn-cancel').addEventListener('click', () => {
            document.body.removeChild(overlay);
            if (onCancel) onCancel();
        });
        // Confirm button
        overlay.querySelector('.confirmation-btn-confirm').addEventListener('click', () => {
            console.log('Confirmation dialog: Confirm button clicked');
            document.body.removeChild(overlay);
            if (onConfirm) {
                console.log('Confirmation dialog: onConfirm callback will be called');
                onConfirm();
            }
        });
        // Dismiss on overlay click (outside dialog)
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                document.body.removeChild(overlay);
                if (onCancel) onCancel();
            }
        });
    }

</script>

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

                    <button id="closeReceiptModal" onclick="closeReceiptModal()" class="text-gray-400 hover:text-red-500 text-2xl bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">&times;</button>
                </div>
            </div>
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <div id="receiptContent">
                    <!-- Receipt content will be loaded here -->
                </div>
            </div>
        </div>
    </div> <!-- Close max-w-7xl -->
    </div> <!-- Close main content -->
</body>
</html>