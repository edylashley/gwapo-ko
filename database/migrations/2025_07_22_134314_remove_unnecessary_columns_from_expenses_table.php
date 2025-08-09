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
        Schema::table('expenses', function (Blueprint $table) {
            // Remove unnecessary columns
            $table->dropColumn([
                'receipt',
                'status',
                'rejection_reason',
                'approved_at',
                'approved_by'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add back the columns if needed to rollback
            $table->string('receipt')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
        });
    }
};
