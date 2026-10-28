<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'type',
        'prix_vente',
        'statut',
    ];

    public function produitStock()
    {
        return $this->hasOne(ProduitStock::class);
    }

    public function produitFini()
    {
        return $this->hasOne(ProduitFini::class);
    }

    public function lignesCommandes()
    {
        return $this->hasMany(LigneCommande::class, 'produit_id');
    }

    public function estStock(): bool
    {
        return $this->type === 'stock';
    }

    public function estFini(): bool
    {
        return $this->type === 'fini';
    }
    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, 'produits_stock')
            ->withPivot('seuil_alerte')
            ->withTimestamps();
    }
    public function scopeFiltrer(Builder $query, array $filters): Builder
    {
        return $query
            // Recherche par nom, description ou nom du stock lié
            ->when(!empty($filters['q']), function (Builder $q) use ($filters) {
                $terme = addcslashes($filters['q'], '%_\\'); // neutralise les jokers LIKE

                $q->where(function (Builder $sub) use ($terme) {
                    $sub->where('nom', 'like', "%{$terme}%")
                        ->orWhere('description', 'like', "%{$terme}%")
                        ->orWhereHas('produitStock.stock', function (Builder $s) use ($terme) {
                            $s->where('name_stock', 'like', "%{$terme}%");
                        });
                });
            })
            // Type : valeurs autorisées uniquement
            ->when(
                in_array($filters['type'] ?? null, ['stock', 'fini'], true),
                fn (Builder $q) => $q->where('type', $filters['type'])
            )
            // Statut : valeurs autorisées uniquement
            ->when(
                in_array($filters['statut'] ?? null, ['actif', 'inactif'], true),
                fn (Builder $q) => $q->where('statut', $filters['statut'])
            );
    }
}
