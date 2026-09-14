<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportMensuel extends Model
{
    protected $fillable = [
        'direction_id',
        'service_id',
        'date_intervention',
        'equipement_concerne',
        'utilisateur_equipement',
        'tache_effectuee',
        'observation',
    ];

    protected $casts = [
        'date_intervention' => 'date',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
