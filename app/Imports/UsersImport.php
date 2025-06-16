<?php
namespace App\Imports;

use App\Http\Requests\Users\Invites\InviteTeachersRequest;
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

    protected array $dataErrors = [];
    protected array $validData = [];
    protected FormRequest $formRequest;

    public function setFormRequestValidation(String $formRequest)
    {
        $this->formRequest = new $formRequest;
    }

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

    public function getDataErrors(): array
    {
        return $this->dataErrors ?? [];
    }

    public function getValidData(): array
    {
        return $this->validData ?? [];
    }
}