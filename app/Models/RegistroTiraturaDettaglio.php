<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroTiraturaDettaglio extends Model
{
    use HasFactory;

    protected $table = 'registro_tirature_dettagli';

    protected $fillable = [
        'registro_tirature_id',
        'titolo_id',
        'data',
        'copie_stampate',
        'prezzo_vendita_iva',
        'imponibile_relativo',
        'imponibile',
        'iva_4percento',
    ];

    public function titolo()
    {
        return $this->belongsTo(Libro::class, 'titolo_id');
    }

    public function registroTirature()
    {
        return $this->belongsTo(RegistroTirature::class, 'registro_tirature_id');
    }
}
