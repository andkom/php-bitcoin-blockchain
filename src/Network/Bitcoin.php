<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Network;

/**
 * Class Bitcoin
 * @package AndKom\PhpBitcoinBlockchain\Network
 */
class Bitcoin
{
    const P2PKH_PREFIX = 0x00;
    const P2SH_PREFIX = 0x05;
    const BECH32_HRP = 'bc';
}