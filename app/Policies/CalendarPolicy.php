<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Calendar;
use Illuminate\Auth\Access\Response;

class CalendarPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Calendar $calendar): Response
    {
        if (!$calendar) {
            return Response::deny('Calendar not found.');
        }
        
        return $user->id === $calendar->user_id
            ? Response::allow()
            : Response::deny('You do not own this calendar.');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Calendar $calendar): Response
    {
        return $user->id === $calendar->user_id
            ? Response::allow()
            : Response::deny('You do not own this calendar.');
    }

    public function delete(User $user, Calendar $calendar): Response
    {
        return $user->id === $calendar->user_id
            ? Response::allow()
            : Response::deny('You do not own this calendar.');
    }
}
