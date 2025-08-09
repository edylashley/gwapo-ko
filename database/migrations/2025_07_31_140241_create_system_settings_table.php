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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Budget Control System',
                'type' => 'string',
                'description' => 'Application name displayed in the interface',
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
            [
                'key' => 'allow_user_registration',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow new users to register accounts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'require_email_verification',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Require email verification for new accounts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode for non-admin users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
