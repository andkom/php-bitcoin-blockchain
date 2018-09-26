<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Class Bitcoin
 * @package AndKom\Bitcoin\Blockchain\Network
 */
class Bitcoin implements NetworkInterface
{
    /**
     * @return int
     */
    public function getPayToPubKeyPrefix(): int
    {
        return 0x00;
    }

    /**
     * @return int
     */
    public function getPayToPubKeyHashPrefix(): int
    {
        return 0x00;
    }

    /**
     * @return int
     */
    public function getPayToScriptHashPrefix(): int
    {
        return 0x05;
    }

    /**
     * @return string
     */
    public function getBech32HumanReadablePart(): string
    {
        return 'bc';
    }
}