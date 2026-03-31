<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_id',
        'description',
        'effectif',
    ];

    public function stock(){
        return $this->belongsTo(Stock::class);
    }
    public function sousCategories(){
        return $this->hasMany(SousCategory::class, 'description_id');
    }
    public function ventes() {
        return $this->hasMany(Vente::class, 'description_id');
    }

}
