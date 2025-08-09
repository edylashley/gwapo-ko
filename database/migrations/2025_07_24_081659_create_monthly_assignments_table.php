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
        Schema::create('monthly_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('engineer_id');
            $table->integer('year');
            $table->integer('month'); // 1-12
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('engineer_id')->references('id')->on('engineers')->onDelete('cascade');

            // Unique constraint: one engineer per project per month
            $table->unique(['project_id', 'year', 'month'], 'unique_project_month');

            // Index for efficient queries
            $table->index(['engineer_id', 'year', 'month']);
            $table->index(['project_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_assignments');
    }
};
