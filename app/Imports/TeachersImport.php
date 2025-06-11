<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements
    ToCollection,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnError,
    SkipsOnFailure
{
    use Importable,SkipsErrors,SkipsFailures;

    protected array $dataErrors = [];
    protected array $validData = [];

    public function collection(Collection $collection)
    {
        foreach ($collection as $rowNumber => $row) {
            if($this->validateRow($row)->fails()) {
                $this->dataErrors[] = [
                    "errors" => $this->validateRow($row)->errors()->all(),
                    "row" => $rowNumber + 2 // Assuming the first row is the header, so we add 2 to get the actual row number
                ];
                continue;
            }
            $this->validData[] = $row->toArray();

        }
    }

    private function validateRow(Collection $row)
    {
        return Validator::make($row->toArray(),[
            'email' => 'required|email',
            'cpf' => 'required|string|cpf',
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'degree' => 'required|string',
            'hire_date' => 'required|date_format:Y-m-d',
        ],["cpf.cpf" => "Document is not valid"]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getDataErrors(): array
    {
        return $this->dataErrors ?? [];
    }

    public function getValidData(): array
    {
        return $this->validData ?? [];
    }
}