<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedaLibro extends Model
{
    use HasFactory;

    protected $fillable = [
        'libro_id',
        'descrizione_breve',
        'sinossi',
        'strillo',
        'extra',
        'biografia_autore',
        'formato',
        'numero_pagine',
        'copertina_path',
        'copertina_stesa_path',
    ];

    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }
}
