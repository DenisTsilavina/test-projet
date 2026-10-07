<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeLigne extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
