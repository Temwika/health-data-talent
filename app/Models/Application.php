<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = ['candidate_id', 'vacancy_id', 'stage', 'source', 'consented_at'];

    protected function casts(): array
    {
        return ['consented_at' => 'datetime'];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function stageLabel(): string
    {
        return config('hdt.stages')[$this->stage] ?? $this->stage;
    }
}
