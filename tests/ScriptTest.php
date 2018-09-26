<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Tests;

use AndKom\Bitcoin\Blockchain\Script\Opcodes;
use AndKom\Bitcoin\Blockchain\Script\Script;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    public function testParse()
    {
        $hex = '00'; // OP_0
        $hex .= '01ff'; // push data 1
        $hex .= '4bffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'; // push data 75
        $hex .= '4f'; // OP_NEGATE
        $hex .= '51'; // OP_1
        $hex .= '60'; // OP_60
        $hex .= 'a9'; // OP_HASH160

        $script = new Script(hex2bin($hex));
        $operations = $script->parse();

        $this->assertEquals($operations[0]->code, Opcodes::OP_0);
        $this->assertEquals($operations[0]->data, '');
        $this->assertEquals($operations[0]->size, 0);
        $this->assertEquals((string)$operations[0], '0');

        $this->assertEquals($operations[1]->code, 0x01);
        $this->assertEquals($operations[1]->data, hex2bin('ff'));
        $this->assertEquals($operations[1]->size, 1);
        $this->assertEquals((string)$operations[1], 'PUSHDATA(1)[ff]');

        $this->assertEquals($operations[2]->code, 0x4b);
        $this->assertEquals($operations[2]->data, hex2bin('ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'));
        $this->assertEquals($operations[2]->size, 75);
        $this->assertEquals((string)$operations[2], 'PUSHDATA(75)[ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff]');

        $this->assertEquals($operations[3]->code, Opcodes::OP_1NEGATE);
        $this->assertEquals($operations[3]->data, chr(-1));
        $this->assertEquals($operations[3]->size, 1);
        $this->assertEquals((string)$operations[3], '1NEGATE');

        $this->assertEquals($operations[4]->code, Opcodes::OP_1);
        $this->assertEquals($operations[4]->data, chr(1));
        $this->assertEquals($operations[4]->size, 1);
        $this->assertEquals((string)$operations[4], '1');

        $this->assertEquals($operations[5]->code, Opcodes::OP_16);
        $this->assertEquals($operations[5]->data, chr(16));
        $this->assertEquals($operations[5]->size, 1);
        $this->assertEquals((string)$operations[5], '16');

        $this->assertEquals($operations[6]->code, Opcodes::OP_HASH160);
        $this->assertEquals($operations[6]->data, null);
        $this->assertEquals($operations[6]->size, 0);
        $this->assertEquals((string)$operations[6], 'HASH160');

        $this->assertEquals($script->getHumanReadable(), '0 PUSHDATA(1)[ff] PUSHDATA(75)[ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff] 1NEGATE 1 16 HASH160');
    }
}