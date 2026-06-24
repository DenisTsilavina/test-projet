<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_stock',
        'persn_stock',
        'date_stock',
    ];

    protected $casts = [
        'date_stock' => 'datetime:d/m/Y H:i',
    ];

    public function descriptions()
    {
        return $this->hasMany(Description::class);
    }

    /**
     * Relation Many-to-Many connectée via le symbole de l'unité.
     */
   /** public function unites()
    {
        return $this->belongsToMany(
            Unite::class,         // 1. Modèle ciblé
            'stock_unite',        // 2. Nom de la table pivot
            'stock_id',           // 3. Clé pivot liée au modèle actuel (Stock)
            'unite_symbole',      // 4. Clé pivot liée au modèle ciblé (Unite)
            'id',                 // 5. Clé locale de référence (stocks.id)
            'symbole'             // 6. Clé distante de référence (unites.symbole)
        )
            ->withPivot('quantite')
            ->withTimestamps();
    }*/
    public function unites()
    {
        return $this->belongsToMany(Unite::class, 'stock_unite')
            ->using(StockUnite::class)
            ->withPivot('quantite')
            ->withTimestamps();
    }
}
