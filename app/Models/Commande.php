<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
    use HasFactory;

    /**
     * Champs modifiables par le client (formulaire de création/édition).
     * 'reference' est exclue : générée automatiquement (voir booted()).
     * 'statut' est exclue : gérée uniquement par l'admin (voir changerStatut()).
     * 'montant' est exclue : calculée automatiquement depuis les lignes.
     */
    protected $fillable = [
        'user_id',
        'designation',
        'quantite',
        'note',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    /**
     * Statuts disponibles.
     */
    const STATUTS = [
        'en_attente' => 'En attente',
        'en_cours'   => 'En cours',
        'livre'      => 'Livré',
        'annule'     => 'Annulé',
    ];

    protected static function booted(): void
    {
        static::creating(function (Commande $commande) {
            // Statut par défaut, toujours forcé côté serveur.
            $commande->statut = 'en_attente';

            // Référence générée automatiquement si absente.
            if (empty($commande->reference)) {
                $commande->reference = self::genererReference();
            }
        });
    }

    /**
     * Génère une référence unique du type CMD-2026-0001.
     * Le numéro repart de 1 chaque nouvelle année.
     */
    public static function genererReference(): string
    {
        $annee = now()->format('Y');
        $prefixe = "CMD-{$annee}-";

        // Verrouille la table le temps de la génération pour éviter
        // deux références identiques en cas de créations simultanées.
        return \DB::transaction(function () use ($prefixe) {
            $dernier = self::where('reference', 'like', $prefixe . '%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('reference');

            $numero = 1;
            if ($dernier) {
                $numero = (int) substr($dernier, strlen($prefixe)) + 1;
            }

            return $prefixe . str_pad((string) $numero, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Relation : une commande appartient à un utilisateur (client).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : lignes de la commande (ingrédients + main d'œuvre).
     * Table attendue : commande_lignes
     * Colonnes attendues : commande_id, type (ingredient|main_oeuvre),
     *                       libelle, quantite, prix_unitaire
     */
    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeLigne::class);
    }

    /**
     * Calcule et enregistre le montant total à partir des lignes.
     * À appeler après ajout/suppression/modification de lignes.
     */
    public function recalculerMontant(): void
    {
        $total = $this->lignes()
            ->selectRaw('SUM(quantite * prix_unitaire) as total')
            ->value('total') ?? 0;

        $this->update(['montant' => $total]);
    }

    /**
     * Accesseur pratique : sous-total ingrédients uniquement.
     */
    public function getMontantIngredientsAttribute(): float
    {
        return (float) $this->lignes()
            ->where('type', 'ingredient')
            ->selectRaw('SUM(quantite * prix_unitaire) as total')
            ->value('total') ?? 0;
    }

    /**
     * Accesseur pratique : sous-total main d'œuvre uniquement.
     */
    public function getMontantMainOeuvreAttribute(): float
    {
        return (float) $this->lignes()
            ->where('type', 'main_oeuvre')
            ->selectRaw('SUM(quantite * prix_unitaire) as total')
            ->value('total') ?? 0;
    }

    /**
     * Seul l'admin doit appeler ceci (à protéger par middleware/policy
     * dans le contrôleur admin, pas ici).
     */
    public function changerStatut(string $statut): void
    {
        if (!array_key_exists($statut, self::STATUTS)) {
            throw new \InvalidArgumentException("Statut invalide : {$statut}");
        }

        $this->update(['statut' => $statut]);
    }

    /**
     * Accesseur : libellé lisible du statut.
     */
    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
