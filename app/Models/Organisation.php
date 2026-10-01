<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $fillable = [
        'name', 'sector', 'contact_name', 'contact_title', 'email', 'phone', 'service', 'message',
    ];

    protected function casts(): array
    {
        return ['phone' => 'encrypted'];
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function serviceLabel(): string
    {
        return config('hdt.services')[$this->service] ?? $this->service;
    }
}
