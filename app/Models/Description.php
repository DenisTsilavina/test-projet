<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Description extends Model
{

    use HasFactory;

    protected $fillable = [
        'stock_id',
        'description',
        'effectif',
        'unite_id',
        'region',
        'image',
    ];

    /**
     * Le stock auquel appartient cette description.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * L'unité de mesure (Kg, Litre, Pièce, etc.).
     */
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    /**
     * CHANGEMENT : sousCategories() (pluriel, hasMany) -> sousCategorie()
     * (singulier, hasOne). Chaque description porte une seule sous-catégorie
     * (relation 1-1 via description_id sur sous_categories), comme utilisé
     * partout ailleurs dans VenteController et Vente.php.
     */
    public function sousCategorie()
    {
        return $this->hasOne(SousCategory::class, 'description_id');
    }

    /**
     * Les ventes liées à cette description.
     */
    public function vente()
    {
        return $this->hasMany(Vente::class, 'description_id');
    }
    /**
     * insertion des immage de produit /stocks
     */

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Image par défaut si aucun fichier n'est téléversé
        return asset('images/default-product.png');
    }
}
