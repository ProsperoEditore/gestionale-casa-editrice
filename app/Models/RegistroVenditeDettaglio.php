<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroVenditeDettaglio extends Model
{
    use HasFactory;

    protected $table = 'registro_vendita_dettagli';

    protected $fillable = [
        'registro_vendita_id', 'ordine_id', 'data', 'periodo', 'isbn', 'titolo', 'quantita', 'prezzo', 'valore_lordo',
    ];

    public function registroVendite()
    {
        return $this->belongsTo(RegistroVendite::class, 'registro_vendita_id');
    }

    public function anagrafica()
    {
    return $this->belongsTo(Anagrafica::class, 'anagrafica_id');
    }

    public function libro()
    {
    return $this->belongsTo(Libro::class, 'titolo', 'titolo'); 
    }


}
