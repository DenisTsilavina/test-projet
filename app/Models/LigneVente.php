<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    protected $fillable = [
        'vente_id',
        'stock_id',
        'description_id',
        'categorie_id',
        'unite_id',
        'quantite',
        'unite_symbol',
        'prix_unitaire',
        'total',
        'produit_nom',
        'sous_categorie',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function description()
    {
        return $this->belongsTo(Description::class);
    }

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }
}
