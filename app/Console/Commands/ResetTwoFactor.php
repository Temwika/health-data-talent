<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;

class ResetTwoFactor extends Command
{
    protected $signature = 'hdt:reset-2fa {email}';

    protected $description = 'Clear a staff user\'s two-factor secret so they can enrol a new device at next login';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('No user with that email.');

            return self::FAILURE;
        }

        $user->forceFill(['two_factor_secret' => null, 'two_factor_confirmed_at' => null])->save();
        AuditLog::record('2fa.reset', $user, 'Two-factor reset from the command line for '.$user->email);
        $this->info('Two-factor reset. They will be asked to set it up again when they next sign in.');

        return self::SUCCESS;
    }
}
