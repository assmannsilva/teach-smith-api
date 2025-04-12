<?php
namespace App\Models\Traits;

use App\Helpers\SodiumCrypto;

trait HasEncrypt {
    /**
     * Cria os callables para os atributos criptografados (Laravel fica tentando chamar a função se retorna um Attribute)
     * @param string $column
     * @return Array $callables
     */
    protected function makeEncryptedAttributeCallables(string $column): Array
    {
        return [
            function ($value) use ($column) {
                if ($this->isDirty($column)) return $value;
                if (!$value) return null;
                $crypt_key = SodiumCrypto::getCryptKey("app.crypted_columns.{$this->getTable()}.$column");
                return SodiumCrypto::decrypt($value, $crypt_key);
            },
            function ($value) use ($column) {
                if (!$value) return null;
                $crypt_key = SodiumCrypto::getCryptKey("app.crypted_columns.{$this->getTable()}.$column");
                return SodiumCrypto::encrypt($value, $crypt_key);
            }
        ];
    }

    /**
     * Realiza a criptografia da coluna index de determinada coluna de referência
     * @return void
     */
    protected function encryptColumnIndex(string $reference_column, string $index_column): void
    {
        if($this->{$reference_column} === null) return;
        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_columns.{$this->getTable()}.$index_column");

        $this->attributes[$index_column] = SodiumCrypto::getIndex($this->{$reference_column}, $crypt_index);
    }
}