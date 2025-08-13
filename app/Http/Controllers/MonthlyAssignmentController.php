<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Engineer;
use App\Models\MonthlyAssignment;
use Carbon\Carbon;

class MonthlyAssignmentController extends Controller
{
    // Show monthly assignment management page
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Get all projects with monthly assignments for the selected month/year
        $projects = Project::with(['projectEngineer', 'monthlyAssignments' => function($query) use ($year, $month) {
            $query->with('engineer')
                ->where('year', $year);
            
            // Only filter by month if not "All" (month != 0)
            if ($month != 0) {
                $query->where('month', $month);
            }
            
            $query->orderBy('is_team_head', 'desc') // Team head first
                ->orderBy('assigned_at', 'asc');
        }])
        ->whereNull('archived_at')
        ->orderBy('name')
        ->get();

        // Get all engineers who can work monthly assignments
        $monthlyEngineers = Engineer::canBeMonthlyEngineer()->orderBy('name')->get();

        // Generate month options for the dropdown
        $monthOptions = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthOptions[$i] = Carbon::create(null, $i, 1)->format('F');
        }

        // Generate year options (current year ± 2 years)
        $yearOptions = [];
        for ($i = now()->year - 2; $i <= now()->year + 2; $i++) {
            $yearOptions[$i] = $i;
        }

        return view('monthly-assignments.index', compact(
            'projects',
            'monthlyEngineers',
            'year',
            'month',
            'monthOptions',
            'yearOptions'
        ));
    }

    // Assign engineer to project team for specific month
    public function assign(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:engineers,id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'is_team_head' => 'boolean',
            'salary' => 'nullable|numeric|min:0',
        ]);

        try {
            $isTeamHead = $request->get('is_team_head', false);

            $assignment = MonthlyAssignment::assignEngineerToTeam(
                $request->project_id,
                $request->engineer_id,
                $request->year,
                $request->month,
                $isTeamHead
            );

            $engineer = Engineer::find($request->engineer_id);
            $project = Project::find($request->project_id);
            $monthName = Carbon::create($request->year, $request->month, 1)->format('F Y');
            $role = $isTeamHead ? 'Team Head' : 'Team Member';

            return response()->json([
                'success' => true,
                'message' => "Engineer '{$engineer->name}' assigned as {$role} to '{$project->name}' for {$monthName}",
                'assignment' => $assignment->load('engineer')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning engineer. Please try again.'
            ], 500);
        }
    }

    // Set team head for a project/month
    public function setTeamHead(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:engineers,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            MonthlyAssignment::setTeamHead(
                $request->project_id,
                $request->engineer_id,
                $request->year,
                $request->month
            );

            $engineer = Engineer::find($request->engineer_id);
            $project = Project::find($request->project_id);
            $monthName = Carbon::create($request->year, $request->month, 1)->format('F Y');

            return response()->json([
                'success' => true,
                'message' => "'{$engineer->name}' is now Team Head for '{$project->name}' in {$monthName}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting team head. Please try again.'
            ], 500);
        }
    }

    // Remove engineer from team
    public function removeEngineer(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:engineers,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            // Get the assignment before removing to check if it has a salary
            $assignment = MonthlyAssignment::where('project_id', $request->project_id)
                ->where('engineer_id', $request->engineer_id)
                ->where('year', $request->year)
                ->where('month', $request->month)
                ->first();

            $salaryAmount = $assignment ? $assignment->salary : 0;

            MonthlyAssignment::removeEngineerFromTeam(
                $request->project_id,
                $request->engineer_id,
                $request->year,
                $request->month
            );

            $engineer = Engineer::find($request->engineer_id);
            $project = Project::find($request->project_id);
            $monthName = Carbon::create($request->year, $request->month, 1)->format('F Y');

            $message = "'{$engineer->name}' removed from '{$project->name}' team for {$monthName}";
            if ($salaryAmount > 0) {
                $message .= ". Salary of ₱" . number_format($salaryAmount, 2) . " returned to project budget.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'salary_returned' => $salaryAmount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing engineer. Please try again.'
            ], 500);
        }
    }

    // Remove all assignments for a project/month
    public function remove(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            MonthlyAssignment::removeAllAssignments(
                $request->project_id,
                $request->year,
                $request->month
            );

            $project = Project::find($request->project_id);
            $monthName = Carbon::create($request->year, $request->month, 1)->format('F Y');

            return response()->json([
                'success' => true,
                'message' => "All assignments removed from '{$project->name}' for {$monthName}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing assignments. Please try again.'
            ], 500);
        }
    }

    // Update engineer salary
    public function updateSalary(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'engineer_id' => 'required|exists:engineers,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'salary' => 'required|numeric|min:0',
        ]);

        try {
            $assignment = MonthlyAssignment::where('project_id', $request->project_id)
                ->where('engineer_id', $request->engineer_id)
                ->where('year', $request->year)
                ->where('month', $request->month)
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found'
                ], 404);
            }

            $assignment->update(['salary' => $request->salary]);

            $engineer = Engineer::find($request->engineer_id);
            $project = Project::find($request->project_id);

            return response()->json([
                'success' => true,
                'message' => "Salary updated for '{$engineer->name}' in '{$project->name}'",
                'assignment' => $assignment->load('engineer'),
                'detailed_engineering_cost' => $project->getDetailedEngineeringCost()
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating salary']);
        }
    }
    

    // Create salary and team assignment from expense modal
    public function createSalaryAssignment(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_engineer_id' => 'required|exists:engineers,id',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:engineers,id',
            'team_head' => 'required|exists:engineers,id',
            'individual_salaries' => 'required|array',
            'individual_salaries.*' => 'required|numeric|min:0',
        ]);

        try {
            $currentYear = now()->year;
            $currentMonth = now()->month;

            // Create or update project engineer assignment
            $project = Project::find($request->project_id);
            $project->update(['project_engineer_id' => $request->project_engineer_id]);

            // Calculate total salary from individual salaries
            $individualSalaries = $request->individual_salaries;
            $totalSalary = 0;
            
            foreach ($request->team_members as $engineerId) {
                if (isset($individualSalaries[$engineerId])) {
                    $totalSalary += floatval($individualSalaries[$engineerId]);
                }
            }

            // Create monthly assignments for all team members with individual salaries
            foreach ($request->team_members as $engineerId) {
                $isTeamHead = ($engineerId == $request->team_head);
                $individualSalary = floatval($individualSalaries[$engineerId] ?? 0);
                
                MonthlyAssignment::updateOrCreate(
                    [
                        'project_id' => $request->project_id,
                        'engineer_id' => $engineerId,
                        'year' => $currentYear,
                        'month' => $currentMonth,
                    ],
                    [
                        'is_team_head' => $isTeamHead,
                        'salary' => $individualSalary,
                        'assigned_at' => now(),
                    ]
                );
            }

            // Create expense record for Detailed Engineering (sixth expense)
            \App\Models\Expense::create([
                'project_id' => $request->project_id,
                'description' => 'Detailed Engineering',
                'amount' => $totalSalary,
                'date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Salary and team assignment created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating salary and team assignment. Please try again.'
            ], 500);
        }
    }

    // Update salary and team assignment
    public function updateSalaryAssignment(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_engineer_id' => 'required|exists:engineers,id',
            'edit_team_members' => 'required|array|min:1',
            'edit_team_members.*' => 'exists:engineers,id',
            'edit_team_head' => 'required|exists:engineers,id',
            'edit_individual_salaries' => 'required|array',
            'edit_individual_salaries.*' => 'required|numeric|min:0',
        ]);

        try {
            $currentYear = now()->year;
            $currentMonth = now()->month;

            // Update project engineer
            $project = Project::find($request->project_id);
            $project->update(['project_engineer_id' => $request->project_engineer_id]);

            // Calculate total salary from individual salaries
            $individualSalaries = $request->edit_individual_salaries;
            $totalSalary = 0;
            
            foreach ($request->edit_team_members as $engineerId) {
                if (isset($individualSalaries[$engineerId])) {
                    $totalSalary += floatval($individualSalaries[$engineerId]);
                }
            }

            // Remove all existing assignments for this project/month
            MonthlyAssignment::where('project_id', $request->project_id)
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->delete();

            // Create new monthly assignments for all team members with individual salaries
            foreach ($request->edit_team_members as $engineerId) {
                $isTeamHead = ($engineerId == $request->edit_team_head);
                $individualSalary = floatval($individualSalaries[$engineerId] ?? 0);
                
                MonthlyAssignment::create([
                    'project_id' => $request->project_id,
                    'engineer_id' => $engineerId,
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'is_team_head' => $isTeamHead,
                    'salary' => $individualSalary,
                    'assigned_at' => now(),
                ]);
            }

            // Update or create the "Detailed Engineering" expense
            $existingExpense = \App\Models\Expense::where('project_id', $request->project_id)
                ->where('description', 'Detailed Engineering')
                ->first();

            if ($existingExpense) {
                $existingExpense->update(['amount' => $totalSalary]);
            } else {
                \App\Models\Expense::create([
                    'project_id' => $request->project_id,
                    'description' => 'Detailed Engineering',
                    'amount' => $totalSalary,
                    'date' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Team and salary assignment updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating team and salary assignment. Please try again.'
            ], 500);
        }
    }
}
