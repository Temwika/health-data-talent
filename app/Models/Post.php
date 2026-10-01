<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = ['title', 'category', 'excerpt', 'body'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug ??= Str::slug($post->title).'-'.Str::lower(Str::random(4));
        });
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Body is stored as plain text. Blank lines split paragraphs and a line
     * starting with "## " is a sub-heading. Output is escaped in the view.
     *
     * @return list<array{type: string, text: string}>
     */
    public function blocks(): array
    {
        $blocks = [];
        foreach (preg_split('/\R{2,}/', trim($this->body)) as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') {
                continue;
            }
            $blocks[] = str_starts_with($chunk, '## ')
                ? ['type' => 'h2', 'text' => substr($chunk, 3)]
                : ['type' => 'p', 'text' => $chunk];
        }

        return $blocks;
    }
}
