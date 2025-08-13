
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Receipt - {{ $project->name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                overflow: visible !important;
            }
            body {
                position: relative !important;
            }
            .print-container {
                box-shadow: none !important;
                position: absolute !important;
                top: 10px !important;
                left: 50% !important;
                transform: translateX(-50%) scale(0.85) !important;
                transform-origin: top center !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 800px !important;
                max-width: none !important;
                background: white !important;
            }
        }
        .receipt-header {
            background: linear-gradient(135deg, #065f46, #10b981);
            color: white;
        }

        /* Better text wrapping for long project names */
        .project-name-text {
            word-break: break-word;
            hyphens: auto;
            line-height: 1.4;
            max-width: 100%;
        }

        @page {
            margin: 5mm;
            size: A4;
        }

        /* Force single page layout */
        @media print {
            .print-container {
                max-height: 90vh !important;
                overflow: hidden !important;
                transform-origin: top center !important;
            }

            /* Strict table height limit */
            .expense-table tbody {
                max-height: 200px !important;
                overflow: hidden !important;
                display: block !important;
            }

            .expense-table thead,
            .expense-table tbody tr {
                display: table !important;
                width: 100% !important;
                table-layout: fixed !important;
            }

            /* Prevent page breaks */
            * {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .receipt-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Force content to fit */
            html, body {
                overflow: hidden !important;
            }
        }

        /* Ultra-compact layout for single page printing */
        @media print {
            .print-container {
                width: 700px !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
            }

            /* Ultra-compact header */
            .receipt-header {
                padding: 8px !important;
            }
            .receipt-header h1 {
                font-size: 16px !important;
                margin-bottom: 2px !important;
                line-height: 1.1 !important;
            }
            .receipt-header p {
                font-size: 9px !important;
                margin: 1px 0 !important;
                line-height: 1.1 !important;
            }

            /* Ultra-compact sections */
            .receipt-section {
                padding: 6px 12px !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            /* Ultra-compact text sizes */
            .section-title {
                font-size: 11px !important;
                margin-bottom: 4px !important;
                font-weight: bold !important;
            }

            .detail-item {
                margin-bottom: 2px !important;
                font-size: 9px !important;
                line-height: 1.1 !important;
            }

            .detail-label {
                font-size: 8px !important;
            }

            .detail-value {
                font-size: 9px !important;
            }

            /* Ultra-compact table */
            .expense-table {
                font-size: 8px !important;
                margin-top: 4px !important;
            }
            .expense-table th {
                padding: 2px 1px !important;
                font-size: 7px !important;
                line-height: 1 !important;
            }
            .expense-table td {
                padding: 1px 2px !important;
                font-size: 8px !important;
                line-height: 1.1 !important;
            }

            /* Minimize all spacing */
            .space-y-3 > * + * {
                margin-top: 2px !important;
            }
            .space-y-4 > * + * {
                margin-top: 3px !important;
            }
            .space-y-2 > * + * {
                margin-top: 1px !important;
            }
            .mb-1 {
                margin-bottom: 1px !important;
            }
            .mb-2 {
                margin-bottom: 2px !important;
            }
            .mb-4 {
                margin-bottom: 3px !important;
            }
            .mb-6 {
                margin-bottom: 4px !important;
            }
            .p-8 {
                padding: 6px 12px !important;
            }
            .p-4 {
                padding: 3px !important;
            }
            .py-2 {
                padding-top: 1px !important;
                padding-bottom: 1px !important;
            }
            .px-2 {
                padding-left: 2px !important;
                padding-right: 2px !important;
            }
            .gap-4 {
                gap: 8px !important;
            }
            .gap-8 {
                gap: 6px !important;
            }

            /* Compact grid layout */
            .grid-cols-2 {
                grid-template-columns: 1fr 1fr !important;
            }

            /* Ultra-compact engineer assignments */
            .engineer-assignments {
                padding: 4px 8px !important;
                font-size: 8px !important;
            }

            .engineer-assignments .grid {
                gap: 8px !important;
            }

            .engineer-assignments .font-semibold {
                font-size: 8px !important;
                margin-bottom: 1px !important;
            }

            .engineer-assignments .font-medium {
                font-size: 8px !important;
                line-height: 1.1 !important;
            }

            .engineer-assignments .text-xs {
                font-size: 7px !important;
                line-height: 1 !important;
            }

            /* Compact borders */
            .border-b {
                border-bottom-width: 1px !important;
            }
            .border-t {
                border-top-width: 1px !important;
            }
        }

        /* Orientation-specific adjustments */
        @media print and (orientation: portrait) {
            .print-container {
                width: 700px !important;
            }
        }

        @media print and (orientation: landscape) {
            .print-container {
                width: 900px !important;
            }
        }
    </style>
    @if(request()->has('print') && request()->get('print') == 'true')
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    </script>
    @endif
</head>
<body style="background: #064e3b;" class="bg-gray-100 min-h-screen py-8">
    <!-- Action Buttons (No Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print">
        <div class="flex justify-between items-center">
            <button onclick="goBackToTrackRecords()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                ← Back to Track Records
            </button>
            <div class="flex space-x-3">
                <button onclick="downloadExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2">
                    <span>Download Excel</span>
                </button>
                <button onclick="downloadPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2">
                    <span>Download PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Receipt Container -->
    <div id="receiptContainer" class="max-w-4xl mx-auto bg-white shadow-lg print-container receipt-content">
        <!-- Header -->
        <div class="receipt-header p-8 text-center">
            <h1 class="text-3xl font-bold mb-2">PROJECT EXPENSE RECEIPT</h1>
            <p class="text-lg opacity-90">Budget Control System</p>
            <p class="text-sm opacity-75">Generated on {{ now()->format('m-d-y \a\t g:i A') }}</p>
        </div>

        <!-- Project Information -->
        <div class="p-8 border-b border-gray-200 receipt-section">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4 section-title">Project Details</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="font-medium text-gray-600 block mb-1">Project Name:</span>
                            <span class="font-semibold text-gray-900 block text-sm leading-relaxed project-name-text">{{ $project->name }}</span>
                        </div>
                        @if($project->fpp_code)
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-600 whitespace-nowrap">F/P/P Code:</span>
                                <span class="font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded whitespace-nowrap text-right">{{ $project->fpp_code }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Created Date:</span>
                            <span class="font-semibold text-gray-900">{{ $project->created_at->format('m-d-y') }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <span class="font-medium text-gray-600">Total Budget:</span>
                            <span class="text-xl font-bold text-green-700">₱{{ number_format($project->budget, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4 section-title">Budget Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Direct Expenses:</span>
                            <span class="text-lg font-bold text-red-600">₱{{ number_format($summary['total_spent'], 2) }}</span>
                        </div>
                        @if($summary['detailed_engineering_cost'] > 0)
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Detailed Engineering:</span>
                            <span class="text-lg font-bold text-red-600">₱{{ number_format($summary['detailed_engineering_cost'], 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center border-t pt-3">
                            <span class="font-medium text-gray-600">Total Spent:</span>
                            <span class="text-lg font-bold text-red-600">₱{{ number_format($summary['total_spent_with_engineering'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-600">Remaining:</span>
                            <span class="text-lg font-bold {{ $summary['remaining_budget'] < 0 ? 'text-red-600' : 'text-green-600' }}">₱{{ number_format($summary['remaining_budget'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <span class="font-medium text-gray-600">Usage:</span>
                            <span class="text-xl font-bold {{ $summary['percent_used'] >= 100 ? 'text-red-600' : ($summary['percent_used'] >= 80 ? 'text-yellow-600' : 'text-green-600') }}">{{ number_format($summary['percent_used'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engineer Assignments Section (Compact) -->
        <div class="p-8 border-b border-gray-200 receipt-section engineer-assignments">
            <h2 class="text-xl font-bold text-gray-800 mb-4 section-title">Engineer Assignments</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Project Engineer -->
                <div>
                    <div class="font-semibold text-gray-700 mb-1">Project Engineer:</div>
                    @if($project->projectEngineer)
                        <div class="text-blue-800 font-medium">{{ $project->projectEngineer->name }}</div>
                        <div class="text-gray-600 text-xs">{{ $project->projectEngineer->specialization }}</div>
                    @else
                        <div class="text-gray-500 italic text-xs">Not assigned</div>
                    @endif
                </div>

                <!-- Team Summary -->
                <div>
                    <div class="font-semibold text-gray-700 mb-1">Team Summary:</div>
                    @if($monthlyAssignments->count() > 0)
                        @php
                            $totalEngineers = 0;
                            $months = [];
                            foreach($monthlyAssignments as $monthKey => $assignments) {
                                $totalEngineers += $assignments->count();
                                $months[] = \Carbon\Carbon::create(substr($monthKey, 0, 4), substr($monthKey, 5, 2), 1)->format('m-y');
                            }
                        @endphp
                        <div class="text-green-800 font-medium">{{ $totalEngineers }} Engineers</div>
                        <div class="text-gray-600 text-xs">{{ implode(', ', $months) }}</div>
                    @else
                        <div class="text-gray-500 italic text-xs">No team assignments</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="p-8 receipt-section">
            <h2 class="text-xl font-bold text-gray-800 mb-4 section-title">Expense Details</h2>

            @if($expenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 expense-table">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-2 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-300 w-8">#</th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-300 w-20">Date</th>
                                <th class="px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-300">Description</th>
                                <th class="px-2 py-2 text-right text-xs font-bold text-red-700 uppercase tracking-wider border-b-2 border-gray-300 w-24 bg-red-50">Amount</th>
                                <th class="px-2 py-2 text-right text-xs font-bold text-green-700 uppercase tracking-wider border-b-2 border-gray-300 w-28 bg-green-50">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $runningBalance = $project->budget; @endphp
                            @foreach($expenses as $index => $expense)
                                @php $runningBalance -= $expense->amount; @endphp
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="px-2 py-2 text-xs text-gray-600 border-b border-gray-200 text-center font-medium">{{ $index + 1 }}</td>
                                    <td class="px-2 py-2 text-xs text-gray-700 border-b border-gray-200 font-medium">{{ $expense->date->format('m-d-y') }}</td>
                                    <td class="px-2 py-2 text-xs text-gray-900 border-b border-gray-200">{{ $expense->description }}</td>
                                    <td class="px-2 py-2 text-xs font-bold text-red-700 text-right border-b border-gray-200 bg-red-25">-₱{{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-2 py-2 text-xs font-bold text-right border-b border-gray-200 {{ $runningBalance < 0 ? 'text-red-700 bg-red-25' : 'text-green-700 bg-green-25' }}">₱{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @endforeach
                            @if($summary['detailed_engineering_cost'] > 0)
                                @php $runningBalance -= $summary['detailed_engineering_cost']; @endphp
                                <tr class="{{ ($expenses->count() % 2 == 0) ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="px-2 py-2 text-xs text-gray-600 border-b border-gray-200 text-center font-medium">{{ $expenses->count() + 1 }}</td>
                                    <td class="px-2 py-2 text-xs text-gray-700 border-b border-gray-200 font-medium">{{ now()->format('m-d-y') }}</td>
                                    <td class="px-2 py-2 text-xs text-gray-900 border-b border-gray-200">Detailed Engineering</td>
                                    <td class="px-2 py-2 text-xs font-bold text-red-700 text-right border-b border-gray-200 bg-red-25">-₱{{ number_format($summary['detailed_engineering_cost'], 2) }}</td>
                                    <td class="px-2 py-2 text-xs font-bold text-right border-b border-gray-200 {{ $runningBalance < 0 ? 'text-red-700 bg-red-25' : 'text-green-700 bg-green-25' }}">₱{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-gray-200">
                            <tr>
                                <td colspan="3" class="px-2 py-3 text-xs font-bold text-gray-900 border-t-2 border-gray-400 uppercase tracking-wide">GRAND TOTAL:</td>
                                <td class="px-2 py-3 text-xs font-bold text-red-700 text-right border-t-2 border-gray-400 bg-red-50">-₱{{ number_format($summary['total_spent_with_engineering'], 2) }}</td>
                                <td class="px-2 py-3 text-xs font-bold text-right border-t-2 border-gray-400 {{ $summary['remaining_budget'] < 0 ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50' }}">₱{{ number_format($summary['remaining_budget'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-xl font-bold text-gray-600 mb-2">No Expenses Recorded</h3>
                    <p class="text-gray-500">This project doesn't have any expenses yet.</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-6 text-center border-t border-gray-200">
            <p class="text-xs text-gray-500 mt-1">Generated by {{ auth()->user()->name ?? 'System Administrator' }}</p>

        </div>
    </div>

    <script>
        // Download as Excel
        function downloadExcel() {
            const projectName = "{{ $project->name }}";
            const data = [
                ['PROJECT EXPENSE RECEIPT'],
                [''],
                ['Project Name:', projectName],
                @if($project->fpp_code)
                    ['F/P/P Code:', "{{ $project->fpp_code }}"],
                @endif
                ['Created Date:', "{{ $project->created_at->format('m-d-y') }}"],
                ['Total Budget:', "₱{{ number_format($project->budget, 2) }}"],
                ['Total Spent:', "₱{{ number_format($summary['total_spent'], 2) }}"],
                ['Remaining:', "₱{{ number_format($summary['remaining_budget'], 2) }}"],
                ['Usage:', "{{ number_format($summary['percent_used'], 1) }}%"],
                [''],
                  // Engineer Assignments Section
                ['ENGINEER ASSIGNMENTS'],
                @if($project->projectEngineer)
                    ['Project Engineer:', "{{ $project->projectEngineer->name }}"],
                    @if($project->projectEngineer->specialization)
                        ['Specialization:', "{{ $project->projectEngineer->specialization }}"],
                    @endif
                @else
                    ['Project Engineer:', 'Not assigned'],
                @endif
                @if(isset($teamSummary) && $teamSummary)
                    ['Team Summary:', "{{ $teamSummary }}"],
                @endif
                [''],
                ['EXPENSE DETAILS'],
                ['#', 'Date', 'Description', 'Amount', 'Running Balance']
            ];

            @if($expenses->count() > 0)
                @php $runningBalance = $project->budget; @endphp
                @foreach($expenses as $index => $expense)
                    @php $runningBalance -= $expense->amount; @endphp
                    data.push([
                        {{ $index + 1 }},
                        "{{ $expense->date->format('m-d-y') }}",
                        "{{ $expense->description }}",
                        "₱{{ number_format($expense->amount, 2) }}",
                        "₱{{ number_format($runningBalance, 2) }}"
                    ]);
                @endforeach
                @if($summary['detailed_engineering_cost'] > 0)
                    @php $runningBalance -= $summary['detailed_engineering_cost']; @endphp
                    data.push([
                        {{ $expenses->count() + 1 }},
                        "{{ now()->format('m-d-y') }}",
                        "Detailed Engineering",
                        "₱{{ number_format($summary['detailed_engineering_cost'], 2) }}",
                        "₱{{ number_format($runningBalance, 2) }}"
                    ]);
                @endif
            @endif

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Project Receipt');
            
            // Auto-size columns
            const colWidths = [
                { wch: 5 },   // #
                { wch: 12 },  // Date
                { wch: 30 },  // Description
                { wch: 15 },  // Amount
                { wch: 15 }   // Running Balance
            ];
            ws['!cols'] = colWidths;

            XLSX.writeFile(wb, `${projectName.replace(/[^a-zA-Z0-9]/g, '_')}_Receipt.xlsx`);
        }

        // Download as PDF
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('receiptContainer');
            
            html2canvas(element, {
                scale: 2,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                
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

                const projectName = "{{ $project->name }}";
                pdf.save(`${projectName.replace(/[^a-zA-Z0-9]/g, '_')}_Receipt.pdf`);
            });
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
