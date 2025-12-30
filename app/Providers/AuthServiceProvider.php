<?php

namespace App\Providers;

use App\Models\Calendar;
use App\Policies\CalendarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Calendar::class => CalendarPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}