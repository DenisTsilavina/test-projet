<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_stock',
        'date_stock',
        'user_id',
        'unite_id',
        'quantite',
    ];

    protected $casts = [
        'date_stock' => 'datetime',
        'quantite'   => 'decimal:2',
    ];

    /**
     * CHANGEMENT : un stock appartient à une seule unité (belongsTo),
     * remplace l'ancienne relation belongsToMany avec table pivot.
     */
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    /**
     * CHANGEMENT : persn_stock (string libre) -> relation vers users.id
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function descriptions()
    {
        return $this->hasMany(Description::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }
}
