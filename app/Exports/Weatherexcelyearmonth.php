<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class Weatherexcelyearmonth implements WithMultipleSheets
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->records as $sheetType => $data) {
            $sheets[] = new class($sheetType, $data) implements FromView, WithTitle, ShouldAutoSize {
                private $sheetType;
                private $data;

                public function __construct($sheetType, $data)
                {
                    $this->sheetType = $sheetType;
                    $this->data = $data;
                }

                public function view(): View
                {
                    return view('exports.excel.export_by_year_month', [
                        'data' => $this->data
                    ]);
                }

                public function title(): string
                {
                    return $this->sheetType;
                }
            };
        }
        // dd($sheets);
        return $sheets;
    }
}