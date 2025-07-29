<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autore extends Model
{
    protected $fillable = [
        'nome',
        'cognome',
        'pseudonimo',
        'denominazione',
        'codice_fiscale',
        'data_nascita',
        'luogo_nascita',
        'iban',
        'indirizzo',
        'biografia',
        'foto',
    ];

    protected $table = 'autori';

    protected $casts = [
        'data_nascita' => 'date',
    ];

    public function libri()
    {
        return $this->belongsToMany(Libro::class, 'autore_libro');
    }

    public function getNomeCompletoAttribute()
    {
        if ($this->denominazione) return $this->denominazione;
        if ($this->pseudonimo) return $this->pseudonimo;
        return trim("{$this->nome} {$this->cognome}");
    }
}
