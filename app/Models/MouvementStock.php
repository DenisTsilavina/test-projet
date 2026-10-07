<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stocks';

    protected $fillable = [
        'stock_id',
        'type_mouvement',
        'reference_type',
        'reference_id',
        'emplacement',
        'quantite',
        'prix_unitaire',
        'date_mouvement',
        'utilisateur_id',
        'remarque',
    ];

    protected $casts = [
        'date_mouvement' => 'datetime',
        'quantite'       => 'decimal:2',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Enregistre un mouvement sur UN emplacement précis (magasin OU
     * réserve) et met à jour la colonne correspondante sur Stock.
     * Bas niveau — utilisé directement pour les achats (toujours en
     * réserve) et les annulations. Pour une VENTE, préférer
     * sortirAvecPriorite() ci-dessous, qui gère les deux emplacements.
     */
    public static function enregistrer(
        Stock $stock,
        string $typeMouvement,
        string $referenceType,
        ?int $referenceId,
        float $quantite,
        ?int $prixUnitaire = null,
        ?string $remarque = null,
        string $emplacement = 'reserve'
    ): self {
        $mouvement = self::create([
            'stock_id'       => $stock->id,
            'type_mouvement' => $typeMouvement,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'emplacement'    => $emplacement,
            'quantite'       => $quantite,
            'prix_unitaire'  => $prixUnitaire,
            'date_mouvement' => now(),
            'utilisateur_id' => auth()->id(),
            'remarque'       => $remarque,
        ]);

        $colonne = $emplacement === 'magasin' ? 'quantite_magasin' : 'quantite';

        if ($typeMouvement === 'entree') {
            $stock->increment($colonne, $quantite);
        } else {
            $stock->decrement($colonne, $quantite);
        }

        return $mouvement;
    }

    /**
     * Règle métier centrale : une sortie (vente, commande) consomme
     * D'ABORD le magasin, puis complète avec la réserve si besoin.
     * Peut générer 1 ou 2 mouvements selon le cas.
     */
    public static function sortirAvecPriorite(
        Stock $stock,
        string $referenceType,
        ?int $referenceId,
        float $quantite,
        ?int $prixUnitaire = null,
        ?string $remarque = null
    ): array {
        $stock->refresh();

        $dispoMagasin = (float) $stock->quantite_magasin;
        $mouvements = [];

        $prisAuMagasin = min($dispoMagasin, $quantite);
        if ($prisAuMagasin > 0) {
            $mouvements[] = self::enregistrer(
                stock: $stock,
                typeMouvement: 'sortie',
                referenceType: $referenceType,
                referenceId: $referenceId,
                quantite: $prisAuMagasin,
                prixUnitaire: $prixUnitaire,
                remarque: $remarque,
                emplacement: 'magasin'
            );
        }

        $resteAPrendre = $quantite - $prisAuMagasin;
        if ($resteAPrendre > 0) {
            $mouvements[] = self::enregistrer(
                stock: $stock,
                typeMouvement: 'sortie',
                referenceType: $referenceType,
                referenceId: $referenceId,
                quantite: $resteAPrendre,
                prixUnitaire: $prixUnitaire,
                remarque: $remarque,
                emplacement: 'reserve'
            );
        }

        return $mouvements;
    }

    /**
     * Transfère une quantité de la réserve vers le magasin
     * (réapprovisionnement du rayon). Deux mouvements liés.
     */
    public static function transferer(Stock $stock, float $quantite, ?string $remarque = null): array
    {
        $stock->refresh();

        if ($quantite > (float) $stock->quantite) {
            throw new \InvalidArgumentException(
                "Réserve insuffisante pour transférer {$quantite} (disponible : {$stock->quantite})."
            );
        }

        $sortie = self::enregistrer(
            stock: $stock,
            typeMouvement: 'sortie',
            referenceType: 'transfert',
            referenceId: null,
            quantite: $quantite,
            remarque: $remarque ?? 'Transfert réserve -> magasin',
            emplacement: 'reserve'
        );

        $entree = self::enregistrer(
            stock: $stock,
            typeMouvement: 'entree',
            referenceType: 'transfert',
            referenceId: null,
            quantite: $quantite,
            remarque: $remarque ?? 'Transfert réserve -> magasin',
            emplacement: 'magasin'
        );

        return [$sortie, $entree];
    }
}
