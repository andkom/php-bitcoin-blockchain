<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Interface SegwitInterface
 * @package AndKom\Bitcoin\Blockchain\Network
 */
interface SegwitInterface
{
    /**
     * @return string
     */
    public function getBech32HumanReadablePart(): string;
}