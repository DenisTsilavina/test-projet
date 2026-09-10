<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
