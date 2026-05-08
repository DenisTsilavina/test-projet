<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'description_id',
        'effectif',
        'unite_id',
    ];

    public function articles()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
