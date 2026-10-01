<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeStaffUser extends Command
{
    protected $signature = 'hdt:user {email} {name} {--role=recruiter : admin or recruiter}';

    protected $description = 'Create a staff login (or reset its password) and print a one-time password';

    public function handle(): int
    {
        $role = $this->option('role');
        if (! in_array($role, ['admin', 'recruiter'], true)) {
            $this->error('Role must be admin or recruiter.');

            return self::FAILURE;
        }

        $password = Str::password(20, symbols: false);
        $user = User::firstOrNew(['email' => $this->argument('email')]);
        $user->forceFill([
            'name' => $this->argument('name'),
            'password' => $password,
            'role' => $role,
        ])->save();

        AuditLog::record('user.saved', $user, "Staff user {$user->email} saved as {$role} from the command line");

        $this->info("Saved {$user->email} ({$role}).");
        $this->line('Password: '.$password);
        $this->line('Shown once. Store it in a password manager.');

        return self::SUCCESS;
    }
}
