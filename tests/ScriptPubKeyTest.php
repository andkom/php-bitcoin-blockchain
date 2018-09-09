<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Tests;

use AndKom\PhpBitcoinBlockchain\ScriptPubKey;
use PHPUnit\Framework\TestCase;

class ScriptPubKeyTest extends TestCase
{
    // OP_RETURN ...
    public function testReturn()
    {
        $hex = '6a';

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isReturn());
    }

    // [pubkey] OP_CHECKSIG
    public function testParseP2PK()
    {
        $hex = '41'; // OP_PUSHDATA
        $hex .= '0479be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isP2PK());
        $this->assertEquals($script->getOutputAddress(), '1EHNa6Q4Jz2uvNExL497mE43ikXhwF6kZm');
    }

    // [pubkey] OP_CHECKSIG
    public function testParseP2PKCompressed()
    {
        $hex = '21'; // OP_PUSHDATA
        $hex .= '0279be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isP2PK());
        $this->assertEquals($script->getOutputAddress(), '1BgGZ9tcN4rm9KBzDn7KprQz87SZ26SAMH');
    }

    // OP_DUP OP_HASH160 [pubkey hash] OP_EQUALVERIFY OP_CHECKSIG
    public function testParseP2PKH()
    {
        $hex = '76'; // OP_DUP
        $hex .= 'a9'; // OP_HASH160
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '91b24bf9f5288532960ac687abb035127b1d28a5'; // pubkey hash
        $hex .= '88'; // OP_EQUALVERIFY
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isP2PKH());
        $this->assertEquals($script->getOutputAddress(), '1EHNa6Q4Jz2uvNExL497mE43ikXhwF6kZm');
    }

    // OP_HASH160 [hash] OP_EQUAL
    public function testParseP2SH()
    {
        $hex = 'a9'; // OP_HASH160
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '91b24bf9f5288532960ac687abb035127b1d28a5'; // script hash
        $hex .= '87'; // OP_EQUAL

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isP2SH());
        $this->assertEquals($script->getOutputAddress(), '3EyPVdtVrtMJ1XwPT9oiBrQysGpRY8LE9K');
    }

    // [numsigs] [...pubkeys...] [numpubkeys] OP_CHECKMULTISIG
    public function testParseMultisig()
    {
        $hex = '52'; // OP_2
        $hex .= '14'; // OP_PUSHDATA
        $hex .= '91b24bf9f5288532960ac687abb035127b1d28a5'; // pubkey hash
        $hex .= '14'; // OP_PUSHDATA
        $hex .= 'd6c8e828c1eca1bba065e1b83e1dc2a36e387a42'; // pubkey hash
        $hex .= '14'; // OP_PUSHDATA
        $hex .= 'ec7eced2c57ed1292bc4eb9bfd13c9f7603bc338'; // pubkey hash
        $hex .= '53'; // OP_3
        $hex .= 'ae'; // OP_CHECKMULTISIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isMultisig());
    }
}