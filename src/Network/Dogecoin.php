<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Class Dogecoin
 * @package AndKom\Bitcoin\Blockchain\Network
 */
class Dogecoin implements NetworkInterface
{
    /**
     * @return int
     */
    public function getPayToPubKeyPrefix(): int
    {
        return 0x1e;
    }

    /**
     * @return int
     */
    public function getPayToPubKeyHashPrefix(): int
    {
        return 0x1e;
    }

    /**
     * @return int
     */
    public function getPayToScriptHashPrefix(): int
    {
        return 0x16;
    }
}