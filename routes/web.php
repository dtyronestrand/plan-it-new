<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GoogleCalendarController;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
  return redirect()->route('calendar.show', ['user_id' => Auth::id()]);
})->middleware(['auth', 'verified'])->name('home');

Route::get('/calendar', function () {
  return redirect()->route('calendar.show', ['user_id' => Auth::id()]);
})->middleware(['auth', 'verified']);

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('calendar/{user_id}/{calendar?}', [App\Http\Controllers\CalendarController::class, 'show'])->name('calendar.show')->middleware(['auth', 'verified']);

Route::post('calendar', [CalendarController::class, 'store'])->name('calendar.store');

Route::put('calendar/{user_id}/{calendar}', [CalendarController::class, 'update'])->name('calendar.update')->middleware(['auth', 'verified']);

Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store')->middleware(['auth', 'verified']);

Route::put('tasks/{id}', [TaskController::class, 'update'])->name('tasks.update')->middleware(['auth', 'verified']);

Route::delete('calendar/{user_id}/{calendar}', [CalendarController::class, 'destroy'])->name('calendar.destroy')->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function() {
  Route::get('/auth/google', [GoogleCalendarController::class, 'connect'])->name('google.connect');
  Route::get('/auth/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
});
Route::post('/webhook/google-calendar', [GoogleCalendarController::class, 'handleWebhook'])->name('google.webhook');
require __DIR__.'/settings.php';
