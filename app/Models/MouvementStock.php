<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stocks';

    protected $fillable = [
        'stock_id',
        'type_mouvement',
        'reference_type',
        'reference_id',
        'quantite',
        'prix_unitaire',
        'date_mouvement',
        'utilisateur_id',
        'remarque',
    ];

    protected $casts = [
        'date_mouvement' => 'datetime',
        'quantite'       => 'decimal:2',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Enregistre un mouvement ET met à jour la quantité du stock en une
     * seule opération, pour ne jamais désynchroniser les deux.
     */
    public static function enregistrer(
        Stock $stock,
        string $typeMouvement,
        string $referenceType,
        ?int $referenceId,
        float $quantite,
        ?int $prixUnitaire = null,
        ?string $remarque = null
    ): self {
        $mouvement = self::create([
            'stock_id' => $stock->id,
            'type_mouvement' => $typeMouvement,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire,
            'date_mouvement' => now(),
            'utilisateur_id' => auth()->id(),
            'remarque' => $remarque,
        ]);

        if ($typeMouvement === 'entree') {
            $stock->increment('quantite', $quantite);
        } else {
            $stock->decrement('quantite', $quantite);
        }

        return $mouvement;
    }
}
