<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Randonnee extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'site_visite',
        'date_randonnee',
        'frais',
        'numero',
        'description',
    ];

    protected $casts = [
        'date_randonnee' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
