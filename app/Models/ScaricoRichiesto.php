<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScaricoRichiesto extends Model
{
    protected $fillable = [
        'ordine_id',
        'libro_id',
        'magazzino_id',
        'quantita',
        'stato',
    ];

    public function ordine() {
        return $this->belongsTo(Ordine::class);
    }

    public function libro() {
        return $this->belongsTo(Libro::class);
    }

    public function magazzino() {
        return $this->belongsTo(Magazzino::class);
    }
}
