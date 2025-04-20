<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SingoloExport implements FromCollection, WithHeadings
{
    protected $modelClass;
    protected $collection;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function collection()
    {
        $model = $this->modelClass;

        // Carica relazioni in base al tipo di modello
        switch (class_basename($model)) {
            case 'Ordine':
                $this->collection = $model::with('righe', 'anagrafica')->get();
                break;

            case 'RegistroVendite':
                $this->collection = $model::with('dettagli')->get();
                break;

            case 'RegistroTirature':
                $this->collection = $model::with('dettagli')->get();
                break;

            case 'Magazzino':
                $this->collection = $model::with('giacenze')->get();
                break;

            default:
                $this->collection = $model::all();
                break;
        }

        // Trasformazione per esportazione
        return $this->collection->map(function ($item) use ($model) {
            switch (class_basename($model)) {
                case 'Ordine':
                    return [
                        'ID' => $item->id,
                        'Cliente' => $item->anagrafica->nome ?? '',
                        'Data' => $item->data,
                        'Totale righe' => $item->righe->count(),
                        'Libri ordinati' => $item->righe->pluck('titolo')->implode(', ')
                    ];

                case 'Magazzino':
                    return [
                        'ID' => $item->id,
                        'Nome magazzino' => $item->nome,
                        'Categoria' => $item->categoria,
                        'Totale giacenze' => $item->giacenze->count(),
                        'Libri' => $item->giacenze->pluck('titolo')->implode(', ')
                    ];

                case 'RegistroVendite':
                    return [
                        'ID' => $item->id,
                        'Cliente' => $item->anagrafica->nome ?? '',
                        'Anno' => $item->anno,
                        'Totale righe' => $item->dettagli->count()
                    ];

                case 'RegistroTirature':
                    return [
                        'ID' => $item->id,
                        'Titolo' => $item->titolo,
                        'Totale righe' => $item->dettagli->count()
                    ];

                default:
                    return $item->toArray();
            }
        });
    }

    public function headings(): array
    {
        return $this->collection->isNotEmpty()
            ? array_keys($this->collection->first()->toArray())
            : [];
    }
}
