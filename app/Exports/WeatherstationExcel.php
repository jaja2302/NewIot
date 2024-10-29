<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class WeatherstationExcel implements FromCollection, WithHeadings
{
    protected $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records->map(function ($record) {
            return [
                'idws' => $record->weatherstation->loc,
                'date' => $record->date,
                'windspeedkmh' => $record->windspeedkmh,
                'winddir' => $record->winddir,
                'rain_rate' => $record->rain_rate,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'idws',
            'date',
            'windspeedkmh',
            'winddir',
            'rain_rate',
        ];
    }
}
