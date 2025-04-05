<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RegistroTiraturaDettaglio;

class RegistroTirature extends Model
{
    use HasFactory;

    protected $table = 'registro_tirature';

    protected $fillable = [
        'periodo',
        'anno',
    ];

    public function dettagli()
    {
        return $this->hasMany(RegistroTiraturaDettaglio::class, 'registro_tirature_id');
    }
}
