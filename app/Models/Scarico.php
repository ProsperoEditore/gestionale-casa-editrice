<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ordine;

class Scarico extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordine_id',
        'altro_ordine',
        'info_spedizione',
        'destinatario_nome',
        'anagrafica_id'
    ];

    public function ordine()
    {
        return $this->belongsTo(Ordine::class);
    }
    
    public function anagrafica()
    {
        return $this->belongsTo(Anagrafica::class);
    }

    public function getDestinatarioAttribute()
    {
    return $this->anagrafica?->nome ?? $this->destinatario_nome;
    }

    
}
