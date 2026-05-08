<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Commande extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'nom_produit',
        'numero_commande',
        'date_commande',
        'total_payements',
        'montant_paye',
        'reste_a_payer',
        'status',
        'payment_status',
        'payment_method',
        'address_livraison',
        'notes',
    ];

    protected $casts = [
        'date_commande'   => 'date',
        'total_payements' => 'decimal:2',
        'montant_paye'    => 'decimal:2',
        'reste_a_payer'   => 'decimal:2',
    ];

    // Constantes statuts
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PAID   = 'payée';
    const PAYMENT_UNPAID = 'nonpayé';
    const PAYMENT_ADVANCE = 'avance';

    const METHOD_CASH = 'cash';
    const METHOD_MOBILE_MONEY = 'mobile_money';
    const METHOD_VIREMENT = 'virement';
    const METHOD_CARTE = 'carte';

    protected static function booted(): void
    {
        static::saving(function ($commande) {
            // 1. Calcul du reste à payer
            $commande->reste_a_payer = max(0, $commande->total_payements - $commande->montant_paye);

            // 2. Détermination du statut de paiement
            if ($commande->montant_paye <= 0) {
                // Rien n'a été payé
                $commande->payment_status = self::PAYMENT_UNPAID;
            } elseif ($commande->montant_paye >= $commande->total_payements) {
                // Tout est payé
                $commande->payment_status = self::PAYMENT_PAID;
            } else {
                // Payé partiellement (avance)
                $commande->payment_status = self::PAYMENT_ADVANCE;
            }
        });
    }

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
// ── Scopes ──────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // Scopes paiement
    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_UNPAID);
    }

    public function scopeAdvance($query)
    {
        return $query->where('payment_status', self::PAYMENT_ADVANCE);
    }
}
