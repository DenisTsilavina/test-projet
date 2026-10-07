<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitFini extends Model
{
    use HasFactory;

    protected $table = 'produits_finis';

    protected $fillable = [
        'produit_id',
        'temps_preparation',
        'description',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function compositions()
    {
        return $this->hasMany(Composition::class);
    }

    /**
     * CHANGEMENT : compare à quantite_totale (magasin + réserve).
     */
    public function stockSuffisantPour(int $quantite): bool
    {
        foreach ($this->compositions as $ligne) {
            $dispo = $ligne->stock->quantite_totale ?? 0;

            if ($dispo < $ligne->quantite_necessaire * $quantite) {
                return false;
            }
        }

        return true;
    }
}
