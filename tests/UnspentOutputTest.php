<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Tests;

use AndKom\Bitcoin\Blockchain\UnspentOutput;
use PHPUnit\Framework\TestCase;

class UnspentOutputTest extends TestCase
{
    public function testPayToPubKeyCompressed()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x02;
        $uo->script = hex2bin('02633280c0a93b45217059013ddadab8d35b9a858336028fecdff64c6a5e068fad');

        $this->assertEquals($uo->getAddress(), '1A8nTwDWzKhV2UNEss6DtDBKuYfJH8TFDG');
    }

    public function testPayToPubKeyUncompressed()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x04;
        $uo->script = hex2bin('02633280c0a93b45217059013ddadab8d35b9a858336028fecdff64c6a5e068fad');

        $this->assertEquals($uo->getAddress(), '1PTYXwamXXgQoAhDbmUf98rY2Pg1pYXhin');
    }
}