<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Tests;

use AndKom\PhpBitcoinBlockchain\Opcodes;
use AndKom\PhpBitcoinBlockchain\Script;
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

    public function testParseP2PK()
    {
        // [pubkey] OP_CHECKSIG

        $hex = '41'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new Script(hex2bin($hex));

        $this->assertTrue($script->isP2PK());
    }

    public function testParseP2PKH()
    {
        // OP_DUP OP_HASH160 [pubkey hash] OP_EQUALVERIFY OP_CHECKSIG

        $hex = '76'; // OP_DUP
        $hex .= 'a9'; // OP_HASH160
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111'; // pubkey hash
        $hex .= '88'; // OP_EQUALVERIFY
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new Script(hex2bin($hex));

        $this->assertTrue($script->isP2PKH());
    }

    public function testParseP2SH()
    {
        // OP_HASH160 [hash] OP_EQUAL

        $hex = 'a9'; // OP_HASH160
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111'; // script hash
        $hex .= '87'; // OP_EQUAL

        $script = new Script(hex2bin($hex));

        $this->assertTrue($script->isP2SH());
    }

    public function testParseMultisig()
    {
        // [numsigs] [...pubkeys...] [numpubkeys] OP_CHECKMULTISIG

        $hex = '52'; // OP_2
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111'; // pubkey hash
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '2222222222222222222222222222222222222222'; // pubkey hash
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '3333333333333333333333333333333333333333'; // pubkey hash
        $hex .= '53'; // OP_3
        $hex .= 'ae'; // OP_CHECKMULTISIG

        $script = new Script(hex2bin($hex));

        $this->assertTrue($script->isMultisig());
    }
}