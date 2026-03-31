<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Vente — corriger 'categorie_id' → 'sous_categorie_id'
class Vente extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'description_id',
        'categorie_id',
        'prix',
        'effectif',
        'prix_total',
    ];
    public function getRevenuNetAttribute()
    {
        $prixAchat = $this->categorie->prix_achat ?? 0;
        return ($this->prix - $prixAchat) * $this->effectif;
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function description() {
        return $this->belongsTo(Description::class, 'description_id');
    }
    public function categorie() {
        return $this->belongsTo(SousCategory::class, 'categorie_id');
    }
}
