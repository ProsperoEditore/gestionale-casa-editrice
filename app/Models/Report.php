<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_creazione',
        'libro_id',
        'contratto_id',
    ];

    public function libro()
    {
        return $this->belongsTo(\App\Models\Libro::class);
    }

    public function contratto()
    {
    return $this->belongsTo(\App\Models\Contratto::class);
    }

}