<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Engineer;
use App\Models\MonthlyAssignment;

class MonthlyAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some projects and engineers
        $projects = Project::whereNull('archived_at')->take(3)->get();
        $engineers = Engineer::where('can_be_monthly_engineer', true)->get();

        if ($projects->count() > 0 && $engineers->count() > 0) {
            // Create assignments for current month
            foreach ($projects as $index => $project) {
                $engineer = $engineers[$index % $engineers->count()];

                MonthlyAssignment::create([
                    'project_id' => $project->id,
                    'engineer_id' => $engineer->id,
                    'year' => now()->year,
                    'month' => now()->month,
                    'assigned_at' => now(),
                ]);
            }

            // Create some assignments for last month too
            foreach ($projects as $index => $project) {
                $engineer = $engineers[($index + 1) % $engineers->count()];

                MonthlyAssignment::create([
                    'project_id' => $project->id,
                    'engineer_id' => $engineer->id,
                    'year' => now()->subMonth()->year,
                    'month' => now()->subMonth()->month,
                    'assigned_at' => now()->subMonth(),
                ]);
            }
        }
    }
}
