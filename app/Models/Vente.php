<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'produit_id',
        'type_produit',
        'prix',
        'effectif',
        'prix_total',
    ];

    /**
     * Le vendeur qui a réalisé la vente.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Le client (User avec role CLIENT), optionnel.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Coût d'achat unitaire, selon le type de produit :
     * - stock : prix d'achat moyen du stock lié.
     * - fini  : somme du coût des ingrédients de la recette
     *           (quantite_necessaire * prix_achat_moyen de chaque stock).
     */
    public function getPrixAchatUnitaireAttribute(): int
    {
        $produit = $this->produit;

        if (!$produit) {
            return 0;
        }

        if ($this->type_produit === 'stock') {
            return (int) ($produit->produitStock->stock->prix_achat_moyen ?? 0);
        }

        $cout = 0;
        foreach ($produit->produitFini->compositions ?? [] as $ligne) {
            $cout += $ligne->quantite_necessaire * ($ligne->stock->prix_achat_moyen ?? 0);
        }

        return (int) $cout;
    }

    public function getRevenuNetAttribute(): int
    {
        return ($this->prix - $this->prix_achat_unitaire) * $this->effectif;
    }

    /**
     * Retire le stock nécessaire à cette vente (directement, ou selon
     * la composition si c'est un produit fini). Même principe que
     * LigneCommande::appliquerSurStock().
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
                referenceId: $this->id,
                quantite: $this->effectif,
                prixUnitaire: $this->prix,
                remarque: "Vente #{$this->id} — {$produit->nom}"
            );

            return;
        }

        foreach ($produit->produitFini->compositions as $ligneRecette) {
            MouvementStock::enregistrer(
                stock: $ligneRecette->stock,
                typeMouvement: 'sortie',
                referenceType: 'vente',
                referenceId: $this->id,
                quantite: $ligneRecette->quantite_necessaire * $this->effectif,
                remarque: "Vente #{$this->id} — {$produit->nom} (composition)"
            );
        }
    }

    /**
     * Inverse de appliquerSurStock(), utilisé quand une vente est supprimée.
     */
    public function annulerSurStock(): void
    {
        $produit = $this->produit;

        if ($this->type_produit === 'stock') {
            $stock = $produit->produitStock->stock;

            MouvementStock::enregistrer(
                stock: $stock,
                typeMouvement: 'entree',
                referenceType: 'ajustement',
                referenceId: $this->id,
                quantite: $this->effectif,
                remarque: "Annulation vente #{$this->id} — {$produit->nom}"
            );

            return;
        }

        foreach ($produit->produitFini->compositions as $ligneRecette) {
            MouvementStock::enregistrer(
                stock: $ligneRecette->stock,
                typeMouvement: 'entree',
                referenceType: 'ajustement',
                referenceId: $this->id,
                quantite: $ligneRecette->quantite_necessaire * $this->effectif,
                remarque: "Annulation vente #{$this->id} — {$produit->nom} (composition)"
            );
        }
    }
}
