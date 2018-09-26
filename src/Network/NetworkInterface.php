<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Network;

/**
 * Interface NetworkInterface
 * @package AndKom\Bitcoin\Blockchain\Network
 */
interface NetworkInterface
{
    /**
     * @return int
     */
    public function getPayToPubKeyPrefix(): int;

    /**
     * @return int
     */
    public function getPayToPubKeyHashPrefix(): int;

    /**
     * @return int
     */
    public function getPayToScriptHashPrefix(): int;

    /**
     * @return string
     */
    public function getBech32HumanReadablePart(): string;
}