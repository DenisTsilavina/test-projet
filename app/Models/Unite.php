<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unite extends Model
{
    protected $table = 'unites';

    protected $fillable = [
        'description_id',
        'name',
        'symbol',
        'type',
        'factor',
        'is_base',
    ];

    protected $casts = [
        'factor'  => 'float',
        'is_base' => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function description(): BelongsTo
    {
        return $this->belongsTo(Description::class, 'description_id', 'id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeBase(Builder $query): Builder
    {
        return $query->where('is_base', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Retourne l'unité de base pour un type donné. */
    public static function baseOf(string $type): ?self
    {
        return static::ofType($type)->base()->first();
    }

    /** Retourne toutes les unités du même type que cette unité (sauf elle-même). */
    public function siblings(): Collection
    {
        return static::ofType($this->type)
            ->where('id', '!=', $this->id)
            ->get();
    }
}
