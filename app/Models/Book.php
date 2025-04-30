<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_id',
        'title',
        'author',
        'cover_url',
    ];

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
