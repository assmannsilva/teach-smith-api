<?php
namespace App\Imports;

use App\Imports\Interfaces\BaseImportInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UsersImport implements BaseImportInterface
{
    use Importable,SkipsErrors,SkipsFailures;

    protected array $validData = [];
    protected int $totalErrorsCount = 0;

    protected FormRequest $formRequest;

    public function setFormRequestValidation(String $formRequest)
    {
        $this->formRequest = new $formRequest;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $validation = $this->validateRow($row);
            if($validation->fails()) {
                $this->totalErrorsCount += 1;
                continue;
            }
            $this->validData[] = $row->toArray();

        }
    }

    private function validateRow(Collection $row)
    {
        return Validator::make($row->toArray(),
        $this->formRequest->rules(),["cpf.cpf" => "Document is not valid"]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getTotalErrorsCount(): int
    {
        return $this->totalErrorsCount;
    }

    public function getValidData(): array
    {
        return $this->validData ?? [];
    }
}