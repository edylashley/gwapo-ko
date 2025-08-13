<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Expense::with('project');
        if ($request->has('project_id') && $request->project_id !== 'all') {
            $query->where('project_id', $request->project_id);
        }
        return response()->json($query->orderBy('date', 'desc')->get());
    }

    public function store(Request $request)
    {
        Log::info('Store expense request received', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Check if this is a new expense structure (5 fields) or old structure
        if ($request->has('materials') || $request->has('labor') || $request->has('fuel') || $request->has('miscellaneous') || $request->has('others')) {
            // New expense structure with 5 fields
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'materials' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'labor' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'fuel' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'miscellaneous' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'others' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            ]);

            // Calculate total amount
            $totalAmount = ($validated['materials'] ?? 0) + 
                          ($validated['labor'] ?? 0) + 
                          ($validated['fuel'] ?? 0) + 
                          ($validated['miscellaneous'] ?? 0) + 
                          ($validated['others'] ?? 0);

            if ($totalAmount <= 0) {
                return response()->json([
                    'error' => 'At least one expense amount must be greater than 0.'
                ], 422);
            }

            // Check budget constraint for total of all 5 expenses
            $project = \App\Models\Project::findOrFail($validated['project_id']);
            $currentSpent = $project->totalSpent();
            $newTotal = $currentSpent + $totalAmount;

            if ($newTotal > $project->budget) {
                $remaining = $project->budget - $currentSpent;
                return response()->json([
                    'error' => 'These expenses would exceed the project budget.',
                    'details' => [
                        'project_budget' => $project->budget,
                        'current_spent' => $currentSpent,
                        'remaining_budget' => $remaining,
                        'requested_amount' => $totalAmount,
                        'would_exceed_by' => $newTotal - $project->budget
                    ]
                ], 422);
            }

            // Create individual expense records for each of the 5 fields
            $expenses = [];
            
            // Create expense for Materials
            if (!empty($validated['materials']) && $validated['materials'] > 0) {
                $expenses[] = \App\Models\Expense::create([
                    'project_id' => $validated['project_id'],
                    'description' => 'Materials',
                    'amount' => $validated['materials'],
                    'date' => now(),
                ]);
            }
            
            // Create expense for Labor
            if (!empty($validated['labor']) && $validated['labor'] > 0) {
                $expenses[] = \App\Models\Expense::create([
                    'project_id' => $validated['project_id'],
                    'description' => 'Labor',
                    'amount' => $validated['labor'],
                    'date' => now(),
                ]);
            }
            
            // Create expense for Fuel/Oil/Equipment
            if (!empty($validated['fuel']) && $validated['fuel'] > 0) {
                $expenses[] = \App\Models\Expense::create([
                    'project_id' => $validated['project_id'],
                    'description' => 'Fuel/Oil/Equipment',
                    'amount' => $validated['fuel'],
                    'date' => now(),
                ]);
            }
            
            // Create expense for Miscellaneous & Contingencies
            if (!empty($validated['miscellaneous']) && $validated['miscellaneous'] > 0) {
                $expenses[] = \App\Models\Expense::create([
                    'project_id' => $validated['project_id'],
                    'description' => 'Miscellaneous & Contingencies',
                    'amount' => $validated['miscellaneous'],
                    'date' => now(),
                ]);
            }
            
            // Create expense for Others
            if (!empty($validated['others']) && $validated['others'] > 0) {
                $expenses[] = \App\Models\Expense::create([
                    'project_id' => $validated['project_id'],
                    'description' => 'Others',
                    'amount' => $validated['others'],
                    'date' => now(),
                ]);
            }
            
            // Use the first expense for the response (or create a summary if needed)
            $expense = $expenses[0] ?? null;
        } else {
            // Old expense structure
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
                'date' => 'required|date',
            ]);

            // Check budget constraint
            $project = \App\Models\Project::findOrFail($validated['project_id']);
            $currentSpent = $project->totalSpent();
            $newTotal = $currentSpent + $validated['amount'];

            if ($newTotal > $project->budget) {
                $remaining = $project->budget - $currentSpent;
                return response()->json([
                    'error' => 'This expense would exceed the project budget.',
                    'details' => [
                        'project_budget' => $project->budget,
                        'current_spent' => $currentSpent,
                        'remaining_budget' => $remaining,
                        'requested_amount' => $validated['amount'],
                        'would_exceed_by' => $newTotal - $project->budget
                    ]
                ], 422);
            }

            $expense = \App\Models\Expense::create($validated);
        }

        // Handle multiple expenses for new structure
        if (isset($expenses) && is_array($expenses)) {
            Log::info('Multiple expenses created successfully', [
                'expense_count' => count($expenses),
                'user_id' => Auth::id()
            ]);

            // Check if project should be auto-archived after adding these expenses
            $project = \App\Models\Project::find($validated['project_id']);
            if ($project && $project->isFullyUtilized() && !$project->force_unarchived && !$project->archived_at) {
                $project->archived_at = now();
                $project->save();

                Log::info('Project auto-archived after expenses', [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'budget_utilization' => $project->getBudgetUtilizationPercentage()
                ]);
            }

            // Return the first expense with project relationship loaded
            $firstExpense = $expenses[0] ?? null;
            if ($firstExpense) {
                $firstExpense->load('project');
                return response()->json($firstExpense, 201);
            }
        } else {
            // Single expense (old structure)
            Log::info('Expense created successfully', [
                'expense_id' => $expense->id,
                'user_id' => Auth::id()
            ]);

            // Check if project should be auto-archived after adding this expense
            $project = \App\Models\Project::find($validated['project_id']);
            if ($project && $project->isFullyUtilized() && !$project->force_unarchived && !$project->archived_at) {
                $project->archived_at = now();
                $project->save();

                Log::info('Project auto-archived after expense', [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'budget_utilization' => $project->getBudgetUtilizationPercentage()
                ]);
            }

            // Load the project relationship for the response
            $expense->load('project');
        }

        return response()->json($expense, 201);
    }

    public function update(Request $request, \App\Models\Expense $expense)
    {
        Log::info('Update expense request received', [
            'user_id' => Auth::id(),
            'expense_id' => $expense->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'date' => 'required|date',
        ]);

        // Check budget constraint (excluding current expense amount)
        $project = \App\Models\Project::findOrFail($validated['project_id']);
        $currentSpent = $project->totalSpent() - $expense->amount; // Subtract current expense
        $newTotal = $currentSpent + $validated['amount'];

        if ($newTotal > $project->budget) {
            $remaining = $project->budget - $currentSpent;
            return response()->json([
                'error' => 'This expense would exceed the project budget.',
                'details' => [
                    'project_budget' => $project->budget,
                    'current_spent' => $currentSpent,
                    'remaining_budget' => $remaining,
                    'requested_amount' => $validated['amount'],
                    'would_exceed_by' => $newTotal - $project->budget
                ]
            ], 422);
        }

        $expense->update($validated);

        Log::info('Expense updated successfully', [
            'expense_id' => $expense->id,
            'user_id' => Auth::id(),
            'changes' => $validated
        ]);

        // Get the updated project with fresh data
        $project = $expense->project->fresh();
        
        // Get all non-archived projects for summary data
        $projects = \App\Models\Project::whereNull('archived_at')
            ->whereNull('deleted_at')
            ->get();
            
        $totalBudget = $projects->sum('budget');
        $activeProjects = $projects->count();
        $avgBudget = $activeProjects > 0 ? $projects->avg('budget') : 0;
        
        // Calculate project's remaining budget and percentage used
        $projectSpent = $project->totalSpentWithDetailedEngineering();
        $projectRemaining = max(0, $project->budget - $projectSpent);
        $percentUsed = $project->budget > 0 ? min(100, ($projectSpent / $project->budget) * 100) : 0;

        // Prepare the response with summary data
        return response()->json([
            'expense' => $expense->load('project'),
            'summary' => [
                'totalProjects' => $projects->count(),
                'totalBudget' => $totalBudget,
                'activeProjects' => $activeProjects,
                'avgBudget' => $avgBudget,
                'updatedProject' => [
                    'id' => $project->id,
                    'remaining_budget' => $projectRemaining,
                    'percent_used' => $percentUsed
                ]
            ]
        ]);
    }

    public function destroy(\App\Models\Expense $expense)
    {
        Log::info('Delete expense request received', [
            'user_id' => Auth::id(),
            'expense_id' => $expense->id,
            'description' => $expense->description
        ]);

        // Get project before deletion for updating summary
        $project = $expense->project;
        
        // Check if this is a Detailed Engineering expense
        $isDetailedEngineering = $expense->description === 'Detailed Engineering';
        
        // Delete the expense
        $expense->delete();
        
        // If this was a Detailed Engineering expense, also remove team assignments
        if ($isDetailedEngineering && $project) {
            Log::info('Deleting team assignments for Detailed Engineering expense', [
                'project_id' => $project->id,
                'expense_id' => $expense->id
            ]);
            
            // Get the current month and year
            $currentYear = now()->year;
            $currentMonth = now()->month;
            
            // Delete all monthly assignments for this project in the current month
            $deleted = \App\Models\MonthlyAssignment::where('project_id', $project->id)
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->delete();
                
            Log::info('Deleted team assignments for Detailed Engineering expense', [
                'project_id' => $project->id,
                'deleted_count' => $deleted
            ]);
        }

        Log::info('Expense deleted successfully', [
            'expense_id' => $expense->id,
            'was_detailed_engineering' => $isDetailedEngineering,
            'team_assignments_deleted' => $isDetailedEngineering ? $deleted ?? 0 : 0
        ]);

        // Get all non-archived projects for summary data
        $projects = \App\Models\Project::whereNull('archived_at')
            ->whereNull('deleted_at')
            ->get();
            
        $totalBudget = $projects->sum('budget');
        $activeProjects = $projects->count();
        $avgBudget = $activeProjects > 0 ? $projects->avg('budget') : 0;
        
        // Calculate project's remaining budget and percentage used
        $project = $project->fresh(); // Refresh to get updated data
        $projectSpent = $project->totalSpentWithDetailedEngineering();
        $projectRemaining = max(0, $project->budget - $projectSpent);
        $percentUsed = $project->budget > 0 ? min(100, ($projectSpent / $project->budget) * 100) : 0;

        // Prepare the response with summary data
        return response()->json([
            'message' => 'Expense deleted successfully',
            'summary' => [
                'totalProjects' => $projects->count(),
                'totalBudget' => $totalBudget,
                'activeProjects' => $activeProjects,
                'avgBudget' => $avgBudget,
                'updatedProject' => [
                    'id' => $project->id,
                    'remaining_budget' => $projectRemaining,
                    'percent_used' => $percentUsed
                ]
            ]
        ]);
    }

    // Add this method to handle multiple expenses at once
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'expenses' => 'required|array|min:1',
            'expenses.*.description' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0.01',
        ]);

        $project = Project::findOrFail($request->project_id);
        $totalAmount = collect($request->expenses)->sum('amount');
        
        // Check budget constraint
        $currentSpent = $project->totalSpent();
        $newTotal = $currentSpent + $totalAmount;

        if ($newTotal > $project->budget) {
            return response()->json([
                'error' => 'These expenses would exceed the project budget.',
                'details' => [
                    'project_budget' => $project->budget,
                    'current_spent' => $currentSpent,
                    'remaining_budget' => $project->budget - $currentSpent,
                    'requested_amount' => $totalAmount,
                    'would_exceed_by' => $newTotal - $project->budget
                ]
            ], 422);
        }

        $createdExpenses = [];
        foreach ($request->expenses as $expenseData) {
            $createdExpenses[] = Expense::create([
                'project_id' => $request->project_id,
                'description' => $expenseData['description'],
                'amount' => $expenseData['amount'],
                'date' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Expenses created successfully',
            'expenses' => $createdExpenses
        ], 201);
    }
}
