<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'vendeur_id',
        'client_anon_id',
        'mode_paiement',
        'total_general',
    ];

    // ── Relations ──────────────────────────────
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'vendeur_id');
    }

    public function clientAnon()
    {
        return $this->belongsTo(Client::class, 'client_anon_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneVente::class, 'vente_id');
    }
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

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_anon_id');
    }
}
