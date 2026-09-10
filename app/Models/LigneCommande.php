<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande
{
    use HasFactory;
    protected $table = 'lignes_commandes';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'type_produit',
        'quantite',
        'prix_unitaire',
        'total_ligne',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Applique la ligne de commande : retire du stock, en fonction
     * du type de produit.
     * - stock : retire directement du stock lié (via ProduitStock).
     * - fini  : retire de chaque stock de la recette (via Composition),
     *           multiplié par la quantité commandée.
     */
    public function appliquerSurStock(): void
    {
        $produit = $this->produit;

        if ($this->type_produit === 'stock') {
            $stock = $produit->produitStock->stock;

            MouvementStock::enregistrer(
                stock: $stock,
                typeMouvement: 'sortie',
                referenceType: 'vente',
                referenceId: $this->commande_id,
                quantite: $this->quantite,
                prixUnitaire: $this->prix_unitaire,
                remarque: "Commande #{$this->commande_id} — {$produit->nom}"
            );

            return;
        }

        // type_produit === 'fini' : on déduit selon la recette (compositions)
        foreach ($produit->produitFini->compositions as $ligneRecette) {
            MouvementStock::enregistrer(
                stock: $ligneRecette->stock,
                typeMouvement: 'sortie',
                referenceType: 'vente',
                referenceId: $this->commande_id,
                quantite: $ligneRecette->quantite_necessaire * $this->quantite,
                remarque: "Commande #{$this->commande_id} — {$produit->nom} (composition)"
            );
        }
    }
}
