<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StockUnite extends Pivot
{
    protected $table = 'stock_unite';

    public $incrementing = true; // car on a un id() dans la migration

    protected $fillable = [
        'stock_id',
        'unite_id',
        'quantite',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
    ];

    /**
     * Stock lié à ce pivot.
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Unité liée à ce pivot.
     */
    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }
}
