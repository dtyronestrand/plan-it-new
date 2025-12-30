<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Calendar;
class CalendarController extends Controller
{
use  AuthorizesRequests;

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'nullable|string|max:7',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ]);

     $calendar = new Calendar($validated);
     $calendar->user_id = $request->user()->id;
     $calendar->save();

     return to_route('Calendar/Show',['calendar' => $calendar->id, 'user_id' => $request->user()->id])->with('success', 'Calendar created successfully.');

}

public function show(Request $request, $user_id, $calendar = null)
{
    // If calendar ID is provided, try to find it
    if ($calendar) {
        $calendar = Calendar::find($calendar);
    }
    
    // If no calendar or calendar not found, get default
    if (!$calendar) {
        $calendar = Calendar::where('user_id', $user_id)->where('is_default', true)->first();
        if (!$calendar) {
            return redirect()->back()->with('error', 'No calendar found.');
        }
    }
    
    $this->authorize('view', $calendar);

    return Inertia::render('Calendar/Show', [
        'calendar' => $calendar->load('tasks'),
    ]);
}


}
