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
      Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique();
        $table->text('google_access_toaken')->nullable();
        $table->text('google_refresh_token')->nullable();
      });

        Schema::table('calendars', function (Blueprint $table) {
            $table->string('google_calendar_id')->nullable()->comment('ID of the calendar in Google Calendar');
            $table->text('google_sync_token')->nullable()->comment('Sync token for Google Calendar synchronization');
            $table->string('google_channel_id')->nullable()->comment('Channel ID for Google Calendar webhook');
            $table->string('google_resource_id')->nullable()->comment('Resource ID for Google Calendar webhook');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->index()->comment('ID of the event in Google Calendar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['google_id', 'google_access_token', 'google_refresh_token']);
    });

    Schema::table('calendars', function (Blueprint $table) {
        $table->dropColumn(['google_calendar_id', 'google_sync_token', 'google_channel_id', 'google_resource_id']);
    });

    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['google_event_id']);
    });
    }
};
