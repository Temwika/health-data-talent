<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = ['name', 'email', 'organisation', 'subject', 'message'];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime'];
    }
}
