<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achat extends Model
{
    use HasFactory;

    protected $fillable = [
        'fournisseur_id',
        'date_achat',
        'statut',
        'total',
        'remarque',
    ];

    protected $casts = [
        'date_achat' => 'datetime',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneAchat::class);
    }

    /**
     * Valide l'achat : pour chaque ligne, crée un mouvement d'entrée
     * sur le stock concerné (met à jour stocks.quantite automatiquement
     * via MouvementStock::enregistrer).
     */
    public function valider(): void
    {
        foreach ($this->lignes as $ligne) {
            MouvementStock::enregistrer(
                stock: $ligne->stock,
                typeMouvement: 'entree',
                referenceType: 'achat',
                referenceId: $this->id,
                quantite: $ligne->quantite,
                prixUnitaire: $ligne->prix_unitaire,
                remarque: "Achat #{$this->id} — {$this->fournisseur->nom}"
            );
        }

        $this->update(['statut' => 'valide']);
    }
}
