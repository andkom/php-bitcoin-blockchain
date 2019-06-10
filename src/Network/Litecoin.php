<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Class Litecoin
 * @package AndKom\Bitcoin\Blockchain\Network
 */
class Litecoin implements NetworkInterface, SegwitInterface
{
    /**
     * @return int
     */
    public function getPayToPubKeyPrefix(): int
    {
        return 0x30;
    }

    /**
     * @return int
     */
    public function getPayToPubKeyHashPrefix(): int
    {
        return 0x30;
    }

    /**
     * @return int
     */
    public function getPayToScriptHashPrefix(): int
    {
        return 0x32;
    }

    /**
     * @return string
     */
    public function getBech32HumanReadablePart(): string
    {
        return 'ltc';
    }
}