<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = [
        'article_id',
        'description_id',
        'unite_id',
        'effectif',
        'prix',
    ];

    /**
     * Récupérer l'article auquel appartient cet ingrédient.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Récupérer la description associée à cet ingrédient.
     */
    public function description(): BelongsTo
    {
        return $this->belongsTo(Description::class);
    }

    /**
     * Récupérer l'unité de mesure associée à cet ingrédient.
     */
    public function unite(): BelongsTo
    {
        return $this->belongsTo(Unite::class); // Ou 'Unit' selon le nom de votre modèle
    }
}
