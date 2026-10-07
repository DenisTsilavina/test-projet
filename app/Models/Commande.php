<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_commande',
        'nom_client',
        'telephone_client',
        'adresse_livraison',
        'montant_total',
        'statut',
    ];

    public function lignes()
    {
        return $this->hasMany(CommandeLigne::class);
    }
}
