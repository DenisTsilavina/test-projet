<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = [
        'article_id',
        'description_id',
        'unite_id',
        'effectif',
    ];

    // relation article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // relation description
    public function description()
    {
        return $this->belongsTo(Description::class);
    }

    // relation unite
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }
}
