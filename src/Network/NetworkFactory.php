<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Class NetworkFactory
 * @package AndKom\Bitcoin\Blockchain\Network
 */
class NetworkFactory
{
    /**
     * @return NetworkInterface
     */
    static public function getDefault(): NetworkInterface
    {
        return new Bitcoin();
    }
}