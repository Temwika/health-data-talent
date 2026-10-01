<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Consent extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['granted_at' => 'datetime', 'withdrawn_at' => 'datetime'];
    }

    public static function record(Model $subject, string $type, Request $request): self
    {
        return self::create([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'type' => $type,
            'version' => config('hdt.privacy_version'),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            'granted_at' => now(),
        ]);
    }
}
