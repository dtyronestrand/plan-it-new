<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Calendar;
use App\Services\GoogleCalendarService;
use Inertia\Inertia;

class GoogleCalendarController extends Controller
{
private GoogleCalendarService $calendarService;
public function __construct(GoogleCalendarService $calendarService)
{
    $this->calendarService = $calendarService;
}
public function connect(Request $request)
{
    if ($request->has('calendar_id')) {
     session(['google_sync_calendar_id' => $request->input('calendar_id')]);
    }
    return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/calendar.readonly'])
        ->with(['access_type' => 'offline', 'prompt' => 'consent'])
        ->redirect();
}

public function callback(GoogleCalendarService $calendarService)
{
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user = User::find(Auth::id());

    if(!$user) {
        return redirect('/login')->with('error', 'Please login to connect.');
    }

    $user->update([
        'google_id' => $googleUser->id,
        'google_access_token' => $googleUser->token,
        'google_refresh_token' => $googleUser->refreshToken,
    ]);
    $calendarId = session()->pull('google_sync_calendar_id');

    $calendar = null;
    if($calendarId) {
        $calendar = Calendar::where('user_id', $user->id)
            ->find($calendarId);
    }
    if(!$calendar) {
        $calendar = Calendar::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
    }
    if(!$calendar) {
     $calendar = Calendar::create([
                'user_id' => $user->id,
                'name' => 'Google Calendar',
                'color' => '#4285F4',
                'is_active' => true,
                'is_default' => true
            ]);
    }

    $calendarService->syncEvents($user, $calendar);
if (app()->environment('production') || config('app.url') !== 'http://localhost') {
        $calendarService->watchCalendar($user, $calendar);
    return Inertia::render('Calendar/Show', [
        'user_id' => $user->id,
        'success' => 'Google Calendar connected successfully.'
    ]);
}

}
/**
     * Handle incoming webhooks from Google.
     */
    public function handleWebhook(Request $request, GoogleCalendarService $calendarService)
    {
        // 1. Get Google Headers
        $channelId = $request->header('X-Goog-Channel-ID');
        $resourceId = $request->header('X-Goog-Resource-ID');
        $state = $request->header('X-Goog-Resource-State');

        // 2. Find the matching Calendar
        /** @var Calendar|null $calendar */
        $calendar = Calendar::where('google_channel_id', $channelId)
            ->where('google_resource_id', $resourceId)
            ->with('user') // Eager load user for the sync service
            ->first();

        if (!$calendar) {
            // If we don't recognize the channel, ignore it (or log it)
            return response()->json(['message' => 'Channel ignored'], 200);
        }

        // 3. Process the Notification
        // 'sync' = Google confirming the watch is active.
        // 'exists' = Data has changed on the calendar.
        if ($state === 'exists') {
            $calendarService->syncEvents($calendar->user, $calendar);
        }

        // Google expects a 200 OK response immediately
        return response()->json(['message' => 'Received'], 200);
    }
}