<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'client_id',
        'description_id',
        'sous_categorie_id',
        'prix',
        'effectif',
        'prix_total',
    ];

    public function getRevenuNetAttribute()
    {
        // CHANGEMENT : $this->categorie -> $this->sousCategorie
        $prixAchat = $this->sousCategorie->prix_achat ?? 0;

        return ($this->prix - $prixAchat) * $this->effectif;
    }

    /**
     * Le vendeur qui a réalisé la vente.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Le client pour qui la vente a été faite (User avec role CLIENT).
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function description()
    {
        return $this->belongsTo(Description::class, 'description_id');
    }

    /**
     * CHANGEMENT : categorie() -> sousCategorie(), et categorie_id -> sous_categorie_id
     */
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategory::class, 'sous_categorie_id');
    }
}
