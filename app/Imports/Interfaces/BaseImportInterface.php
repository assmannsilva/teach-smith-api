<?php
namespace App\Imports\Interfaces;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

interface BaseImportInterface extends
    ToCollection,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError,
    SkipsOnFailure
{

    /**
     * Returns the errors found during the import.
     *
     * @return array
     */
    public function getDataErrors(): array;

    /**
     * Returns the valid data after the import.
     *
     * @return array
     */
    public function getValidData(): array;
}
