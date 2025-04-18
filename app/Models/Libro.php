<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ordine;
use App\Models\MarchioEditoriale;
use App\Models\Magazzino;
use App\Models\Giacenza;


class Libro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'libri';

    protected $fillable = [
        'isbn',
        'titolo',
        'marchio_editoriale_id',
        'collana',
        'data_pubblicazione',
        'anno_pubblicazione',
        'prezzo',
        'costo_produzione',
        'stato',
        'data_cessazione_commercio'
    ];

    public function marchio_editoriale()
    {
        return $this->belongsTo(MarchioEditoriale::class, 'marchio_editoriale_id');
    }

    public function ordini()
    {
        return $this->belongsToMany(Ordine::class, 'ordine_titoli', 'libro_id', 'ordine_id')
                    ->withPivot('quantita', 'prezzo_copertina', 'valore_vendita_lordo', 'sconto', 'netto_a_pagare');
    }
    
    




    protected static function boot()
    {
        parent::boot();
    
        static::updating(function ($libro) {
            $originalStato = $libro->getOriginal('stato');
            $newStato = $libro->stato;
    
            if ($originalStato !== $newStato) {
                self::spostaGiacenza($libro, $originalStato, $newStato);
            }
        });
    }
    
    public static function spostaGiacenza($libro, $originalStato, $newStato)
    {
        // Definizione delle associazioni Stato -> Magazzino
        $magazzini = [
            'C' => 'MIL snc',
            'A' => 'MIL snc - Accantonamenti',
            'FC' => 'MIL snc - Fuori Catalogo'
        ];
    
        // Trova il magazzino di origine basandosi sulla relazione con anagrafica
        $magazzinoOrigine = Magazzino::whereHas('anagrafica', function ($query) use ($originalStato, $magazzini) {
            $query->where('nome', $magazzini[$originalStato] ?? '');
        })->first();
    
        // Trova il magazzino di destinazione basandosi sulla relazione con anagrafica
        $magazzinoDestinazione = Magazzino::whereHas('anagrafica', function ($query) use ($newStato, $magazzini) {
            $query->where('nome', $magazzini[$newStato] ?? '');
        })->first();
    
        if ($magazzinoOrigine && $magazzinoDestinazione) {
            // Recuperiamo la giacenza dal magazzino di origine
            $giacenza = Giacenza::where('magazzino_id', $magazzinoOrigine->id)
                                ->where('isbn', $libro->isbn)
                                ->first();
    
            if ($giacenza) {
                // Sposta la giacenza al nuovo magazzino
                Giacenza::updateOrCreate(
                    [
                        'magazzino_id' => $magazzinoDestinazione->id,
                        'isbn' => $libro->isbn,
                    ],
                    [
                        'titolo' => $libro->titolo,
                        'quantita' => $giacenza->quantita,
                        'prezzo' => $giacenza->prezzo,
                        'data_aggiornamento' => now(),
                    ]
                );
    
                // Rimuove la giacenza dal vecchio magazzino
                $giacenza->delete();
            }
        }
    }
    
}