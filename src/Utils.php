<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

/**
 * Class Utils
 * @package AndKom\PhpBitcoinBlockchain
 */
class Utils
{
    static public function hash(string $data, bool $raw = false): string
    {
        return hash('sha256', hash('sha256', $data, $raw), $raw);
    }
}