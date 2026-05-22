<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'name',
    'total_prd_finit',
    'note',
    ];

    // relation utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relation ingredients
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
