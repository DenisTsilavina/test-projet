<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_stock',
        'date_stock',
        'responsable_id',
        'unite_id',
        'quantite',
        'quantite_magasin',
        'prix_achat_moyen',
    ];

    protected $casts = [
        'date_stock'       => 'datetime',
        'quantite'         => 'decimal:2',
        'quantite_magasin' => 'decimal:2',
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

    /**
     * AJOUT : total réellement disponible (magasin + réserve).
     * C'est CE nombre qu'il faut utiliser pour vérifier la disponibilité
     * avant une vente/commande — pas 'quantite' seule.
     */
    public function getQuantiteTotaleAttribute(): float
    {
        return (float) $this->quantite + (float) $this->quantite_magasin;
    }
    public function produits(): BelongsToMany
    {
        return $this->belongsToMany(Produit::class, 'produits_stock')
            ->withPivot('seuil_alerte')
            ->withTimestamps();
    }
}
