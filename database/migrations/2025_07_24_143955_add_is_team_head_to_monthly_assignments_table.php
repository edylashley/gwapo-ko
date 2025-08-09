<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monthly_assignments', function (Blueprint $table) {
            // Add team head designation
            $table->boolean('is_team_head')->default(false)->after('assigned_at');

            // Drop the unique constraint to allow multiple engineers per project per month
            $table->dropUnique('unique_project_month');

            // Add new unique constraint to ensure only one team head per project per month
            $table->unique(['project_id', 'year', 'month', 'is_team_head'], 'unique_team_head_per_project_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_assignments', function (Blueprint $table) {
            // Drop the team head unique constraint
            $table->dropUnique('unique_team_head_per_project_month');

            // Restore the original unique constraint
            $table->unique(['project_id', 'year', 'month'], 'unique_project_month');

            // Remove the team head column
            $table->dropColumn('is_team_head');
        });
    }
};
