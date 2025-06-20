<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anagrafica extends Model
{
    use HasFactory;

    protected $table = 'anagraficas';

    protected $fillable = [
        'categoria',
        'nome',
        'cognome',
        'denominazione', 
        'partita_iva',
        'codice_fiscale',
        'email',
        'telefono',
        'pec',
        'codice_univoco',

        // indirizzo fatturazione (scomposto)
        'via_fatturazione',
        'civico_fatturazione',
        'cap_fatturazione',
        'comune_fatturazione',
        'provincia_fatturazione',
        'nazione_fatturazione',

        // indirizzo spedizione (scomposto)
        'via_spedizione',
        'civico_spedizione',
        'cap_spedizione',
        'comune_spedizione',
        'provincia_spedizione',
        'nazione_spedizione',

        // campi legacy (manteniamoli per ora)
        'indirizzo_fatturazione',
        'indirizzo_spedizione',
    ];

    protected $appends = ['nome_completo'];

    public function getNomeCompletoAttribute()
    {
        return $this->denominazione ?: trim("{$this->nome} {$this->cognome}");
    }
}
