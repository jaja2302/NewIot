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

use function Laravel\Prompts\error;

class WaterlevelImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    private $waterStationId;

    public function __construct($waterStationId)
    {
        $this->waterStationId = $waterStationId;
    }

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
            if (empty($row[1])) {
                return null;
            }

            // Get the station from the passed ID instead of the CSV
            $waterLevelList = Waterlevellist::findOrFail($this->waterStationId);
            $stationName = $waterLevelList->location;
            $stationNameInExcel = trim($row[1]); // Trim untuk menghindari spasi yang tidak diinginkan

            // Validasi kecocokan nama station
            if (strtolower($stationName) !== strtolower($stationNameInExcel)) {
                throw new \Exception(
                    "Station name mismatch. Selected station: '{$stationName}', " .
                        "Excel station: '{$stationNameInExcel}'. " .
                        "Please make sure the station names match exactly."
                );
            }

            $formattedDate = $this->parseDate($row[2]);

            // Remove the firstOrCreate since we already have the station
            $existingRecord = Waterlevel::where('idwl', $waterLevelList->id)
                ->where('datetime', $formattedDate)
                ->first();

            if ($existingRecord) {
                DB::commit();
                return null;
            }

            $waterlevel = new Waterlevel([
                'idwl' => $waterLevelList->id,
                'station_name' => $stationName,
                'level_blok' => $row[3],
                'level_parit' => $row[4],
                'sensor_distance' => $row[5],
                'datetime' => $formattedDate,
            ]);

            DB::commit();
            return $waterlevel;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing row:', [
                'row_number' => $this->getCurrentRowNumber(),
                'station_id' => $this->waterStationId,
                'station_name' => $stationName ?? 'unknown',
                'station_name_in_excel' => $stationNameInExcel ?? 'unknown',
                'datetime' => $row[2] ?? 'empty',
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
