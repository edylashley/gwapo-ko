<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MonthlyAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'engineer_id',
        'year',
        'month',
        'assigned_at',
        'is_team_head',
        'salary',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'is_team_head' => 'boolean',
        'salary' => 'decimal:2',
    ];

    // Relationship: Project this assignment belongs to
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relationship: Engineer assigned to this project for this month
    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }

    // Scope: Assignments for a specific month/year
    public function scopeForMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // Scope: Assignments for current month
    public function scopeCurrentMonth($query)
    {
        return $query->where('year', now()->year)->where('month', now()->month);
    }

    // Scope: Team heads only
    public function scopeTeamHeads($query)
    {
        return $query->where('is_team_head', true);
    }

    // Scope: Team members only (non-heads)
    public function scopeTeamMembers($query)
    {
        return $query->where('is_team_head', false);
    }

    // Get formatted month name
    public function getMonthNameAttribute()
    {
        return Carbon::create(null, $this->month, 1)->format('F');
    }

    // Get formatted month/year
    public function getMonthYearAttribute()
    {
        return Carbon::create($this->year, $this->month, 1)->format('F Y');
    }

    // Static method to assign engineer to team
    public static function assignEngineerToTeam($projectId, $engineerId, $year, $month, $isTeamHead = false)
    {
        // Check if engineer is already assigned to this project in this month
        $existingAssignment = static::where('project_id', $projectId)
            ->where('engineer_id', $engineerId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existingAssignment) {
            throw new \Exception('Engineer is already assigned to this project for this month.');
        }

        // If assigning as team head, first remove existing team head
        if ($isTeamHead) {
            static::where('project_id', $projectId)
                ->where('year', $year)
                ->where('month', $month)
                ->where('is_team_head', true)
                ->update(['is_team_head' => false]);
        }

        return static::create([
            'project_id' => $projectId,
            'engineer_id' => $engineerId,
            'year' => $year,
            'month' => $month,
            'is_team_head' => $isTeamHead,
            'assigned_at' => now(),
        ]);
    }

    // Static method to set team head
    public static function setTeamHead($projectId, $engineerId, $year, $month)
    {
        // First, remove team head status from all engineers in this project/month
        static::where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->update(['is_team_head' => false]);

        // Then set the specified engineer as team head
        return static::where('project_id', $projectId)
            ->where('engineer_id', $engineerId)
            ->where('year', $year)
            ->where('month', $month)
            ->update(['is_team_head' => true]);
    }

    // Static method to remove engineer from team
    public static function removeEngineerFromTeam($projectId, $engineerId, $year, $month)
    {
        return static::where('project_id', $projectId)
            ->where('engineer_id', $engineerId)
            ->where('year', $year)
            ->where('month', $month)
            ->delete();
    }

    // Static method to remove all assignments for a project/month
    public static function removeAllAssignments($projectId, $year, $month)
    {
        return static::where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->delete();
    }

    // Get team for a specific project/month
    public static function getTeam($projectId, $year, $month)
    {
        return static::with('engineer')
            ->where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->orderBy('is_team_head', 'desc') // Team head first
            ->orderBy('assigned_at', 'asc')
            ->get();
    }

    // Get team head for a specific project/month
    public static function getTeamHead($projectId, $year, $month)
    {
        return static::with('engineer')
            ->where('project_id', $projectId)
            ->where('year', $year)
            ->where('month', $month)
            ->where('is_team_head', true)
            ->first();
    }
}
