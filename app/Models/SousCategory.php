<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'description_id',
        // CHANGEMENT : 'stock_categorie' retiré, colonne supprimée de la migration.
        'prix_achat',
        'prix_vente',
    ];

    public function description()
    {
        return $this->belongsTo(Description::class, 'description_id');
    }

    public function vente()
    {
        return $this->hasMany(Vente::class, 'sous_categorie_id');
    }

    public function stock()
    {
        return $this->hasOneThrough(
            Stock::class,
            Description::class,
            'id',              // clé sur descriptions
            'id',               // clé sur stocks
            'description_id',   // clé locale sur sous_categories
            'stock_id'          // clé locale sur descriptions
        );
    }
}
