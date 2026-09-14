<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    protected $fillable = [
        'code',
        'nom',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
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
