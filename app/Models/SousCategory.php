<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SousCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'description_id',
        'stock_categorie',
        'prix_achat',
        'prix_vente',
    ];

    public function description() {
        return $this->belongsTo(Description::class, 'description_id');
    }

    // ✅ AJOUTER — relation vers Vente
    public function ventes() {
        return $this->hasMany(Vente::class, 'sous_categorie_id');
    }

    // ✅ AJOUTER — accéder au Stock parent via Description
    public function stock() {
        return $this->hasOneThrough(
            Stock::class,
            Description::class,
            'id',         // clé sur descriptions
            'id',         // clé sur stocks
            'description_id', // clé locale sur sous_categories
            'stock_id'    // clé locale sur descriptions
        );
    }
}
