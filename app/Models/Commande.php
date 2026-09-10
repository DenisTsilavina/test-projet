<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'date_commande',
        'date_besoin',
        'date_livraison',
        'statut',
        'remarque',
        'total',
    ];

    protected $casts = [
        'date_commande'  => 'datetime',
        'date_besoin'    => 'date',
        'date_livraison' => 'date',
    ];

    /**
     * Le client (User avec role CLIENT) qui a passé la commande.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class);
    }


    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function transports()
    {
        return $this->hasMany(Transport::class);
    }

    /**
     * Total deja paye pour cette commande.
     */
    public function getMontantPayeAttribute(): int
    {
        return $this->paiements()->sum('montant');
    }

    /**
     * Reste a payer.
     */
    public function getResteAPayerAttribute(): int
    {
        return max(0, $this->total - $this->montant_paye);
    }
}
