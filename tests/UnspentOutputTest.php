<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Tests;

use AndKom\Bitcoin\Blockchain\UnspentOutput;
use PHPUnit\Framework\TestCase;

class UnspentOutputTest extends TestCase
{
    public function testPayToPubKeyHash()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x00;
        $uo->script = hex2bin('cbc2986ff9aed6825920aece14aa6f5382ca5580');

        $this->assertEquals($uo->getAddress(), '1KaPHfvVWNZADup3Yc26SfVdkTDvvHySVX');
    }

    public function testPayToScriptHash()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x01;
        $uo->script = hex2bin('ee12c1c86ca0a2f60cf90e559eb10847645c44c1');

        $this->assertEquals($uo->getAddress(), '3PPqDTQgndCHiDxqeB3zayJxXB6UeKAiod');
    }

    public function testPayToPubKeyCompressed()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x02;
        $uo->script = hex2bin('0e46e79a2a8d12b9b5d12c7a91adb4e454edfae43c0a0cb805427d2ac7613fd9');

        $this->assertEquals($uo->getAddress(), '1P3rU1Nk1pmc2BiWC8dEy9bZa1ZbMp5jfg');
    }

    public function testPayToPubKeyCompressed2()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x03;
        $uo->script = hex2bin('0f34af4b908fb8eb2099accb56b8d157d49f6cfb691baa80fdd34f385efed961');

        $this->assertEquals($uo->getAddress(), '1CSxcc7PC8Fx5hZeSF4k9Rf6AMTeU3brJC');
    }

    public function testPayToPubKeyUncompressed()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x04;
        $uo->script = hex2bin('7a488354d9d5414de09b7121b80b973c991b76998ad68756d8cf4560c0ddcbe2');

        $this->assertEquals($uo->getAddress(), '18R1zbfonvzUf5nqzMa81kpt9XR37RWrcM');
    }

    public function testPayToPubKeyUncompressed2()
    {
        $uo = new UnspentOutput();
        $uo->type = 0x05;
        $uo->script = hex2bin('c863781e34ee29d96f493a002de08c27cdfb79268d774b566100e9ae2d06bdfe');

        $this->assertEquals($uo->getAddress(), '16No8BKxQuhKf5SkJvHmuGRPgFCuxfdYnx');
    }
}