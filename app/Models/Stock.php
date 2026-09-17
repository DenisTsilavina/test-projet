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
        'responsable_id',
        'unite_id',
        'quantite',
        'prix_achat_moyen',
    ];

    protected $casts = [
        'date_stock' => 'datetime',
        'quantite'   => 'decimal:2',
    ];

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

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
