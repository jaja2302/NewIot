<?php

namespace App\Imports;

use App\Models\Waterlevel;
use App\Models\Waterlevellist;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class WaterlevelImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    public function startRow(): int
    {
        return 1; // Start from first row since we don't have headers
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8'
        ];
    }

    public function model(array $row)
    {
        try {
            // Show loading notification
            // Notification::make()
            //     ->title('Uploading...')
            //     ->info()
            //     ->send();

            // Skip if row is empty
            if (empty($row[1])) {
                // \Log::info('Skipping empty row');
                return null;
            }

            $stationName = trim($row[1]);
            // \Log::info('Processing station:', ['name' => $stationName]);

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

            // Check if the record already exists
            $existingRecord = Waterlevel::where('idwl', $waterLevelList->id)
                ->where('datetime', $row[2])
                ->first();

            if ($existingRecord) {
                // \Log::info('Skipping duplicate record:', [
                //     'idwl' => $waterLevelList->id,
                //     'datetime' => $row[2]
                // ]);
                return null; // Skip if record exists
            }

            // \Log::info('Station processed:', [
            //     'id' => $waterLevelList->id,
            //     'name' => $stationName,
            //     'datetime' => $row[2],
            //     'level_in' => $row[3]
            // ]);

            return new Waterlevel([
                'idwl' => $waterLevelList->id,
                'datetime' => $row[2],
                'lvl_in' => $row[3],
                'lvl_out' => 0,
                'lvl_act' => 0,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error processing row:', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
