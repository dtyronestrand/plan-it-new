<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calendar;
use App\Services\GoogleCalendarService;

class RenewGoogleWatchChannels extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'google:renew-watch';

    /**
     * The console command description.
     */
    protected $description = 'Renew Google Calendar watch channels (webhooks) to prevent expiration.';

    /**
     * Execute the console command.
     */
    public function handle(GoogleCalendarService $calendarService)
    {
        $this->info('Starting Google Calendar watch renewal...');

        // Find all calendars that have a channel_id (meaning they are currently connected)
        // We limit to those updated > 24 hours ago to avoid spamming Google, 
        // or just renew everyone to be safe. Here we renew everyone with an active connection.
        $calendars = Calendar::whereNotNull('google_channel_id')
                             ->whereNotNull('google_resource_id')
                             ->with('user')
                             ->get();

        foreach ($calendars as $calendar) {
            $user = $calendar->user;

            if (!$user || !$user->google_access_token) {
                continue;
            }

            try {
                $this->info("Renewing for User: {$user->email}");

                // 1. Stop the old channel (good citizenship to avoid hitting quotas)
                $calendarService->stopWatching($user, $calendar);

                // 2. Start a new watch channel
                $calendarService->watchCalendar($user, $calendar);

            } catch (\Exception $e) {
                $this->error("Failed to renew for {$user->email}: " . $e->getMessage());
            }
        }

        $this->info('Renewal process complete.');
    }
}