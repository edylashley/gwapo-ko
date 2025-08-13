
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Engineer Assignments - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
</head>
<body style="background: #064e3b;;" class="min-h-screen">

    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Monthly Team Assignments'])

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
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .techy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
    </style>

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 transition-all duration-300" style="margin-left: 256px;" id="mainContent">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white drop-shadow">Monthly Engineer Assignments</h1>
                <p class="text-green-100 mt-2">Manage monthly engineer assignments for each project</p>
            </div>
        </div>

        <!-- Month/Year Selector and Search -->
        <div class="glass-card p-6 mb-8">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label for="month" class="block text-sm font-medium text-white mb-1">Month</label>
                <select name="month" id="month" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="changeMonthYear()">
                    <option value="0" {{ $month == '0' || $month == 0 ? 'selected' : '' }}>All</option>
                    @foreach($monthOptions as $value => $label)
                        <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-white mb-1">Year</label>
                <select name="year" id="year" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="changeMonthYear()">
                    @foreach($yearOptions as $value => $label)
                        <option value="{{ $value }}" {{ $year == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-64">
                <label for="search" class="block text-sm font-medium text-white mb-1">Search Projects</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Search by project name, F/P/P code, or project engineer..."
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 placeholder-gray-500"
                    oninput="handleSearch()"
                    onkeyup="handleSearch()">
            </div>
            <div id="clearSearchContainer" style="display: none;">
                <button type="button" onclick="clearSearch()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors border border-gray-500">
                    Clear Search
                </button>
            </div>
        </div>
    </div>

        <!-- Search Results Indicator -->
        <div id="searchResultsIndicator" class="mb-6" style="display: none;">
            <div class="glass-card p-4">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <span class="font-medium">Search Results:</span>
                        <span id="searchResultsText">Found 0 projects</span>
                    </div>
                    <button onclick="clearSearch()" class="text-teal-300 hover:text-teal-100 text-sm underline">
                        Show all projects
                    </button>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid gap-6" id="projectsGrid">
            @forelse($projects as $index => $project)
                @php
                    $delayClass = 'card-delay-' . (($index % 3) + 1);
                @endphp
                <div class="glass-card p-6 project-card {{ $delayClass }}"
                     data-project-name="{{ strtolower($project->name) }}"
                     data-fpp-code="{{ strtolower($project->fpp_code ?? '') }}"
                     data-project-engineer="{{ strtolower($project->projectEngineer ? $project->projectEngineer->name : '') }}"
                     data-monthly-engineers="{{ strtolower($project->monthlyAssignments->pluck('engineer.name')->implode(' ')) }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ $project->name }}</h3>
                        <p class="text-green-100">
                            <span class="font-medium">Project Engineer:</span>
                            {{ $project->projectEngineer ? $project->projectEngineer->name : 'Not assigned' }}
                        </p>
                        <p class="text-green-100">
                            <span class="font-medium">Budget:</span> ₱{{ number_format($project->budget, 2) }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if($project->fpp_code)
                            <span class="bg-white bg-opacity-20 text-white px-2 py-1 rounded text-sm">{{ $project->fpp_code }}</span>
                        @endif
                    </div>
                </div>

                <!-- Monthly Team Assignment Section -->
                <div class="border-t pt-4">
                    @if($month == 0)
                        <!-- Show all months when "All" is selected -->
                        <h4 class="font-medium text-white mb-3">
                            Monthly Teams for {{ $year }} (All Months)
                        </h4>
                        
                        @php
                            // Group assignments by month
                            $assignmentsByMonth = $project->monthlyAssignments->groupBy('month');
                        @endphp
                        
                        @if($assignmentsByMonth->count() > 0)
                            @foreach($assignmentsByMonth as $assignmentMonth => $monthAssignments)
                                <div class="mb-6 bg-white bg-opacity-5 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h5 class="font-medium text-white">
                                            {{ \Carbon\Carbon::create($year, $assignmentMonth, 1)->format('F Y') }}
                                        </h5>
                                        @if(auth()->user()->is_admin)
                                            <button
                                                onclick="openTeamModal({{ $project->id }}, '{{ $project->name }}', {{ $year }}, {{ $assignmentMonth }})"
                                                class="bg-teal-700 hover:bg-teal-800 text-white px-3 py-1 rounded text-sm transition-colors border border-teal-600">
                                                Manage Team
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @php
                                        $teamHead = $monthAssignments->where('is_team_head', true)->first();
                                        $teamMembers = $monthAssignments->where('is_team_head', false);
                                    @endphp

                                    @include('monthly-assignments.partials.team-display', [
                                        'teamHead' => $teamHead,
                                        'teamMembers' => $teamMembers,
                                        'monthAssignments' => $monthAssignments,
                                        'project' => $project,
                                        'year' => $year,
                                        'assignmentMonth' => $assignmentMonth
                                    ])
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-6 bg-gray-50 bg-opacity-20 rounded-md">
                                <div class="text-gray-300 mb-2">No teams assigned for any month in {{ $year }}</div>
                            </div>
                        @endif
                    @else
                        <!-- Show single month when specific month is selected -->
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-white">
                                Monthly Team for {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                            </h4>
                            @if(auth()->user()->is_admin)
                                <button
                                    onclick="openTeamModal({{ $project->id }}, '{{ $project->name }}', {{ $year }}, {{ $month }})"
                                    class="bg-teal-700 hover:bg-teal-800 text-white px-3 py-1 rounded text-sm transition-colors border border-teal-600">
                                    Manage Team
                                </button>
                            @endif
                        </div>

                        @php
                            $teamAssignments = $project->monthlyAssignments;
                            $teamHead = $teamAssignments->where('is_team_head', true)->first();
                            $teamMembers = $teamAssignments->where('is_team_head', false);
                        @endphp

                        @if($teamAssignments->count() > 0)
                            @include('monthly-assignments.partials.team-display', [
                                'teamHead' => $teamHead,
                                'teamMembers' => $teamMembers,
                                'monthAssignments' => $teamAssignments,
                                'project' => $project,
                                'year' => $year,
                                'assignmentMonth' => $month
                            ])
                        @else
                            <!-- No Team Assigned -->
                            <div class="text-center py-6 bg-gray-50 bg-opacity-20 rounded-md">
                                <div class="text-gray-300 mb-2">No team assigned for this month</div>
                                @if(auth()->user()->is_admin)
                                    <button
                                        onclick="openTeamModal({{ $project->id }}, '{{ $project->name }}', {{ $year }}, {{ $month }})"
                                        class="bg-teal-700 hover:bg-teal-800 text-white px-4 py-2 rounded-md text-sm transition-colors border border-teal-600">
                                        Assign Team
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @empty
                <div class="text-center py-12">
                    @if(request('search'))
                        <div class="text-green-100 text-lg">No projects found matching "{{ request('search') }}"</div>
                        <p class="text-green-200 mt-2">Try adjusting your search terms or
                            <a href="{{ route('monthly-assignments.index', ['month' => $month, 'year' => $year]) }}"
                               class="text-teal-300 hover:text-teal-100 underline">view all projects</a>
                        </p>
                    @else
                        <div class="text-green-100 text-lg">No active projects found</div>
                        <p class="text-green-200 mt-2">Create some projects to manage monthly assignments</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

    <!-- Success Notification Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" id="successModalContent">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6" id="successMessage">Salary updated successfully.</p>
                <button onclick="closeSuccessModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    OK
                </button>
            </div>
        </div>
    </div>

@if(auth()->user()->is_admin)
    <!-- Team Management Modal (Admin Only) -->
    <div id="teamModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden transition" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
            <button id="closeTeamModal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
            <h2 id="teamModalTitle" class="text-2xl font-bold mb-6 text-gray-800">Manage Monthly Team</h2>

        <!-- Current Team Display -->
        <div id="currentTeam" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Current Team</h3>
            <div id="teamList" class="space-y-2 mb-4">
                <!-- Team members will be loaded here -->
            </div>
        </div>

        <!-- Add Engineer Form -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Add Engineer to Team</h3>
            <div class="flex gap-3 mb-4">
                <select id="newEngineerSelect" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mx-auto">
                    <option value="">Select an engineer...</option>
                    @foreach($monthlyEngineers as $engineer)
                        <option value="{{ $engineer->id }}">{{ $engineer->name }} </option>
                    @endforeach
                </select>
                <label class="flex items-center">
                    <input type="checkbox" id="isTeamHeadCheckbox" class="mr-2">
                    <span class="text-sm">Team Head</span>
                </label>
                <button onclick="addEngineerToTeam()" class="bg-green-500 hover:bg-green-700 text-black px-4 py-2 rounded-md transition-colors border border-emerald-600">
                    Add
                </button>
            </div>
        </div>
        
        
        <!-- Modal Actions -->
        <div class="flex justify-end space-x-3 border-t pt-6">
            <button onclick="closeTeamModal()" class="bg-slate-600 hover:bg-gray-300 text-black px-4 py-2 rounded-md transition-colors border border-slate-500">
                Close
            </button>
        </div>
    </div>
</div>
@endif

<!-- Success/Error Messages -->
<div id="message-container" class="fixed top-4 right-4 z-50"></div>

<script>

let currentProjectId, currentProjectName, currentYear, currentMonth;

function openTeamModal(projectId, projectName, year, month) {
    currentProjectId = projectId;
    currentProjectName = projectName;
    currentYear = year;
    currentMonth = month;

    document.getElementById('teamModalTitle').textContent = `Manage Team: ${projectName}`;
    document.getElementById('teamModal').classList.remove('hidden');

    loadCurrentTeam();
}

function closeTeamModal() {
    document.getElementById('teamModal').classList.add('hidden');
    document.getElementById('newEngineerSelect').value = '';
    document.getElementById('isTeamHeadCheckbox').checked = false;
}

function loadCurrentTeam() {
    // This will be populated by the page data, but we can also fetch fresh data
    // For now, we'll reload the page after changes to show updated data
}

function removeEngineerFromTeam(projectId, engineerId, year, month, engineerName, projectName) {
    if (!confirm(`Are you sure you want to remove "${engineerName}" from the "${projectName}" team?`)) {
        return;
    }

    fetch('{{ route("monthly-assignments.remove-engineer") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            project_id: projectId,
            engineer_id: engineerId,
            year: year,
            month: month
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Reload the page to show updated team
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMessage(data.message || 'Error removing engineer from team', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error removing engineer from team', 'error');
    });
}

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
            // Ctrl+K or Cmd+K focuses search bar (monthly-assignments uses id="search")
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                var searchInput = document.getElementById('search');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select && searchInput.select();
                }
            }
        });
        
function addEngineerToTeam() {
    const engineerSelect = document.getElementById('newEngineerSelect');
    const engineerId = engineerSelect.value;
    const isTeamHead = document.getElementById('isTeamHeadCheckbox').checked;

    if (!engineerId) {
        showMessage('Please select an engineer', 'error');
        return;
    }

    fetch('{{ route("monthly-assignments.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            project_id: currentProjectId,
            engineer_id: engineerId,
            year: currentYear,
            month: currentMonth,
            is_team_head: isTeamHead
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                closeTeamModal();
                location.reload();
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', 'error');
    });
}

function removeEngineerFromTeam(projectId, engineerId, year, month, engineerName) {
    if (!confirm(`Are you sure you want to remove ${engineerName} from the team?`)) {
        return;
    }

    fetch('{{ route("monthly-assignments.remove-engineer") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            project_id: projectId,
            engineer_id: engineerId,
            year: year,
            month: month
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', 'error');
    });
}

function setTeamHead(projectId, engineerId, year, month, engineerName) {
    if (!confirm(`Set ${engineerName} as Team Head?`)) {
        return;
    }

    fetch('{{ route("monthly-assignments.set-team-head") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            project_id: projectId,
            engineer_id: engineerId,
            year: year,
            month: month
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', 'error');
    });
}

// Event listeners
document.getElementById('closeTeamModal').addEventListener('click', closeTeamModal);

function showMessage(message, type) {
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

    const messageDiv = document.createElement('div');
    const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    messageDiv.style.cssText = `
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
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${type === 'success' ? '✅' : '❌'}</span>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(messageDiv);

    // Animate in
    requestAnimationFrame(() => {
        overlay.style.opacity = '1';
        messageDiv.style.opacity = '1';
        messageDiv.style.transform = 'translate(-50%, -50%) scale(1)';
    });

    setTimeout(() => {
        overlay.style.opacity = '0';
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translate(-50%, -50%) scale(0.8)';

        setTimeout(() => {
            if (document.body.contains(overlay)) {
                document.body.removeChild(overlay);
            }
            if (document.body.contains(messageDiv)) {
                document.body.removeChild(messageDiv);
            }
        }, 300);
    }, 3000);
}

// Smooth client-side search functionality
function handleSearch() {
    const searchInput = document.getElementById('search');
    const searchTerm = searchInput.value.toLowerCase().trim();
    const projectCards = document.querySelectorAll('.project-card');
    const searchResultsIndicator = document.getElementById('searchResultsIndicator');
    const searchResultsText = document.getElementById('searchResultsText');
    const clearSearchContainer = document.getElementById('clearSearchContainer');

    let visibleCount = 0;

    projectCards.forEach(card => {
        const projectName = card.dataset.projectName;
        const fppCode = card.dataset.fppCode;
        const projectEngineer = card.dataset.projectEngineer;
        const monthlyEngineers = card.dataset.monthlyEngineers;

        const shouldShow = searchTerm === '' ||
                          projectName.includes(searchTerm) ||
                          fppCode.includes(searchTerm) ||
                          projectEngineer.includes(searchTerm) ||
                          monthlyEngineers.includes(searchTerm);

        if (shouldShow) {
            card.style.display = 'block';
            card.style.opacity = '1';
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.style.opacity = '0';
        }
    });

    // Show/hide search results indicator and clear button
    if (searchTerm !== '') {
        searchResultsIndicator.style.display = 'block';
        clearSearchContainer.style.display = 'block';
        searchResultsText.textContent = `Found ${visibleCount} project${visibleCount !== 1 ? 's' : ''} matching "${searchInput.value}"`;

        // Show "no results" message if needed
        if (visibleCount === 0) {
            showNoResultsMessage(searchInput.value);
        } else {
            hideNoResultsMessage();
        }
    } else {
        searchResultsIndicator.style.display = 'none';
        clearSearchContainer.style.display = 'none';
        hideNoResultsMessage();
    }
}

function clearSearch() {
    const searchInput = document.getElementById('search');
    searchInput.value = '';
    handleSearch();
}

function changeMonthYear() {
    // For month/year changes, we still need to submit the form to get new data
    const month = document.getElementById('month').value;
    const year = document.getElementById('year').value;
    window.location.href = `{{ route('monthly-assignments.index') }}?month=${month}&year=${year}`;
}

function showNoResultsMessage(searchTerm) {
    hideNoResultsMessage(); // Remove existing message first

    const projectsGrid = document.getElementById('projectsGrid');
    const noResults = document.createElement('div');
    noResults.id = 'noSearchResults';
    noResults.className = 'col-span-full text-center py-12';
    noResults.innerHTML = `
        <div class="glass-card p-8">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-2xl font-bold text-white mb-2">No Projects Found</h3>
            <p class="text-green-200 mb-6">No projects match your search for "${searchTerm}".</p>
            <button onclick="clearSearch()" class="bg-teal-700 hover:bg-teal-800 text-white px-6 py-2 rounded-lg transition-colors border border-teal-600">
                Clear Search
            </button>
        </div>
    `;
    projectsGrid.appendChild(noResults);
}

function hideNoResultsMessage() {
    const noResults = document.getElementById('noSearchResults');
    if (noResults) {
        noResults.remove();
    }
}

// Apply engineer salary
function applySalary(projectId, engineerId, year, month, engineerName) {
    const salaryInput = document.getElementById(`salary_${projectId}_${engineerId}_${year}_${month}`);
    const salary = salaryInput.value;

    if (salary === '' || salary === null || parseFloat(salary) < 0) {
        alert('Please enter a valid salary amount.');
        return;
    }

    fetch('{{ route("monthly-assignments.update-salary") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            project_id: projectId,
            engineer_id: engineerId,
            year: year,
            month: month,
            salary: parseFloat(salary)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            showSuccessModal(`Salary of ₱${parseFloat(salary).toLocaleString()} applied for ${engineerName}`);
        } else {
            alert(data.message || 'Error updating salary');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating salary');
    });
}

// Show success modal
function showSuccessModal(message) {
    const modal = document.getElementById('successModal');
    const messageElement = document.getElementById('successMessage');
    const modalContent = document.getElementById('successModalContent');

    messageElement.textContent = message;
    modal.classList.remove('hidden');

    // Animate modal appearance
    setTimeout(() => {
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }, 10);
}

// Close success modal
function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    const modalContent = document.getElementById('successModalContent');

    // Animate modal disappearance
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>
    </div> <!-- Close max-w-7xl -->
    </div> <!-- Close main content -->

    <!-- Back to Top Button - Centered in main content area (accounting for sidebar) -->
    <button id="backToTopBtn" class="fixed bottom-8 bg-green-800 hover:bg-green-700 text-white px-6 py-3 rounded-full shadow-lg transition-all duration-300 z-50 opacity-0 invisible hover:scale-105 flex items-center space-x-3 pointer-events-auto" onclick="scrollToTop()" style="left: calc(50% + 128px); transform: translateX(-50%);">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
        <span class="text-base font-medium">TO TOP</span>
    </button>

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
    </script>
</body>
</html>