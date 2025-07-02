<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SollecitoOrdineLog extends Model
{
    protected $fillable = ['ordine_id'];

    public function ordine()
    {
        return $this->belongsTo(\App\Models\Ordine::class);
    }
}
