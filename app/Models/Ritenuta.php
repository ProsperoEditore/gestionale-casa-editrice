<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Ritenuta extends Model
{
    use HasFactory;

    protected $table = 'ritenute';

    protected $fillable = [
        'numero',
        'data_emissione',
        'nome_autore',
        'cognome_autore',
        'data_nascita',
        'luogo_nascita',
        'codice_fiscale',
        'iban',
        'indirizzo',
        'marchio_id',
        'prestazioni',
        'totale',
        'quota_esente',
        'imponibile',
        'ritenuta',
        'netto_pagare',
        'nota_iva',
        'marca_bollo',
        'data_pagamento_netto',
        'data_pagamento_ritenuta',
    ];

    protected $casts = [
        'prestazioni' => 'array',
        'data_emissione' => 'date',
        'data_nascita' => 'date',
        'data_pagamento_netto' => 'date',
        'data_pagamento_ritenuta' => 'date',
    ];

    // 🔁 Relazione con Marchio Editoriale
    public function marchio()
    {
        return $this->belongsTo(MarchioEditoriale::class, 'marchio_id');
    }

    // 📆 Calcolo automatico età autore
    public function getEtaAutoreAttribute()
    {
        return Carbon::parse($this->data_nascita)->age;
    }

    // 🧮 Determina se autore è under 35
    public function getIsUnder35Attribute()
    {
        return $this->eta_autore < 35;
    }
}
