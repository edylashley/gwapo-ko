<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove default_currency and default_timezone settings from system_settings table
        DB::table('system_settings')->whereIn('key', ['default_currency', 'default_timezone'])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the removed settings if migration is rolled back
        DB::table('system_settings')->insert([
            [
                'key' => 'default_currency',
                'value' => 'PHP',
                'type' => 'string',
                'description' => 'Default currency for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_timezone',
                'value' => 'Asia/Manila',
                'type' => 'string',
                'description' => 'Default timezone for the application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
