<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeLigne extends Model
{
    use HasFactory;
    protected $fillable = [
        'commande_id',
        'type',
        'libelle',
        'quantite',
        'prix_unitaire',
    ];

    /**
     * Relation inverse vers la commande.
     */
    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Événements du modèle : Recalculer le montant de la commande parente à chaque modification.
     */
    protected static function booted(): void
    {
        static::saved(function (CommandeLigne $ligne) {
            $ligne->commande->recalculerMontant();
        });

        static::deleted(function (CommandeLigne $ligne) {
            $ligne->commande->recalculerMontant();
        });
    }
}
