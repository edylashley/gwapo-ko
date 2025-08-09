<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEngineer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'specialization',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship with projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Get active engineers only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get project count for this engineer
    public function getProjectCountAttribute()
    {
        return $this->projects()->count();
    }

    // Get active project count for this engineer
    public function getActiveProjectCountAttribute()
    {
        return $this->projects()->whereNull('archived_at')->count();
    }

    // Get projects assigned this month
    public function getThisMonthProjectsAttribute()
    {
        return $this->projects()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
}
