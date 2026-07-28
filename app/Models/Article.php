<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'total_prd_finit',
        'note'
    ];

    // L'utilisateur qui a créé la recette
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation utilisée dans votre seeder pour ajouter les ingrédients
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
