<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'description',
        'effectif',
        'unite_id',
        'region',
    ];

    /**
     * Le stock auquel appartient cette description.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * L'unité de mesure (Kg, Litre, Pièce, etc.).
     */
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    /**
     * Les sous-catégories de cette description.
     */
    public function sousCategories()
    {
        return $this->hasMany(SousCategory::class, 'description_id');
    }

    /**
     * Les ventes liées à cette description.
     */
    public function ventes()
    {
        return $this->hasMany(Vente::class, 'description_id');
    }
}
