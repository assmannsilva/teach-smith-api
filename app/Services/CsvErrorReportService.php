<?php

namespace App\Services\User;
use Psy\Util\Str;

class CsvErrorReportService {

    /**
     * Generate a temporary CSV file containing duplicated emails and their errors.
     *
     * @param array $headers
     * @param array $data
     * @return string Public URL to download the file
     */
    public function handle(array $headers, array $data): string
    {
        $fileName = 'error_report_' . Str::uuid() . '.csv';
        $path = 'tmp/' . $fileName;

        Storage::makeDirectory('tmp');
        $stream = fopen(storage_path('app/' . $path), 'w');

        fputcsv($stream, $headers);
        foreach ($data as $row) fputcsv($stream, $row);
        fclose($stream);

        return route('error-report', ['filename' => $fileName]);
    }
}