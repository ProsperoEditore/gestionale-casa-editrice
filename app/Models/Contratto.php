<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contratto extends Model
{
    use HasFactory;

    protected $table = 'contratti';

    protected $fillable = [
        'nome_contratto',
        'sconto_proprio_libro',
        'sconto_altri_libri',
        'royalties_vendite_indirette',
        'royalties_vendite_indirette_soglia_1',
        'royalties_vendite_indirette_percentuale_1',
        'royalties_vendite_indirette_soglia_2',
        'royalties_vendite_indirette_percentuale_2',
        'royalties_vendite_indirette_soglia_3',
        'royalties_vendite_indirette_percentuale_3',
        'royalties_vendite_dirette',
        'royalties_eventi'
    ];
}
