<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Tests;

use AndKom\PhpBitcoinBlockchain\Script;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    public function testParseP2PK()
    {
        $hex = '4104678afdb0fe5548271967f1a67130b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c384df7ba0b8d578a4c702b6bf11d5fac';

        $script = new Script(hex2bin($hex));
        $operations = $script->parse();

        $this->assertEquals(bin2hex($operations[0]), '04678afdb0fe5548271967f1a67130b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c384df7ba0b8d578a4c702b6bf11d5f');
        $this->assertEquals($operations[1], 172);
    }

    public function testParseReadableP2PK()
    {
        $hex = '4104678afdb0fe5548271967f1a67130b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c384df7ba0b8d578a4c702b6bf11d5fac';

        $script = new Script(hex2bin($hex));
        $operations = $script->parse(true);

        $this->assertEquals($operations[0], 'PUSHDATA(65)[04678afdb0fe5548271967f1a67130b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c384df7ba0b8d578a4c702b6bf11d5f]');
        $this->assertEquals($operations[1], 'OP_CHECKSIG');
    }
}