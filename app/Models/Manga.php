<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    protected $fillable = [
        'title',
        'slug_mangalib'
    ];

    public function chapters()
    {
        return $this->hasMany(MangaChapter::class);
    }
}
