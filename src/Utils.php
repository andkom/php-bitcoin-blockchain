<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

/**
 * Class Utils
 * @package AndKom\Bitcoin\Blockchain
 */
class Utils
{
    /**
     * @param string $data
     * @param bool $raw
     * @return string
     */
    static public function hash256(string $data, bool $raw = false): string
    {
        return hash('sha256', hash('sha256', $data, true), $raw);
    }

    /**
     * @param string $data
     * @param bool $raw
     * @return string
     */
    static public function hash160(string $data, bool $raw = false): string
    {
        return hash('ripemd160', hash('sha256', $data, true), $raw);
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    static public function xor(string $key, string $value): string
    {
        $valueLen = strlen($value);
        $keyLen = strlen($key);
        $output = $value;

        for ($i = 0; $i < $valueLen; $i++) {
            $output[$i] = $value[$i] ^ $key[$i % $keyLen];
        }

        return $output;
    }

    /**
     * @param string $hash
     * @return string
     */
    static public function hashToHex(string $hash): string
    {
        return bin2hex(strrev($hash));
    }

    /**
     * @param string $hex
     * @return string
     */
    static public function hexToHash(string $hex): string
    {
        return strrev(hex2bin($hex));
    }

    /**
     * @param \GMP $gmp
     * @return string
     */
    static public function gmpToBin(\GMP $gmp): string
    {
        $hex = gmp_strval($gmp, 16);
        if (strlen($hex) % 2 != 0) {
            $hex = '0' . $hex;
        }
        return hex2bin($hex);
    }

    /**
     * @param string $data
     * @return \GMP
     */
    static public function binToGmp(string $data): \GMP
    {
        return gmp_init(bin2hex($data), 16);
    }
}