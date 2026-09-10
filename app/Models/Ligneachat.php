<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ligneachat extends Model
{
    use HasFactory;
    protected $table = 'lignes_achats';

    protected $fillable = [
        'achat_id',
        'stock_id',
        'quantite',
        'prix_unitaire',
        'total_ligne',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
    ];

    public function achat()
    {
        return $this->belongsTo(Achat::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
