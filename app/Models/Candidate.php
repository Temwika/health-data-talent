<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    protected $fillable = [
        'track', 'name', 'email', 'phone', 'location', 'current_title', 'years', 'qualification',
        'specialty', 'registration', 'expertise', 'tools', 'ngo_work', 'desired_role', 'pattern',
        'salary', 'availability', 'right_to_work', 'marketing_opt_in',
    ];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted',
            'registration' => 'encrypted',
            'expertise' => 'array',
            'tools' => 'array',
            'ngo_work' => 'array',
            'marketing_opt_in' => 'boolean',
            'last_contact_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (Candidate $c) => $c->last_contact_at ??= now());

        // Secure deletion: the CV file and consent trail go with the profile.
        static::deleting(function (Candidate $candidate) {
            if ($candidate->cv_path) {
                Storage::disk('local')->delete($candidate->cv_path);
            }
            $candidate->consents()->delete();
        });
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function consents(): MorphMany
    {
        return $this->morphMany(Consent::class, 'subject');
    }

    public function isDoctor(): bool
    {
        return $this->track === 'doctor';
    }

    public function trackLabel(): string
    {
        return $this->isDoctor() ? 'Doctor (NGO)' : 'Health data';
    }

    /** @return list<string> */
    public function skillList(): array
    {
        $skills = $this->isDoctor()
            ? array_merge([$this->specialty], $this->ngo_work ?? [])
            : array_merge($this->tools ?? [], $this->expertise ?? []);

        return array_values(array_filter($skills));
    }

    public function retentionDue(): ?\Illuminate\Support\Carbon
    {
        return $this->last_contact_at?->copy()->addMonths(config('hdt.retention_months'));
    }
}
