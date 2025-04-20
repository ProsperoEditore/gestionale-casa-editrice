<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Model;

class SingoloExport implements FromCollection, WithHeadings
{
    protected $model;

    public function __construct(string $model)
    {
        $this->model = new $model;
    }

    public function collection()
    {
        return $this->model->newQuery()->get();
    }

    public function headings(): array
    {
        return array_keys($this->model->getAttributes());
    }
}
