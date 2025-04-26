<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Anagrafica;
use App\Models\Scarico;
use App\Models\Libro;

class Ordine extends Model
{
    use HasFactory;

    protected $table = 'ordines';

    protected $fillable = [
        'codice',
        'data',
        'tipo_ordine',
        'anagrafica_id',
        'canale',
        'causale',
        'condizioni_conto_deposito',
        'tempi_pagamento',
        'modalita_pagamento',
        'totale_netto_compilato',
        'specifiche_iva',
        'costo_spedizione',
        'altre_specifiche_iva',
        'pagato'

    ];

    public function anagrafica()
    {
        return $this->belongsTo(Anagrafica::class, 'anagrafica_id');
    }

    public function scarico()
    {
        return $this->hasOne(Scarico::class, 'ordine_id');
    }

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'ordine_titoli', 'ordine_id', 'libro_id')
                    ->withPivot('quantita', 'prezzo_copertina', 'valore_vendita_lordo', 'sconto', 'netto_a_pagare', 'info_spedizione'); 
    }

    public function registroVendita()
{
    return $this->hasOne(\App\Models\RegistroVendite::class, 'ordine_id');
}

    

}
