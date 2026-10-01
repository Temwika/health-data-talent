<?php

namespace App\Support;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Matcher
{
    /** Number of the vacancy's essential skills the candidate's profile mentions. */
    public static function score(Candidate $candidate, Vacancy $vacancy): int
    {
        $have = Str::lower(implode(' ', array_merge(
            $candidate->skillList(),
            [$candidate->current_title, $candidate->desired_role],
        )));

        $score = 0;
        foreach ($vacancy->skillList() as $skill) {
            $skill = Str::lower($skill);
            $words = array_filter(preg_split('/[\s\/]+/', $skill), fn ($w) => strlen($w) > 2);

            if (str_contains($have, $skill) || collect($words)->contains(fn ($w) => str_contains($have, $w))) {
                $score++;
            }
        }

        return $score;
    }

    /**
     * @param  Collection<int, Candidate>  $candidates
     * @return Collection<int, array{candidate: Candidate, score: int}>
     */
    public static function top(Vacancy $vacancy, Collection $candidates, int $limit = 3): Collection
    {
        $wantDoctor = $vacancy->area === 'doctor';

        return $candidates
            ->filter(fn (Candidate $c) => $c->isDoctor() === $wantDoctor)
            ->map(fn (Candidate $c) => ['candidate' => $c, 'score' => self::score($c, $vacancy)])
            ->filter(fn ($m) => $m['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }
}
