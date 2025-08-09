<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Carbon\Carbon;

class CleanupDeletedProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:cleanup-deleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete projects that have been soft deleted for more than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of deleted projects...');

        // Find projects deleted more than 30 days ago
        $cutoffDate = Carbon::now()->subDays(30);
        
        $projectsToDelete = Project::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->get();

        if ($projectsToDelete->isEmpty()) {
            $this->info('No projects found for cleanup.');
            return 0;
        }

        $this->info("Found {$projectsToDelete->count()} projects to permanently delete:");

        foreach ($projectsToDelete as $project) {
            $this->line("- {$project->name} (deleted on {$project->deleted_at->format('Y-m-d H:i:s')})");
        }

        if ($this->confirm('Do you want to permanently delete these projects?')) {
            $deletedCount = 0;

            foreach ($projectsToDelete as $project) {
                try {
                    // Force delete all associated expenses first
                    $expenseCount = $project->expenses()->onlyTrashed()->count();
                    $project->expenses()->onlyTrashed()->forceDelete();
                    
                    // Then force delete the project
                    $project->forceDelete();
                    
                    $this->info("✓ Permanently deleted project: {$project->name} (with {$expenseCount} expenses)");
                    $deletedCount++;
                } catch (\Exception $e) {
                    $this->error("✗ Failed to delete project: {$project->name} - {$e->getMessage()}");
                }
            }

            $this->info("Cleanup completed. {$deletedCount} projects permanently deleted.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
