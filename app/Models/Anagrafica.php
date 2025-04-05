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
        'indirizzo_fatturazione',
        'indirizzo_spedizione',
        'partita_iva',
        'codice_fiscale',
        'email',
        'telefono',
        'pec',
        'codice_univoco'
    ];
}