<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit_fini_id',
        'stock_id',
        'quantite_necessaire',
        'unite',
    ];

    public function produitFini()
    {
        return $this->belongsTo(ProduitFini::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
