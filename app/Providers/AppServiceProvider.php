<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Event::listen(Login::class, function (Login $event): void {
            AuditLog::record('login', $event->user, 'Signed in');
            $event->user->forceFill(['last_login_at' => now()])->save();
        });

        Event::listen(Logout::class, function (Logout $event): void {
            AuditLog::record('logout', $event->user, 'Signed out');
        });

        Event::listen(Failed::class, function (Failed $event): void {
            AuditLog::record('login.failed', null, 'Failed sign-in for '.($event->credentials['email'] ?? ''));
        });
    }
}

