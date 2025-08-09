<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Engineer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'specialization',
        'can_be_project_engineer',
        'can_be_monthly_engineer',
        'is_active',
    ];

    protected $casts = [
        'can_be_project_engineer' => 'boolean',
        'can_be_monthly_engineer' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationship: Projects where this engineer is the project engineer (supervisor)
    public function supervisedProjects()
    {
        return $this->hasMany(Project::class, 'project_engineer_id');
    }

    // Relationship: Monthly assignments where this engineer works
    public function monthlyAssignments()
    {
        return $this->hasMany(MonthlyAssignment::class);
    }

    // Scope: Only active engineers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: Engineers who can be project engineers
    public function scopeCanBeProjectEngineer($query)
    {
        return $query->where('can_be_project_engineer', true)->where('is_active', true);
    }

    // Scope: Engineers who can be monthly engineers
    public function scopeCanBeMonthlyEngineer($query)
    {
        return $query->where('can_be_monthly_engineer', true)->where('is_active', true);
    }

    // Get count of projects this engineer supervises
    public function getSupervisedProjectCountAttribute()
    {
        return $this->supervisedProjects()->whereNull('archived_at')->count();
    }

    // Get count of monthly assignments for current month
    public function getCurrentMonthAssignmentsAttribute()
    {
        return $this->monthlyAssignments()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();
    }

    // Get monthly assignments for a specific month/year
    public function getMonthlyAssignments($year, $month)
    {
        return $this->monthlyAssignments()
            ->with('project')
            ->where('year', $year)
            ->where('month', $month)
            ->get();
    }
}
