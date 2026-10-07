<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitStock extends Model
{
    use HasFactory;

    protected $table = 'produits_stock';

    protected $fillable = [
        'produit_id',
        'stock_id',
        'seuil_alerte',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * CHANGEMENT : magasin + réserve, pas seulement stock->quantite.
     */
    public function getQuantiteDisponibleAttribute(): float
    {
        return $this->stock->quantite_totale ?? 0;
    }

    public function enAlerte(): bool
    {
        return $this->quantite_disponible <= $this->seuil_alerte;
    }
}
