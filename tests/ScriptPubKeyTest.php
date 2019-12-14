<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Tests;

use AndKom\Bitcoin\Blockchain\Script\ScriptPubKey;
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
        $hex = '41'; // 65
        $hex .= '0479be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToPubKey());
        $this->assertEquals($script->getOutputAddress(), '1EHNa6Q4Jz2uvNExL497mE43ikXhwF6kZm');
    }

    // [pubkey] OP_CHECKSIG
    public function testParseP2PKCompressed()
    {
        $hex = '21'; // 33
        $hex .= '0279be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798'; // pubkey
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToPubKey());
        $this->assertEquals($script->getOutputAddress(), '1BgGZ9tcN4rm9KBzDn7KprQz87SZ26SAMH');
    }

    // OP_DUP OP_HASH160 [pubkey hash] OP_EQUALVERIFY OP_CHECKSIG
    public function testParseP2PKH()
    {
        $hex = '76'; // OP_DUP
        $hex .= 'a9'; // OP_HASH160
        $hex .= '14'; // 20
        $hex .= '91b24bf9f5288532960ac687abb035127b1d28a5'; // pubkey hash
        $hex .= '88'; // OP_EQUALVERIFY
        $hex .= 'ac'; // OP_CHECKSIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToPubKeyHash());
        $this->assertEquals($script->getOutputAddress(), '1EHNa6Q4Jz2uvNExL497mE43ikXhwF6kZm');
    }

    // OP_HASH160 [hash] OP_EQUAL
    public function testParseP2SH()
    {
        $hex = 'a9'; // OP_HASH160
        $hex .= '14'; // 20
        $hex .= '91b24bf9f5288532960ac687abb035127b1d28a5'; // script hash
        $hex .= '87'; // OP_EQUAL

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToScriptHash());
        $this->assertEquals($script->getOutputAddress(), '3EyPVdtVrtMJ1XwPT9oiBrQysGpRY8LE9K');
    }

    // [numsigs] [...pubkeys...] [numpubkeys] OP_CHECKMULTISIG
    public function testParseMultisig1()
    {
        $hex = '51'; // 1
        $hex .= '21'; // 33
        $hex .= '0279be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798'; // pubkey
        $hex .= '41'; // 65
        $hex .= '0479be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8'; // pubkey
        $hex .= '52'; // 2
        $hex .= 'ae'; // OP_CHECKMULTISIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToMultisig());
        $this->assertEquals($script->getOutputAddress(), '1:2:1BgGZ9tcN4rm9KBzDn7KprQz87SZ26SAMH,1EHNa6Q4Jz2uvNExL497mE43ikXhwF6kZm');
    }

    public function testParseMultisig2()
    {
        $hex = '51'; // 1
        $hex .= '21'; // 33
        $hex .= '035b219535a5e9238bccc25c71ce4fddf024f5d9b452887225d23057b870d444b9'; // pubkey
        $hex .= '41'; // 65
        $hex .= '0479be667ef9dcbbac55a06295ce870b07029bfcdb2dce28d959f2815b16f81798483ada7726a3c4655da4fbfc0e1108a8fd17b448a68554199c47d08ffb10d4b8'; // pubkey
        $hex .= '51'; // 1
        $hex .= 'ae'; // OP_CHECKMULTISIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertFalse($script->isPayToMultisig());
    }

    public function testParseMultisig3()
    {
        $hex = '51'; // 1
        $hex .= '21'; // 33
        $hex .= '005b219535a5e9238bccc25c71ce4fddf024f5d9b452887225d23057b870d444b9'; // pubkey
        $hex .= '51'; // 1
        $hex .= 'ae'; // OP_CHECKMULTISIG

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertFalse($script->isPayToMultisig());
    }

    // [segwit ver] [pubkey hash]
    public function testParseP2WPKH()
    {
        $hex = '00'; // segwit version
        $hex .= '14'; // 20
        $hex .= '536523b38a207338740797cd03c3312e81408d53'; // pubkey hash

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToWitnessPubKeyHash());
        $this->assertEquals($script->getOutputAddress(), 'bc1q2djj8vu2ypensaq8jlxs8se396q5pr2n3sa2wn');
    }

    // [segwit ver] [pubkey hash]
    public function testParseP2WSH()
    {
        $hex = '00'; // segwit version
        $hex .= '20'; // 20
        $hex .= '701a8d401c84fb13e6baf169d59684e17abd9fa216c8cc5b9fc63d622ff8c58d'; // hash

        $script = new ScriptPubKey(hex2bin($hex));

        $this->assertTrue($script->isPayToWitnessScriptHash());
        $this->assertEquals($script->getOutputAddress(), 'bc1qwqdg6squsna38e46795at95yu9atm8azzmyvckulcc7kytlcckxswvvzej');
    }
}