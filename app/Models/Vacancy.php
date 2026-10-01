<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Vacancy extends Model
{
    protected $fillable = [
        'title', 'organisation_name', 'location', 'pattern', 'salary', 'contract_type',
        'area', 'skills', 'description', 'contact_name', 'contact_email',
    ];

    protected function casts(): array
    {
        return ['is_example' => 'boolean', 'published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Vacancy $vacancy) {
            $vacancy->slug ??= Str::slug($vacancy->title).'-'.Str::lower(Str::random(6));
            $vacancy->area ??= self::inferArea($vacancy->title.' '.$vacancy->skills);
        });
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeLive(Builder $query): void
    {
        $query->where('status', 'live');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    /** @return list<string> */
    public function skillList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $this->skills))));
    }

    public function areaLabel(): string
    {
        return config('hdt.areas')[$this->area] ?? $this->area;
    }

    public static function inferArea(string $text): string
    {
        $text = Str::lower($text);

        return match (true) {
            (bool) preg_match('/doctor|clinical advis|telemedicine/', $text) => 'doctor',
            (bool) preg_match('/informatic|epr|ehr|epic|cerner|systm/', $text) => 'informatics',
            (bool) preg_match('/engineer|digital|transform|product/', $text) => 'digital',
            default => 'data',
        };
    }
}
