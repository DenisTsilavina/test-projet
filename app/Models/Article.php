<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_prd_finit',
        'note',
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'article_id', 'id');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'article_id', 'id');
    }
}
