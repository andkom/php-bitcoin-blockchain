<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use StephenHill\Base58;

/**
 * Class Utils
 * @package AndKom\PhpBitcoinBlockchain
 */
class Utils
{
    /**
     * @param string $data
     * @param bool $raw
     * @return string
     */
    static public function hash(string $data, bool $raw = false): string
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
     * @param string $hash160
     * @param int $network
     * @return string
     * @throws \Exception
     */
    static public function hash160ToAddress(string $hash160, int $network = 0x00): string
    {
        $hash160 = chr($network) . $hash160;
        $checksum = substr(static::hash($hash160, true), 0, 4);
        $address = $hash160 . $checksum;
        return (new Base58())->encode($address);
    }

    /**
     * @param string $pubKey
     * @param int $network
     * @return string
     * @throws \Exception
     */
    public static function pubKeyToAddress(string $pubKey, int $network = 0x00): string
    {
        return static::hash160ToAddress(static::hash160($pubKey, true), $network);
    }
}