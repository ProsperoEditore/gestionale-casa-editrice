<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profilo extends Model
{
    protected $table = 'profilo';

    protected $fillable = [
        'codice_destinatario', 'pec', 'nazione', 'partita_iva', 'codice_fiscale', 'denominazione',
        'codice_eori', 'regime_fiscale', 'iban',
        'indirizzo_amministrativa', 'numero_civico_amministrativa', 'cap_amministrativa',
        'comune_amministrativa', 'provincia_amministrativa', 'nazione_amministrativa',
        'indirizzo_operativa', 'numero_civico_operativa', 'cap_operativa',
        'comune_operativa', 'provincia_operativa', 'nazione_operativa',
        'sede_unica', 'telefono', 'email',
        'numero_rea', 'capitale_sociale', 'provincia_ufficio_rea', 'tipologia_soci', 'stato_liquidazione',
        'rapp_nazione', 'rapp_partita_iva', 'rapp_codice_fiscale', 'rapp_denominazione', 'rapp_codice_eori'
    ];
} 