<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroVendite extends Model {
    use HasFactory;

    protected $fillable = ['anagrafica_id', 'canale'];

    public function anagrafica() {
        return $this->belongsTo(Anagrafica::class, 'anagrafica_id');
    }

    public function dettagli() {
        return $this->hasMany(RegistroVenditeDettaglio::class, 'registro_vendita_id');
    }

}
