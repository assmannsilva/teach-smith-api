<?php

namespace App\Observers;

use App\Helpers\SodiumCrypto;
use App\Models\User;

class UserObserver
{
    /** 
     * Realiza a criptografia dos tokens de sobrenome
     * @param User $user
     * @return void
     */
    private function encryptSurnameTokens(User $user): void
    {
        if (empty($user->surname)) return;

        $crypt_index = SodiumCrypto::getCryptKey("app.crypted_columns.users.surname_index");

        $surnames = explode(" ", $user->surname);
        $surnames_tokens = array_map(
            fn ($surname) => SodiumCrypto::getIndex($surname, $crypt_index),
            $surnames
        );

        $user->surname_tokens = json_encode($surnames_tokens);
    }


    /**
     * Handle the User "saving" event.
     */
    public function saving(User $user): void
    {
        $this->encryptSurnameTokens($user);
        $user->encryptColumnIndex("first_name", "first_name_index");
        $user->encryptColumnIndex("email", "email_index");
    }
}
