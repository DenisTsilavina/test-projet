<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_commande',
        'nom_client',
        'telephone_client',
        'adresse_livraison',
        'montant_total',
        'statut',
    ];

    public function lignes()
    {
        return $this->hasMany(CommandeLigne::class);
    }
    public static function verifierDisponibiliteLignes(array $lignes): array
    {
        $errors = [];

        $produits = Produit::with(['produitStock.stock', 'produitFini.compositions.stock'])
            ->whereIn('id', collect($lignes)->pluck('produit_id')->unique())
            ->get()
            ->keyBy('id');

        // stock_id => ['stock' => Stock, 'requis' => float]
        $besoins = [];

        foreach ($lignes as $i => $ligne) {
            $produit  = $produits->get($ligne['produit_id']);
            $quantite = (int) $ligne['quantite'];

            if (!$produit) {
                $errors["ligne_{$i}"] = 'Produit introuvable.';
                continue;
            }

            if ($produit->statut !== 'actif') {
                $errors["ligne_{$i}"] = "Le produit « {$produit->nom} » est inactif.";
                continue;
            }

            if ($produit->type === 'stock') {
                $stock = $produit->produitStock?->stock;

                if (!$stock) {
                    $errors["ligne_{$i}"] = "Le produit « {$produit->nom} » n'est relié à aucun stock.";
                    continue;
                }

                self::ajouterBesoin($besoins, $stock, $quantite);
            } else {
                $compositions = $produit->produitFini?->compositions ?? collect();

                if ($compositions->isEmpty()) {
                    $errors["ligne_{$i}"] = "Le produit « {$produit->nom} » n'a aucune recette.";
                    continue;
                }

                foreach ($compositions as $composition) {
                    if (!$composition->stock) {
                        $errors["ligne_{$i}"] = "Un ingrédient de « {$produit->nom} » est introuvable.";
                        continue 2;
                    }

                    self::ajouterBesoin(
                        $besoins,
                        $composition->stock,
                        (float) $composition->quantite_necessaire * $quantite
                    );
                }
            }
        }

        foreach ($besoins as $stockId => $besoin) {
            $disponible = self::quantiteDisponible($besoin['stock']);

            if ($besoin['requis'] > $disponible) {
                $errors["stock_{$stockId}"] = sprintf(
                    'Stock insuffisant pour « %s » : %s requis, %s disponible.',
                    $besoin['stock']->name_stock,
                    rtrim(rtrim(number_format($besoin['requis'], 2, ',', ' '), '0'), ','),
                    rtrim(rtrim(number_format($disponible, 2, ',', ' '), '0'), ',')
                );
            }
        }

        return $errors;
    }

    private static function ajouterBesoin(array &$besoins, $stock, float $quantite): void
    {
        $besoins[$stock->id]['stock']  = $stock;
        $besoins[$stock->id]['requis'] = ($besoins[$stock->id]['requis'] ?? 0) + $quantite;
    }

    /**
     * POINT UNIQUE À ADAPTER : nom de la colonne qui contient la quantité
     * en stock dans votre table "stocks" (ici : quantite_disponible).
     */
    private static function quantiteDisponible($stock): float
    {
        return (float) $stock->quantite_disponible;
    }
}
