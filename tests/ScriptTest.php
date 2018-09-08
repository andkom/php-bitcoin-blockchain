<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Tests;

use AndKom\PhpBitcoinBlockchain\Opcodes;
use AndKom\PhpBitcoinBlockchain\Script;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    public function testParseP2PK()
    {
        // [pubkey] OP_CHECKSIG

        $hex = '41'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new Script(hex2bin($hex));
        $operations = $script->parse();

        $this->assertEquals($operations[0], hex2bin('1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111'));
        $this->assertEquals($operations[1], Opcodes::OP_CHECKSIG);

        $operations = $script->parse(true);

        $this->assertEquals($operations[0], 'PUSHDATA(65)[1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111]');
        $this->assertEquals($operations[1], 'CHECKSIG');
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
        $operations = $script->parse();

        $this->assertEquals($operations[0], Opcodes::OP_DUP);
        $this->assertEquals($operations[1], Opcodes::OP_HASH160);
        $this->assertEquals($operations[2], hex2bin('1111111111111111111111111111111111111111'));
        $this->assertEquals($operations[3], Opcodes::OP_EQUALVERIFY);
        $this->assertEquals($operations[4], Opcodes::OP_CHECKSIG);

        $operations = $script->parse(true);

        $this->assertEquals($operations[0], 'DUP');
        $this->assertEquals($operations[1], 'HASH160');
        $this->assertEquals($operations[2], 'PUSHDATA(20)[1111111111111111111111111111111111111111]');
        $this->assertEquals($operations[3], 'EQUALVERIFY');
        $this->assertEquals($operations[4], 'CHECKSIG');
    }

    public function testParseP2SH()
    {
        // OP_HASH160 [hash] OP_EQUAL

        $hex = 'a9'; // OP_HASH160
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '1111111111111111111111111111111111111111'; // script hash
        $hex .= '87'; // OP_EQUAL

        $script = new Script(hex2bin($hex));
        $operations = $script->parse();

        $this->assertEquals($operations[0], Opcodes::OP_HASH160);
        $this->assertEquals($operations[1], hex2bin('1111111111111111111111111111111111111111'));
        $this->assertEquals($operations[2], Opcodes::OP_EQUAL);

        $operations = $script->parse(true);

        $this->assertEquals($operations[0], 'HASH160');
        $this->assertEquals($operations[1], 'PUSHDATA(20)[1111111111111111111111111111111111111111]');
        $this->assertEquals($operations[2], 'EQUAL');
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
        $operations = $script->parse();

        $this->assertEquals($operations[0], 2);
        $this->assertEquals($operations[1], hex2bin('1111111111111111111111111111111111111111'));
        $this->assertEquals($operations[2], hex2bin('2222222222222222222222222222222222222222'));
        $this->assertEquals($operations[3], hex2bin('3333333333333333333333333333333333333333'));
        $this->assertEquals($operations[4], 3);
        $this->assertEquals($operations[5], Opcodes::OP_CHECKMULTISIG);

        $operations = $script->parse(true);

        $this->assertEquals($operations[0], '2');
        $this->assertEquals($operations[1], 'PUSHDATA(20)[1111111111111111111111111111111111111111]');
        $this->assertEquals($operations[2], 'PUSHDATA(20)[2222222222222222222222222222222222222222]');
        $this->assertEquals($operations[3], 'PUSHDATA(20)[3333333333333333333333333333333333333333]');
        $this->assertEquals($operations[4], '3');
        $this->assertEquals($operations[5], 'CHECKMULTISIG');
    }
}