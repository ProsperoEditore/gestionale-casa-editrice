<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magazzino extends Model
{
    use HasFactory;

    protected $table = 'magazzini';

    protected $fillable = [
        'anagrafica_id',
        'prossima_scadenza',
        'nome',
        'indirizzo',
        'telefono',
        'email'
    ];

    public function anagrafica()
    {
        return $this->belongsTo(Anagrafica::class);
    }

    public function giacenze()
    {
        return $this->hasMany(Giacenza::class);
    }
}
