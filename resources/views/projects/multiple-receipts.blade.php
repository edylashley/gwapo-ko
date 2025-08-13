<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multiple Project Receipts</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; }
            .page-break { page-break-before: always; }
        }
        .receipt-header {
            background: linear-gradient(135deg, #065f46, #10b981);
            color: white;
        }
    </style>
    @if($isPrint ?? false)
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
    @endif
    <script>
        window.projectsSummaryData = @json($projectsData);
    </script>
</head>
<body style="background: #064e3b;" class="bg-gray-100 min-h-screen py-8">
    <!-- Action Buttons (No Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print">
        <div class="flex justify-between items-center">
            <button onclick="goBackToTrackRecords()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                ← Back to Track Records
            </button>
            <div class="flex space-x-3">
                <button onclick="downloadAllExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                    Download Excel
                </button>
                <button onclick="downloadAllPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    @if(count($projectsData) == 1)
        {{-- Single project - show detailed receipt --}}
        @php
            $data = $projectsData[0];
            $project = $data['project'];
            $expenses = $data['expenses'];
            $summary = $data['summary'];
            $monthlyAssignments = $data['monthlyAssignments'];
        @endphp

        <!-- Single Project Receipt Container -->
        <div class="max-w-4xl mx-auto bg-white shadow-lg print-container mb-8">
            <!-- Header -->
            <div class="receipt-header p-8 text-center">
                <h1 class="text-3xl font-bold mb-2">PROJECT EXPENSE RECEIPT</h1>
                <p class="text-lg opacity-90">Budget Control System</p>
                <p class="text-sm opacity-75">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
            </div>

            <!-- Project Information -->
            <div class="p-8 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $project->name }}</h2>
                        @if($project->fpp_code)
                            <div class="mb-2">
                                <span class="font-semibold text-gray-600">F/P/P Code:</span>
                                <span class="text-gray-800">{{ $project->fpp_code }}</span>
                            </div>
                        @endif
                        <div class="mb-2">
                            <span class="font-semibold text-gray-600">Created:</span>
                            <span class="text-gray-800">{{ $project->created_at->format('F d, Y') }}</span>
                        </div>
                        @if($project->projectEngineer)
                            <div class="mb-2">
                                <span class="font-semibold text-gray-600">Project Engineer:</span>
                                <span class="text-gray-800">{{ $project->projectEngineer->name }}</span>
                                @if($project->projectEngineer->specialization)
                                    <span class="text-gray-600">({{ $project->projectEngineer->specialization }})</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Budget Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Budget:</span>
                                    <span class="font-bold text-green-600">₱{{ number_format($summary['total_budget'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Spent:</span>
                                    <span class="font-bold text-red-600">₱{{ number_format($summary['total_spent'], 2) }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2">
                                    <span class="text-gray-600">Remaining:</span>
                                    <span class="font-bold {{ $summary['remaining_budget'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ₱{{ number_format($summary['remaining_budget'], 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Utilization:</span>
                                    <span class="font-bold text-purple-600">{{ number_format($summary['percent_used'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Team Assignments -->
            @if($monthlyAssignments && count($monthlyAssignments) > 0)
                <div class="p-8 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Team Assignments</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($monthlyAssignments as $month => $assignments)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-800 mb-2">{{ $month }}</h4>
                                @if(isset($assignments['head']) && $assignments['head'])
                                    <div class="mb-2">
                                        <span class="text-sm font-medium text-blue-600">Team Head:</span>
                                        <div class="text-sm text-gray-800">{{ $assignments['head']->engineer->name }}</div>
                                    </div>
                                @endif
                                @if(isset($assignments['members']) && count($assignments['members']) > 0)
                                    <div>
                                        <span class="text-sm font-medium text-green-600">Team Members:</span>
                                        <div class="text-sm text-gray-800">
                                            @foreach($assignments['members'] as $member)
                                                <div>• {{ $member->engineer->name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Expenses Table -->
            <div class="p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Expense Details</h3>
                @if($expenses->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-300 w-24">Date</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-300">Description</th>
                                    <th class="px-4 py-4 text-right text-xs font-bold text-red-700 uppercase tracking-wider border-b-2 border-gray-300 w-32 bg-red-50">Amount</th>
                                    <th class="px-4 py-4 text-right text-xs font-bold text-green-700 uppercase tracking-wider border-b-2 border-gray-300 w-36 bg-green-50">Running Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $runningBalance = $summary['total_budget']; @endphp
                                @foreach($expenses as $expense)
                                    @php $runningBalance -= $expense->amount; @endphp
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-4 text-sm text-gray-700 border-b border-gray-200 font-medium">
                                            {{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900 border-b border-gray-200">
                                            {{ $expense->description }}
                                        </td>
                                        <td class="px-4 py-4 text-base font-bold text-red-700 text-right border-b border-gray-200 bg-red-25">
                                            -₱{{ number_format($expense->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-4 text-base font-bold text-right border-b border-gray-200 {{ $runningBalance >= 0 ? 'text-green-700 bg-green-25' : 'text-red-700 bg-red-25' }}">
                                            ₱{{ number_format($runningBalance, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-sm font-bold text-gray-800 border-t-2 border-gray-300">
                                        TOTAL EXPENSES
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-red-600 text-right border-t-2 border-gray-300">
                                        -₱{{ number_format($summary['total_spent'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-right border-t-2 border-gray-300 {{ $summary['remaining_budget'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ₱{{ number_format($summary['remaining_budget'], 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-4xl mb-2">📝</div>
                        <p>No expenses recorded for this project</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 p-6 text-center text-sm text-gray-600">
                <p>This is an official receipt generated by the Budget Control System</p>
                <p class="mt-1">For questions or concerns, please contact the project management team</p>
            </div>
        </div>
    @else
        {{-- Multiple projects - show summary table --}}
        <!-- Multiple Projects Summary Container -->
        <div class="max-w-6xl mx-auto bg-white shadow-lg print-container">
            <!-- Header -->
            <div class="receipt-header p-8 text-center">
                <h1 class="text-3xl font-bold mb-2">MULTIPLE PROJECTS SUMMARY</h1>
                <p class="text-lg opacity-90">Budget Control System</p>
                <p class="text-sm opacity-75">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
                <p class="text-sm opacity-75 mt-2">{{ count($projectsData) }} Projects Selected</p>
            </div>

            <!-- Summary Table -->
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-bold text-blue-700 uppercase tracking-wider border-b-2 border-gray-300 w-24">F/P/P Code</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-300">Project Name</th>
                                <th class="px-4 py-4 text-right text-xs font-bold text-green-700 uppercase tracking-wider border-b-2 border-gray-300 w-32 bg-green-50">Budget</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-300 w-24">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b-2 border-gray-300">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($projectsData as $index => $data)
                                @php
                                    $project = $data['project'];
                                    $summary = $data['summary'];
                                    $percentUsed = $summary['percent_used'];

                                    // Determine status
                                    if ($project->archived_at) {
                                        $status = 'Archived';
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                    } elseif ($percentUsed >= 100) {
                                        $status = 'At Limit';
                                        $statusClass = 'bg-red-100 text-red-800';
                                    } elseif ($percentUsed >= 80) {
                                        $status = 'Near Limit';
                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $status = 'Active';
                                        $statusClass = 'bg-green-100 text-green-800';
                                    }

                                    // Generate remarks
                                    $remarks = [];
                                    if ($summary['remaining_budget'] < 0) {
                                        $remarks[] = 'Over budget by ₱' . number_format(abs($summary['remaining_budget']), 2);
                                    }
                                    if ($project->projectEngineer) {
                                        $remarks[] = 'Engineer: ' . $project->projectEngineer->name;
                                    }
                                    $remarksText = implode(' | ', $remarks) ?: 'No remarks';
                                @endphp
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors border-b border-gray-200">
                                    <td class="px-4 py-4 text-sm font-bold text-blue-700 bg-blue-25 whitespace-nowrap">
                                        {{ $project->fpp_code ?: 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ $project->name }}
                                    </td>
                                    <td class="px-4 py-4 text-base font-bold text-green-700 text-right bg-green-25">
                                        ₱{{ number_format($project->budget, 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $remarksText }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm font-bold text-gray-900">TOTALS:</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                    ₱{{ number_format(collect($projectsData)->sum(function($data) { return $data['project']->budget; }), 2) }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                    {{ count($projectsData) }} Projects
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    Total Spent: ₱{{ number_format(collect($projectsData)->sum(function($data) { return $data['summary']['total_spent']; }), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 p-6 text-center text-sm text-gray-600">
                <p>This is an official summary generated by the Budget Control System. For detailed project information, view individual project receipts</p>
                <p class="mt-1"></p>
            </div>
        </div>
    @endif

    <script>
        // Download all projects as Excel
        function downloadAllExcel() {
            @if(count($projectsData) == 1)
                // Single project - detailed format
                const data = [
                    ['PROJECT EXPENSE RECEIPT'],
                    ['Budget Control System'],
                    ['Generated on {{ now()->format("F d, Y \\a\\t g:i A") }}'],
                    ['']
                ];
            @else
                // Multiple projects - summary format
                const data = [
                    ['MULTIPLE PROJECTS SUMMARY'],
                    ['Budget Control System'],
                    ['Generated on {{ now()->format("F d, Y \\a\\t g:i A") }}'],
                    ['{{ count($projectsData) }} Projects Selected'],
                    [''],
                    ['F/P/P Code', 'Project Name', 'Budget', 'Status', 'Remarks']
                ];
            @endif

            @if(count($projectsData) == 1)
                {{-- Single project - detailed format --}}
                @php
                    $project = $projectsData[0]['project'];
                    $expenses = $projectsData[0]['expenses'];
                    $summary = $projectsData[0]['summary'];
                @endphp

                // Header
                data.push(['PROJECT EXPENSE RECEIPT']);
                data.push(['Budget Control System']);
                data.push(['Generated on {{ now()->format('F d, Y \\a\\t g:i A') }}']);
                data.push(['']);
                // Project Details
                data.push(['Project Details']);
                data.push(['Project Name:', '{{ $project->name }}']);
                @if($project->fpp_code)
                    data.push(['F/P/P Code:', '{{ $project->fpp_code }}']);
                @endif
                data.push(['Created Date:', '{{ $project->created_at->format('F d, Y') }}']);
                data.push(['Total Budget:', '₱{{ number_format($summary['total_budget'], 2) }}']);
                data.push(['']);
                // Budget Summary
                data.push(['Budget Summary']);
                data.push(['Direct Expenses:', '₱{{ number_format($summary['total_spent'], 2) }}']);
                data.push(['Total Spent:', '₱{{ number_format($summary['total_spent'], 2) }}']);
                data.push(['Remaining:', '₱{{ number_format($summary['remaining_budget'], 2) }}']);
                data.push(['Usage:', '{{ number_format($summary['percent_used'], 1) }}%']);
                data.push(['']);
                // Engineer Assignments
                data.push(['Engineer Assignments']);
                @if($project->projectEngineer)
                    data.push(['Project Engineer:', '{{ $project->projectEngineer->name }}']);
                    @if($project->projectEngineer->specialization)
                        data.push(['Specialization:', '{{ $project->projectEngineer->specialization }}']);
                    @endif
                @endif
                @if(isset($teamSummary) && $teamSummary)
                    data.push(['Team Summary:', '{{ $teamSummary }}']);
                @endif
                data.push(['']);
                data.push(['EXPENSE DETAILS']);
                data.push(['#', 'Date', 'Description', 'Amount', 'Running Balance']);

                @if($expenses->count() > 0)
                    @php $runningBalance = $project->budget; @endphp
                    @foreach($expenses as $i => $expense)
                        data.push([
                            {{ $i + 1 }},
                            '{{ \Carbon\Carbon::parse($expense->date)->format('m-d-y') }}',
                            '{{ $expense->description }}',
                            '₱{{ number_format($expense->amount, 2) }}',
                            '₱{{ number_format($runningBalance -= $expense->amount, 2) }}'
                        ]);
                    @endforeach
                    // Grand total row
                    data.push(['', '', 'GRAND TOTAL:', '₱{{ number_format($summary['total_spent'], 2) }}', '₱{{ number_format($summary['remaining_budget'], 2) }}']);
                @else
                    data.push(['No expenses recorded']);
                @endif
            @else
                {{-- Multiple projects - summary format --}}
                @foreach($projectsData as $index => $projectData)
                    @php
                        $project = $projectData['project'];
                        $summary = $projectData['summary'];
                        $percentUsed = $summary['percent_used'];

                        // Determine status
                        if ($project->archived_at) {
                            $status = 'Archived';
                        } elseif ($percentUsed >= 100) {
                            $status = 'At Limit';
                        } elseif ($percentUsed >= 80) {
                            $status = 'Near Limit';
                        } else {
                            $status = 'Active';
                        }

                        // Generate remarks
                        $remarks = [];
                        if ($summary['remaining_budget'] < 0) {
                            $remarks[] = 'Over budget by ₱' . number_format(abs($summary['remaining_budget']), 2);
                        }
                        if ($project->projectEngineer) {
                            $remarks[] = 'Engineer: ' . $project->projectEngineer->name;
                        }
                        $remarksText = implode(' | ', $remarks) ?: 'No remarks';
                    @endphp

                    data.push([
                        '{{ $project->fpp_code ?: 'N/A' }}',
                        '{{ $project->name }}',
                        '₱{{ number_format($project->budget, 2) }}',
                        '{{ $status }}',
                        '{{ $remarksText }}'
                    ]);
                @endforeach

                // Add totals row
                data.push(['']);
                data.push([
                    'TOTALS:',
                    '{{ count($projectsData) }} Projects',
                    '₱{{ number_format(collect($projectsData)->sum(function($data) { return $data['project']->budget; }), 2) }}',
                    '',
                    'Total Spent: ₱{{ number_format(collect($projectsData)->sum(function($data) { return $data['summary']['total_spent']; }), 2) }}'
                ]);
            @endif

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();

            @if(count($projectsData) == 1)
                XLSX.utils.book_append_sheet(wb, ws, 'Project Receipt');
                // Auto-size columns for detailed view
                const colWidths = [
                    { wch: 5 },   // #
                    { wch: 12 },  // Date
                    { wch: 30 },  // Description
                    { wch: 15 },  // Amount
                    { wch: 15 }   // Running Balance
                ];
                ws['!cols'] = colWidths;
                XLSX.writeFile(wb, 'Project_Receipt.xlsx');
            @else
                XLSX.utils.book_append_sheet(wb, ws, 'Projects Summary');
                // Auto-size columns for summary view
                const colWidths = [
                    { wch: 12 },  // F/P/P Code
                    { wch: 25 },  // Project Name
                    { wch: 15 },  // Budget
                    { wch: 12 },  // Status
                    { wch: 30 }   // Remarks
                ];
                ws['!cols'] = colWidths;
                XLSX.writeFile(wb, 'Projects_Summary.xlsx');
            @endif
        }

        // Download all projects as PDF
        function downloadAllPDF() {
            @if(count($projectsData) == 1)
                // Single project - use html2canvas + jsPDF as before
                const element = document.body;
                html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
                    const imgWidth = 210;
                    const pageHeight = 295;
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    let heightLeft = imgHeight;
                    let position = 0;
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                    }
                    pdf.save('Project_Receipt.pdf');
                });
            @else
                // Multiple projects - use html2canvas to capture the summary table visually
                const element = document.querySelector('.print-container'); // capture only the summary container
                if (!element) {
                    alert('Summary table not found!');
                    return;
                }
                // Save original styles
                const originalBoxShadow = element.style.boxShadow;
                const originalBackground = element.style.background;
                // Remove shadow and set background to white
                element.style.boxShadow = 'none';
                element.style.background = 'white';
                html2canvas(element, {
                    scale: 1,
                    useCORS: true,
                    allowTaint: true
                }).then(canvas => {
                    // Restore styles
                    element.style.boxShadow = originalBoxShadow;
                    element.style.background = originalBackground;
                    try {
                        const imgData = canvas.toDataURL('image/png');
                        const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
                        const imgWidth = 210;
                        const pageHeight = 295;
                        const imgHeight = (canvas.height * imgWidth) / canvas.width;
                        let heightLeft = imgHeight;
                        let position = 0;
                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                        while (heightLeft > 0) {
                            position = heightLeft - imgHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }
                        console.log('Saving PDF...');
                        pdf.save('Projects_Summary.pdf');
                        console.log('PDF saved.');
                    } catch (err) {
                        console.error('PDF generation failed:', err);
                        alert('PDF generation failed: ' + err.message);
                    }
                }).catch(err => {
                    console.error('html2canvas failed:', err);
                    alert('Screenshot failed: ' + err.message);
                });
            @endif
        }

        // Function to go back to track records
        function goBackToTrackRecords() {
            // Check if this window was opened from another window
            if (window.opener && !window.opener.closed) {
                // Focus the parent window (track records modal)
                window.opener.focus();
                // Close this tab
                window.close();
            } else {
                // Fallback: try to go back in history
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    // Last resort: redirect to projects page
                    window.location.href = '/projects';
                }
            }
        }
    </script>
</body>
</html>