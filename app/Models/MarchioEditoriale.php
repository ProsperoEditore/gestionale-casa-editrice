<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarchioEditoriale extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'logo',
        'sito_web',
        'indirizzo_sede_legale',
        'partita_iva',
        'codice_univoco',
        'iban',
        'indirizzo_sede_logistica',
        'telefono',
        'email'
    ];
}