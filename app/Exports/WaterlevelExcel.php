<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class WaterlevelExcel implements FromCollection, WithHeadings
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
                'Station' => $record->waterlevellist->location,
                'Date' => $record->datetime,
                'Level In' => $record->lvl_in,
                'Level Out' => $record->lvl_out,
                'Level Actual' => $record->lvl_act,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Station',
            'Date',
            'Level In',
            'Level Out',
            'Level Actual',
        ];
    }
}
