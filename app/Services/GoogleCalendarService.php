<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Calendar;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GoogleCalendarService
{
    protected $baseUrl = 'https://www.googleapis.com/calendar/v3';

    /**
     * Sync the user's primary calendar to a specific PlanIt calendar.
     */
    public function syncEvents(User $user, Calendar $calendar)
    {
        // 1. Link this calendar to Google's "primary" calendar if not already
        if ($calendar->google_calendar_id !== 'primary') {
            $calendar->update([
                'google_calendar_id' => 'primary'
            ]);
        }

        // 2. Prepare params
        $params = [
            'singleEvents' => 'true',
            'showDeleted' => 'true',
        ];

        if ($calendar->google_sync_token) {
            $params['syncToken'] = $calendar->google_sync_token;
        }

        // 3. Call Google API
        $response = Http::withToken($user->google_access_token)
            ->get("{$this->baseUrl}/calendars/primary/events", $params);

        if ($response->failed()) {
            if ($response->status() === 410) {
                // Token invalid, clear it and retry with full sync
                $calendar->update(['google_sync_token' => null]);
                return $this->syncEvents($user, $calendar);
            }
            // Optional: Log the error
            return;
        }

        $data = $response->json();

        // 4. Process Events
        if (isset($data['items'])) {
            foreach ($data['items'] as $event) {
                $this->processEvent($event, $user, $calendar);
            }
        }

        // 5. Save the new Next Sync Token
        if (isset($data['nextSyncToken'])) {
            $calendar->update(['google_sync_token' => $data['nextSyncToken']]);
        }
    }

    /**
     * Create, Update, or Delete a local Task based on Google Event data.
     */
    protected function processEvent($event, User $user, Calendar $calendar)
    {
        // Handle Deletions
        if (isset($event['status']) && $event['status'] === 'cancelled') {
            Task::where('google_event_id', $event['id'])->delete();
            return;
        }

        // Determine Due Date (Handle "dateTime" vs "date" for all-day events)
        $dueDate = null;
        if (isset($event['start']['dateTime'])) {
            $dueDate = Carbon::parse($event['start']['dateTime']);
        } elseif (isset($event['start']['date'])) {
            $dueDate = Carbon::parse($event['start']['date']); // All-day event
        }

        // Create or Update the Task
        Task::updateOrCreate(
            [
                'google_event_id' => $event['id'],
            ],
            [
                'user_id' => $user->id,
                'calendar_id' => $calendar->id,
                'name' => $event['summary'] ?? '(No Title)',
                'notes' => $event['description'] ?? null,
                'due_date' => $dueDate,
            ]
        );
    }

    public function watchCalendar(User $user, Calendar $calendar)
    {
        // 1. Generate a unique Channel ID
        $channelId = (string) \Illuminate\Support\Str::uuid();

        // 2. Call Google API to start watching
        // Note: Google requires an HTTPS address.
        $response = Http::withToken($user->google_access_token)
            ->post("{$this->baseUrl}/calendars/primary/events/watch", [
                'id' => $channelId,
                'type' => 'web_hook',
                'address' => route('google.webhook'), // We will create this route next
            ]);

        // 3. Save the Channel and Resource IDs so we can verify incoming hooks
        if ($response->successful()) {
            $data = $response->json();
            $calendar->update([
                'google_channel_id' => $data['id'],
                'google_resource_id' => $data['resourceId'],
            ]);
        }
    }
    public function stopWatching(User $user, Calendar $calendar)
    {
        if (!$calendar->google_channel_id || !$calendar->google_resource_id) {
            return;
        }

        Http::withToken($user->google_access_token)
            ->post("{$this->baseUrl}/channels/stop", [
                'id' => $calendar->google_channel_id,
                'resourceId' => $calendar->google_resource_id,
            ]);

        $calendar->update([
            'google_channel_id' => null,
            'google_resource_id' => null,
        ]);
    }
}