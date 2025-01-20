<?php

namespace App\Imports;

use App\Models\Waterlevel;
use App\Models\Waterlevellist;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class WaterlevelImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    public function startRow(): int
    {
        return 2; // Start from second row to skip header
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8'
        ];
    }

    private function parseDate($dateString)
    {
        // Array of possible date formats to try
        $formats = [
            'n/j/Y G:i',       // 1/3/2025 9:56 (single digit month/day)
            'n/j/Y H:i',       // 1/3/2025 09:56 (24-hour format)
            'm/d/Y G:i',       // 01/03/2025 9:56
            'm/d/Y H:i',       // 01/03/2025 09:56
            'd/m/Y H:i',       // 03/01/2025 09:56
            'Y-m-d H:i',       // 2025-01-03 09:56
            'Y/m/d H:i',       // 2025/01/03 09:56
            'Y-m-d H:i:s'      // 2025-01-03 09:56:31 (tambahan format dengan detik)
        ];

        // Hapus quotes jika ada
        $dateString = trim($dateString, "'\" ");

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateString)
                    ->format('Y-m-d H:i:s');
            } catch (InvalidFormatException $e) {
                continue;
            }
        }

        // If no format matches, throw an exception with more detail
        throw new \Exception("Unable to parse date: '{$dateString}'. Expected formats: M/D/YYYY H:MM or YYYY-MM-DD HH:MM:SS");
    }

    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            // \Log::info('Processing row:', [
            //     'row_number' => $this->getCurrentRowNumber(),
            //     'station_id' => $row[0] ?? 'empty',
            //     'station_name' => $row[1] ?? 'empty',
            //     'datetime' => $row[2] ?? 'empty',
            //     'water_level' => $row[3] ?? 'empty',
            //     'raw_data' => $row
            // ]);

            if (empty($row[1])) {
                return null;
            }

            $stationName = trim($row[1]);

            // Use the new parseDate method
            $formattedDate = $this->parseDate($row[2]);

            $waterLevelList = Waterlevellist::firstOrCreate(
                ['location' => $stationName],
                [
                    'location' => $stationName,
                    'lat' => 0,
                    'lon' => 0,
                    'batas_atas_air' => 0,
                    'batas_bawah_air' => 0
                ]
            );

            $existingRecord = Waterlevel::where('idwl', $waterLevelList->id)
                ->where('datetime', $formattedDate)
                ->first();

            if ($existingRecord) {
                DB::commit();
                return null;
            }

            $waterlevel = new Waterlevel([
                'idwl' => $waterLevelList->id,
                'datetime' => $formattedDate,
                'lvl_in' => $row[3],
                'lvl_out' => 0,
                'lvl_act' => 0,
            ]);

            DB::commit();
            return $waterlevel;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing row:', [
                'row_number' => $this->getCurrentRowNumber(),
                'station_id' => $row[0] ?? 'empty',
                'station_name' => $row[1] ?? 'empty',
                'datetime' => $row[2] ?? 'empty',
                'water_level' => $row[3] ?? 'empty',
                'raw_data' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Add this new method to track row number
    private $rowNumber = 0;

    public function getCurrentRowNumber(): int
    {
        return ++$this->rowNumber;
    }
}
