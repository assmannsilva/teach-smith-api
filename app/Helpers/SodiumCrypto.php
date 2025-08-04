<?php

namespace App\Helpers;

class SodiumCrypto
{
    /**
     * Searchers for the cryptographic key in the configuration.
     * @param string $config_key_encrypt
     * @return string
     */
    public static function getCryptKey(string $config_key_encrypt): string
    {
        $crypt_key = config($config_key_encrypt);
        if(!$crypt_key) {
            throw new \RuntimeException("Chave de criptografia não configurada para {$config_key_encrypt}");
        }
        return sodium_hex2bin($crypt_key);
    }

     /**
     * Encrypts a value using the Sodium library.
     * @param string $value
     * @param string $value
     * @return string
     */
    public static function encrypt(string $value, string $key): string
    {
        $nonce = random_bytes(24);
        $cipher_text = sodium_crypto_secretbox($value, $nonce, $key);
        return bin2hex($nonce . $cipher_text);
    }
    
    /**
     * Decrypts a value using the Sodium library.
     * @param string $cipher_text
     * @param string $key
     * @return string
     */
    public static function decrypt(string $cipher_text, string $key): string
    {
        $decoded = hex2bin($cipher_text);
        $nonce = mb_substr($decoded, 0, 24, '8bit');
        $cipher = mb_substr($decoded, 24, null, '8bit');
        return sodium_crypto_secretbox_open($cipher, $nonce, $key);
    }

    /**
     * Generates a HMAC hash for the value.
     * @param string $value
     * @param string $index_key
     * @return string
     */
    public static function getIndex(string $value, string $index_key): string
    {
        return hash_hmac('sha256', strtolower($value), $index_key);
    }

}

?>