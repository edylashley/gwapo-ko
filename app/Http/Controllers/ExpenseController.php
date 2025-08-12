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
            'expense_id' => $expense->id
        ]);

        // Get project before deletion for updating summary
        $project = $expense->project;
        
        $expense->delete();

        Log::info('Expense deleted successfully', [
            'expense_id' => $expense->id
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
}
