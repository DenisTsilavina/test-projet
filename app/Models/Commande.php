<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'commandable_type',
        'commandable_id',
        'nom_commande',
        'effectif',
        'date_besoin',
        'statut',
        'commentaires',
        'user_id'
    ];

    protected $casts = [
        'date_besoin' => 'date',
    ];

    /**
     * Lien polymorphique vers l'Article, le Stock, ou autre.
     */
    public function commandable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relation avec l'utilisateur qui a passé la commande.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesseur magique pour obtenir le nom de l'item commandé
     * (Qu'il vienne d'un article, d'un stock ou du texte libre "Autre")
     */
    public function getNomItemAttribute(): string
    {
        if ($this->commandable) {
            // Suppose que vos modèles Article ou Stock ont un attribut 'nom' ou 'designation'
            return $this->commandable->nom ?? $this->commandable->designation;
        }

        return $this->nom_commande ?? 'Élément inconnu';
    }
}
