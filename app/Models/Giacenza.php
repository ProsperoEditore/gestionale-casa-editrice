<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giacenza extends Model
{
    use HasFactory;

    protected $table = 'giacenze';

    protected $fillable = [
        'magazzino_id',
        'libro_id',
        'isbn',
        'titolo',
        'quantita',
        'prezzo',
        'sconto',
        'costo_produzione',
        'data_ultimo_aggiornamento',
        'note',
        'ordine_id'
    ];

    protected $casts = [
        'data_ultimo_aggiornamento' => 'datetime',
    ];

    public function magazzino()
    {
        return $this->belongsTo(Magazzino::class);
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }
}
