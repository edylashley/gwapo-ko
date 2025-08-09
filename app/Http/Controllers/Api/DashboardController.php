<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Engineer;
use App\Models\MonthlyAssignment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function statistics()
    {
        $totalEngineers = Engineer::active()->count();
        $activeProjects = Project::whereNull('archived_at')->count();

        // Count monthly assignments for this month instead of project assignments
        $thisMonthAssignments = MonthlyAssignment::where('year', now()->year)
            ->where('month', now()->month)
            ->count();
        $averagePerEngineer = $totalEngineers > 0 ? round($activeProjects / $totalEngineers, 1) : 0;

        return response()->json([
            'totalEngineers' => $totalEngineers,
            'activeProjects' => $activeProjects,
            'thisMonth' => $thisMonthAssignments, // Now shows monthly assignments
            'average' => $averagePerEngineer
        ]);
    }

    public function engineers()
    {
        $engineers = Engineer::active()
            ->withCount([
                'supervisedProjects as total_supervised',
                'supervisedProjects as active_supervised' => function($query) {
                    $query->whereNull('archived_at');
                },
                'monthlyAssignments as current_month_assignments' => function($query) {
                    $query->where('year', now()->year)->where('month', now()->month);
                }
            ])
            ->get()
            ->map(function($engineer) {
                return [
                    'id' => $engineer->id,
                    'name' => $engineer->name,
                    'specialization' => $engineer->specialization ?? 'General',
                    'projects' => $engineer->active_supervised,
                    'monthly_assignments' => $engineer->current_month_assignments,
                    'can_be_project_engineer' => $engineer->can_be_project_engineer,
                    'can_be_monthly_engineer' => $engineer->can_be_monthly_engineer
                ];
            });

        return response()->json($engineers);
    }

    public function budgetSummary()
    {
        $projects = Project::whereNull('archived_at')
            ->with('expenses')
            ->get()
            ->map(function($project) {
                $totalSpent = $project->expenses->sum('amount');

                return [
                    'name' => $project->name,
                    'budget' => $project->budget,
                    'spent' => $totalSpent,
                    'remaining' => $project->budget - $totalSpent
                ];
            });

        return response()->json($projects);
    }

    public function budgetAlerts()
    {
        $alerts = [];

        // Get projects with budget issues
        $projects = Project::whereNull('archived_at')
            ->with('expenses')
            ->get();

        foreach ($projects as $project) {
            $totalSpent = $project->expenses->sum('amount');
            $percentUsed = $project->budget > 0 ? ($totalSpent / $project->budget) * 100 : 0;

            if ($percentUsed > 100) {
                $alerts[] = [
                    'type' => 'danger',
                    'title' => 'Budget Exceeded',
                    'message' => "{$project->name} is " . number_format($percentUsed - 100, 1) . "% over budget",
                    'time' => 'Now'
                ];
            } elseif ($percentUsed > 90) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Budget Warning',
                    'message' => "{$project->name} has used " . number_format($percentUsed, 1) . "% of budget",
                    'time' => 'Now'
                ];
            }
        }

        // Add recent expense alerts
        $recentExpenses = \App\Models\Expense::with('project')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentExpenses as $expense) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Recent Expense',
                'message' => "₱" . number_format($expense->amount, 2) . " added to {$expense->project->name}",
                'time' => $expense->created_at->diffForHumans()
            ];
        }

        return response()->json(array_slice($alerts, 0, 5)); // Limit to 5 alerts
    }

    public function currentMonthProjects()
    {
        // Get projects that have monthly assignments for current month
        $projects = Project::with(['projectEngineer', 'monthlyAssignments' => function($query) {
                $query->with('engineer')
                    ->where('year', now()->year)
                    ->where('month', now()->month)
                    ->orderBy('is_team_head', 'desc') // Team head first
                    ->orderBy('assigned_at', 'asc');
            }])
            ->whereNull('archived_at')
            ->whereHas('monthlyAssignments', function($query) {
                $query->where('year', now()->year)->where('month', now()->month);
            })
            ->orderBy('name')
            ->get()
            ->map(function($project) {
                $status = $project->getBudgetUtilizationPercentage() >= 100 ? 'Completed' : 'Active';
                $statusClass = $status === 'Active' ? 'bg-green-500' : 'bg-blue-500';

                $teamHead = $project->monthlyAssignments->where('is_team_head', true)->first();
                $teamMembers = $project->monthlyAssignments->where('is_team_head', false);
                $teamSize = $project->monthlyAssignments->count();

                return [
                    'name' => $project->name,
                    'project_engineer' => $project->projectEngineer ? $project->projectEngineer->name : 'Unassigned',
                    'team_head' => $teamHead ? $teamHead->engineer->name : 'No Head',
                    'team_size' => $teamSize,
                    'team_members' => $teamMembers->pluck('engineer.name')->join(', '),
                    'budget' => '₱' . number_format($project->budget, 2),
                    'status' => $status,
                    'statusClass' => $statusClass,
                    'date' => $teamHead ? $teamHead->assigned_at->format('Y-m-d') : 'Not assigned'
                ];
            });

        return response()->json($projects);
    }
}
