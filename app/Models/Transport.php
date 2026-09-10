<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'depart',
        'arrivee',
        'frais_transport',
        'transporteur',
        'date_depart',
        'date_arrivee',
        'statut',
    ];

    protected $casts = [
        'date_depart'  => 'datetime',
        'date_arrivee' => 'datetime',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
