<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nom_evenement',
        'lieu',
        'date_evenement',
        'prix_entree',
        'description',
        'organisateur',
    ];

    protected $casts = [
        'date_evenement' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
