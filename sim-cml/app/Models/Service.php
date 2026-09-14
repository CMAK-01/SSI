<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'direction_id',
        'nom',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function rapportMensuels(): HasMany
    {
        return $this->hasMany(RapportMensuel::class);
    }

    public function parcInformatiques(): HasMany
    {
        return $this->hasMany(ParcInformatique::class);
    }
}
