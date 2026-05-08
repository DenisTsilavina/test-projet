<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Description extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_id',
        'description',
        'unite_id',
        'effectif',
        'unite_id'
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

    public function unite()
    {
        return $this->hasOne(Unite::class , 'id' , 'unite_id');
    }
    public function getFullEffectifAttribute()
    {
        return number_format($this->effectif, 2) . ' ' . $this->unit->symbol;
    }
}
