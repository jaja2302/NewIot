<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Weatherexcelyear implements WithMultipleSheets
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->records as $monthYear => $data) {
            $sheets[] = new class($monthYear, $data) implements FromView, WithTitle {
                private $monthYear;
                private $data;

                public function __construct($monthYear, $data)
                {
                    $this->monthYear = $monthYear;
                    $this->data = $data;
                }

                public function view(): View
                {
                    return view('exports.excel.export_weather_year', [
                        'data' => $this->data
                    ]);
                }

                public function title(): string
                {
                    return $this->monthYear;
                }
            };
        }
        // dd($sheets);
        return $sheets;
    }
}
