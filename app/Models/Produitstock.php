<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produitstock extends Model
{

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
     * La quantité vendable vient directement du stock lié,
     * pas d'un champ dupliqué ici.
     */
    public function getQuantiteDisponibleAttribute(): float
    {
        return $this->stock->quantite ?? 0;
    }

    public function enAlerte(): bool
    {
        return $this->quantite_disponible <= $this->seuil_alerte;
    }
}
