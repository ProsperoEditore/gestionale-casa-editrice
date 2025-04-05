<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDettaglio extends Model
{
    use HasFactory;

    protected $table = 'report_dettagli';

    protected $fillable = [
        'report_id',
        'periodo',
        'quantita',
        'prezzo_unitario',
        'valore_vendita_lordo',
        'canale',
    ];

    protected $dates = ['periodo'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    
}
