<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected  $fillable=[
        'name_stock',
        'description_stock',
        'persn_stock',
        'date_stock',
    ];
    protected $casts = [
        'date_stock' => 'datetime:d/m/Y H:i',
    ];
    public function descriptions()
    {
        return $this->hasMany(Description::class);
    }
}
