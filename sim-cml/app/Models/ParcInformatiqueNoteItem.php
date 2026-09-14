<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcInformatiqueNoteItem extends Model
{
    protected $fillable = [
        'parc_informatique_id',
        'parc_informatique_note_id',
        'autre_precision',
    ];

    public function parcInformatique(): BelongsTo
    {
        return $this->belongsTo(ParcInformatique::class);
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(ParcInformatiqueNote::class, 'parc_informatique_note_id');
    }
}
