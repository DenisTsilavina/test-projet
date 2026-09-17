<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'reference',
        'date_commande',
        'date_besoin',
        'date_livraison',
        'statut',
        'remarque',
        'total',
    ];

    protected $casts = [
        'date_commande'  => 'datetime',
        'date_besoin'    => 'date',
        'date_livraison' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }

    public function getMontantPayeAttribute(): int
    {
        return $this->paiements()->sum('montant');
    }

    public function getResteAPayerAttribute(): int
    {
        return max(0, $this->total - $this->montant_paye);
    }

    /**
     * Vérifie que les lignes DÉJÀ ENREGISTRÉES de cette commande sont
     * encore réalisables avec le stock actuel (utilisé avant validation
     * par l'admin, car le stock a pu bouger depuis la demande du client).
     */
    public function verifierDisponibilite(): array
    {
        $lignes = $this->lignes()->get()->map(fn ($l) => [
            'produit_id' => $l->produit_id,
            'quantite'   => $l->quantite,
        ])->toArray();

        return self::verifierDisponibiliteLignes($lignes);
    }

    /**
     * Version statique réutilisable AVANT la création d'une commande
     * (quand on n'a encore que le tableau brut des lignes du formulaire,
     * pas d'instance de Commande).
     */
    public static function verifierDisponibiliteLignes(array $lignes): array
    {
        $errors = [];

        foreach ($lignes as $i => $ligne) {
            $produit = Produit::with(['produitStock.stock', 'produitFini.compositions.stock'])
                ->find($ligne['produit_id']);

            if (!$produit) {
                $errors["lignes.$i.produit_id"] = 'Produit introuvable.';
                continue;
            }

            $quantiteDemandee = (int) $ligne['quantite'];

            if ($produit->estStock()) {
                $dispo = $produit->produitStock->stock->quantite ?? 0;

                if ($quantiteDemandee > $dispo) {
                    $errors["lignes.$i.quantite"] =
                        "Stock insuffisant pour « {$produit->nom} ». Disponible : {$dispo}.";
                }
            } elseif ($produit->estFini()) {
                if (!$produit->produitFini->stockSuffisantPour($quantiteDemandee)) {
                    $errors["lignes.$i.quantite"] =
                        "Ingrédients insuffisants pour fabriquer « {$produit->nom} » en quantité {$quantiteDemandee}.";
                }
            }
        }

        return $errors;
    }
}
