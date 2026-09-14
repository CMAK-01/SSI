<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParcInformatique extends Model
{
    protected $fillable = [
        'direction_id',
        'service_id',
        'type_equipement',
        'equipement_id_interne',
        'nom_materiel',
        'marque_modele',
        'os_logiciels',
        'utilisateur_assigne',
        'numero_bc',
        'date_achat',
        'prix_achat',
        'fin_garantie',
        'fournisseur',
        'etat',
        'notes_libres',
    ];

    protected $casts = [
        'date_achat' => 'date',
        'fin_garantie' => 'date',
        'prix_achat' => 'decimal:2',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function noteItems(): HasMany
    {
        return $this->hasMany(ParcInformatiqueNoteItem::class);
    }
}
