<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'budget',
        'fpp_code',
        'project_engineer_id',
        'assigned_at',
        'archived_at',
        'force_unarchived',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'assigned_at' => 'datetime',
        'archived_at' => 'datetime',
        'force_unarchived' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relationship with expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Relationship with project engineer (supervisor)
    public function projectEngineer()
    {
        return $this->belongsTo(Engineer::class, 'project_engineer_id');
    }

        // Relationship with monthly assignments
    public function monthlyAssignments()
    {
        return $this->hasMany(MonthlyAssignment::class);
    }

    // Accessor for current month's detailed engineering team (with salary)
    public function getDetailedEngineeringTeamAttribute()
    {
        $year = now()->year;
        $month = now()->month;
        return $this->monthlyAssignments()
            ->with('engineer')
            ->where('year', $year)
            ->where('month', $month)
            ->whereNotNull('salary')
            ->get()
            ->map(function($assignment) {
                return [
                    'id' => $assignment->engineer_id,
                    'name' => $assignment->engineer ? $assignment->engineer->name : '',
                    'salary' => $assignment->salary
                ];
            });
    }

    // Override delete method to soft delete expenses as well
    public function delete()
    {
        // Soft delete all associated expenses first
        $this->expenses()->delete();

        // Then soft delete the project
        return parent::delete();
    }

    // Custom method to restore project and its related expenses
    public function restoreWithExpenses()
    {
        Log::info("Starting restore for project: {$this->name} (ID: {$this->id})");

        // Store the project's deletion time for comparison
        $projectDeletedAt = $this->deleted_at;

        Log::info("Project deleted at: {$projectDeletedAt}");

        // Restore only this specific project using Laravel's built-in restore
        $restored = parent::restore();

        Log::info("Project restore result: " . ($restored ? 'success' : 'failed'));

        // Only restore expenses that were deleted at approximately the same time as the project
        // (within 1 minute to account for processing time)
        if ($projectDeletedAt && $restored) {
            $expensesToRestore = $this->expenses()
                ->onlyTrashed()
                ->whereBetween('deleted_at', [
                    $projectDeletedAt->copy()->subMinute(),
                    $projectDeletedAt->copy()->addMinute()
                ])
                ->get();

            Log::info("Found " . $expensesToRestore->count() . " expenses to restore");

            $this->expenses()
                ->onlyTrashed()
                ->whereBetween('deleted_at', [
                    $projectDeletedAt->copy()->subMinute(),
                    $projectDeletedAt->copy()->addMinute()
                ])
                ->update(['deleted_at' => null]);
        }

        return $restored;
    }

    // Calculate total spent on this project
    public function totalSpent()
    {
        return $this->expenses()->sum('amount');
    }

    // Calculate remaining budget
    public function remainingBudget()
    {
        return $this->budget - $this->totalSpent();
    }

    // Check if project is over budget
    public function isOverBudget()
    {
        return $this->totalSpentWithDetailedEngineering() > $this->budget;
    }

    // Check if project has reached 100% budget utilization (ready for archive)
    public function isFullyUtilized()
    {
        if ($this->budget <= 0) {
            return false;
        }

        $percentUsed = ($this->totalSpentWithDetailedEngineering() / $this->budget) * 100;
        return $percentUsed >= 100;
    }

    // Get budget utilization percentage
    public function getBudgetUtilizationPercentage()
    {
        if ($this->budget <= 0) {
            return 0;
        }

        return ($this->totalSpentWithDetailedEngineering() / $this->budget) * 100;
    }

    // Calculate total detailed engineering cost (sum of all engineer salaries)
    public function getDetailedEngineeringCost()
    {
        return $this->monthlyAssignments()
            ->whereNotNull('salary')
            ->sum('salary');
    }

    // Calculate total spent including detailed engineering
    public function totalSpentWithDetailedEngineering()
    {
        return $this->totalSpent() + $this->getDetailedEngineeringCost();
    }

    // Calculate remaining budget including detailed engineering
    public function remainingBudgetWithDetailedEngineering()
    {
        return $this->budget - $this->totalSpentWithDetailedEngineering();
    }

    // Check if project is eligible for archive (100% or more budget used OR manually archived)
    // But not if it was force unarchived
    public function isArchiveEligible()
    {
        // If project was force unarchived, don't auto-archive it again
        if ($this->force_unarchived) {
            return $this->isManuallyArchived();
        }

        return $this->isFullyUtilized() || $this->isManuallyArchived();
    }

    // Check if project is manually archived
    public function isManuallyArchived()
    {
        return !is_null($this->archived_at);
    }

    // Archive project manually
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    // Unarchive project
    public function unarchive()
    {
        $this->archived_at = null;

        // If this project was automatically archived (100% budget),
        // set force_unarchived to prevent it from being auto-archived again
        if ($this->isFullyUtilized()) {
            $this->force_unarchived = true;
        }

        $this->save();
    }

    // Get completion status for archive display
    public function getCompletionStatus()
    {
        $percentUsed = $this->getBudgetUtilizationPercentage();

        // Check if manually archived first
        if ($this->isManuallyArchived()) {
            return [
                'status' => 'manually_archived',
                'percentage' => $percentUsed,
                'message' => 'Manually Archived',
                'class' => 'text-purple-500'
            ];
        }

        if ($percentUsed >= 100) {
            return [
                'status' => 'completed',
                'percentage' => $percentUsed,
                'message' => $percentUsed > 100 ? 'Over Budget' : 'Budget Fully Utilized',
                'class' => $percentUsed > 100 ? 'text-red-500' : 'text-green-500'
            ];
        }

        return [
            'status' => 'active',
            'percentage' => $percentUsed,
            'message' => 'In Progress',
            'class' => 'text-blue-500'
        ];
    }

    // Get monthly team for specific month/year
    public function getMonthlyTeam($year, $month)
    {
        return MonthlyAssignment::getTeam($this->id, $year, $month);
    }

    // Get monthly team head for specific month/year
    public function getMonthlyTeamHead($year, $month)
    {
        return MonthlyAssignment::getTeamHead($this->id, $year, $month);
    }

    // Get current month team
    public function getCurrentMonthTeam()
    {
        return $this->getMonthlyTeam(now()->year, now()->month);
    }

    // Get current month team head
    public function getCurrentMonthTeamHead()
    {
        return $this->getMonthlyTeamHead(now()->year, now()->month);
    }

    // Legacy method for backward compatibility
    public function getMonthlyAssignment($year, $month)
    {
        return $this->getMonthlyTeamHead($year, $month);
    }

    // Legacy method for backward compatibility
    public function getCurrentMonthAssignment()
    {
        return $this->getCurrentMonthTeamHead();
    }

    // Assign engineer to team for specific month
    public function assignEngineerToTeam($engineerId, $year, $month, $isTeamHead = false)
    {
        return MonthlyAssignment::assignEngineerToTeam($this->id, $engineerId, $year, $month, $isTeamHead);
    }

    // Set team head for specific month
    public function setTeamHead($engineerId, $year, $month)
    {
        return MonthlyAssignment::setTeamHead($this->id, $engineerId, $year, $month);
    }

    // Remove engineer from team
    public function removeEngineerFromTeam($engineerId, $year, $month)
    {
        return MonthlyAssignment::removeEngineerFromTeam($this->id, $engineerId, $year, $month);
    }

    // Remove all monthly assignments
    public function removeAllMonthlyAssignments($year, $month)
    {
        return MonthlyAssignment::removeAllAssignments($this->id, $year, $month);
    }

    // Legacy methods for backward compatibility
    public function assignMonthlyEngineer($engineerId, $year, $month)
    {
        return $this->assignEngineerToTeam($engineerId, $year, $month, true); // Assign as team head
    }

    public function removeMonthlyAssignment($year, $month)
    {
        return $this->removeAllMonthlyAssignments($year, $month);
    }

    // Get all assignments for this project grouped by month
    public function getMonthlyAssignmentsGrouped()
    {
        return $this->monthlyAssignments()
            ->with('engineer')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy(function ($assignment) {
                return $assignment->year . '-' . str_pad($assignment->month, 2, '0', STR_PAD_LEFT);
            });
    }
}