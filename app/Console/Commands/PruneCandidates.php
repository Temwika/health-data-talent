<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Candidate;
use Illuminate\Console\Command;

class PruneCandidates extends Command
{
    protected $signature = 'hdt:prune-candidates {--dry-run : List what would be deleted without deleting}';

    protected $description = 'Delete candidate profiles (and their CV files) with no contact inside the retention period';

    public function handle(): int
    {
        $cutoff = now()->subMonths(config('hdt.retention_months'));
        $query = Candidate::where('last_contact_at', '<', $cutoff);

        if ($this->option('dry-run')) {
            $this->info($query->count().' profile(s) are past retention (last contact before '.$cutoff->toDateString().').');

            return self::SUCCESS;
        }

        $deleted = 0;
        // Deleted one at a time so the model's deleting hook removes each CV file.
        $query->each(function (Candidate $candidate) use (&$deleted) {
            $candidate->delete();
            $deleted++;
        });

        if ($deleted > 0) {
            AuditLog::record('retention.prune', null, $deleted.' candidate profile(s) deleted after retention period');
        }

        $this->info("Deleted {$deleted} profile(s).");

        return self::SUCCESS;
    }
}
