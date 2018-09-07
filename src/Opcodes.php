<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

/**
 * Class Opcodes
 * @package AndKom\PhpBitcoinBlockchain
 */
class Opcodes
{
    // push value
    const OP_0 = 0x00;
    const OP_FALSE = self::OP_0;
    const OP_PUSHDATA1 = 0x4c;
    const OP_PUSHDATA2 = 0x4d;
    const OP_PUSHDATA4 = 0x4e;
    const OP_1NEGATE = 0x4f;
    const OP_RESERVED = 0x50;
    const OP_1 = 0x51;
    const OP_TRUE = self::OP_0;
    const OP_2 = 0x52;
    const OP_3 = 0x53;
    const OP_4 = 0x54;
    const OP_5 = 0x55;
    const OP_6 = 0x56;
    const OP_7 = 0x57;
    const OP_8 = 0x58;
    const OP_9 = 0x59;
    const OP_10 = 0x5a;
    const OP_11 = 0x5b;
    const OP_12 = 0x5c;
    const OP_13 = 0x5d;
    const OP_14 = 0x5e;
    const OP_15 = 0x5f;
    const OP_16 = 0x60;

    // control
    const OP_NOP = 0x61;
    const OP_VER = 0x62;
    const OP_IF = 0x63;
    const OP_NOTIF = 0x64;
    const OP_VERIF = 0x65;
    const OP_VERNOTIF = 0x66;
    const OP_ELSE = 0x67;
    const OP_ENDIF = 0x68;
    const OP_VERIFY = 0x69;
    const OP_RETURN = 0x6a;

    // stack ops
    const OP_TOALTSTACK = 0x6b;
    const OP_FROMALTSTACK = 0x6c;
    const OP_2DROP = 0x6d;
    const OP_2DUP = 0x6e;
    const OP_3DUP = 0x6f;
    const OP_2OVER = 0x70;
    const OP_2ROT = 0x71;
    const OP_2SWAP = 0x72;
    const OP_IFDUP = 0x73;
    const OP_DEPTH = 0x74;
    const OP_DROP = 0x75;
    const OP_DUP = 0x76;
    const OP_NIP = 0x77;
    const OP_OVER = 0x78;
    const OP_PICK = 0x79;
    const OP_ROLL = 0x7a;
    const OP_ROT = 0x7b;
    const OP_SWAP = 0x7c;
    const OP_TUCK = 0x7d;

    // splice ops
    const OP_CAT = 0x7e;
    const OP_SUBSTR = 0x7f;
    const OP_LEFT = 0x80;
    const OP_RIGHT = 0x81;
    const OP_SIZE = 0x82;

    // bit logic
    const OP_INVERT = 0x83;
    const OP_AND = 0x84;
    const OP_OR = 0x85;
    const OP_XOR = 0x86;
    const OP_EQUAL = 0x87;
    const OP_EQUALVERIFY = 0x88;
    const OP_RESERVED1 = 0x89;
    const OP_RESERVED2 = 0x8a;

    // numeric
    const OP_1ADD = 0x8b;
    const OP_1SUB = 0x8c;
    const OP_2MUL = 0x8d;
    const OP_2DIV = 0x8e;
    const OP_NEGATE = 0x8f;
    const OP_ABS = 0x90;
    const OP_NOT = 0x91;
    const OP_0NOTEQUAL = 0x92;

    const OP_ADD = 0x93;
    const OP_SUB = 0x94;
    const OP_MUL = 0x95;
    const OP_DIV = 0x96;
    const OP_MOD = 0x97;
    const OP_LSHIFT = 0x98;
    const OP_RSHIFT = 0x99;

    const OP_BOOLAND = 0x9a;
    const OP_BOOLOR = 0x9b;
    const OP_NUMEQUAL = 0x9c;
    const OP_NUMEQUALVERIFY = 0x9d;
    const OP_NUMNOTEQUAL = 0x9e;
    const OP_LESSTHAN = 0x9f;
    const OP_GREATERTHAN = 0xa0;
    const OP_LESSTHANOREQUAL = 0xa1;
    const OP_GREATERTHANOREQUAL = 0xa2;
    const OP_MIN = 0xa3;
    const OP_MAX = 0xa4;

    const OP_WITHIN = 0xa5;

    // crypto
    const OP_RIPEMD160 = 0xa6;
    const OP_SHA1 = 0xa7;
    const OP_SHA256 = 0xa8;
    const OP_HASH160 = 0xa9;
    const OP_HASH256 = 0xaa;
    const OP_CODESEPARATOR = 0xab;
    const OP_CHECKSIG = 0xac;
    const OP_CHECKSIGVERIFY = 0xad;
    const OP_CHECKMULTISIG = 0xae;
    const OP_CHECKMULTISIGVERIFY = 0xaf;

    // expansion
    const OP_NOP1 = 0xb0;
    const OP_CHECKLOCKTIMEVERIFY = 0xb1;
    const OP_NOP2 = self::OP_CHECKLOCKTIMEVERIFY;
    const OP_CHECKSEQUENCEVERIFY = 0xb2;
    const OP_NOP3 = self::OP_CHECKSEQUENCEVERIFY;
    const OP_NOP4 = 0xb3;
    const OP_NOP5 = 0xb4;
    const OP_NOP6 = 0xb5;
    const OP_NOP7 = 0xb6;
    const OP_NOP8 = 0xb7;
    const OP_NOP9 = 0xb8;
    const OP_NOP10 = 0xb9;

    const OP_INVALIDOPCODE = 0xff;

    // op names
    static public $names = [
        0x00 => 'OP_0',
        0x4c => 'OP_PUSHDATA1',
        0x4d => 'OP_PUSHDATA2',
        0x4e => 'OP_PUSHDATA4',
        0x4f => 'OP_1NEGATE',
        0x50 => 'OP_RESERVED',
        0x51 => 'OP_1',
        0x52 => 'OP_2',
        0x53 => 'OP_3',
        0x54 => 'OP_4',
        0x55 => 'OP_5',
        0x56 => 'OP_6',
        0x57 => 'OP_7',
        0x58 => 'OP_8',
        0x59 => 'OP_9',
        0x5a => 'OP_10',
        0x5b => 'OP_11',
        0x5c => 'OP_12',
        0x5d => 'OP_13',
        0x5e => 'OP_14',
        0x5f => 'OP_15',
        0x60 => 'OP_16',

        // control
        0x61 => 'OP_NOP',
        0x62 => 'OP_VER',
        0x63 => 'OP_IF',
        0x64 => 'OP_NOTIF',
        0x65 => 'OP_VERIF',
        0x66 => 'OP_VERNOTIF',
        0x67 => 'OP_ELSE',
        0x68 => 'OP_ENDIF',
        0x69 => 'OP_VERIFY',
        0x6a => 'OP_RETURN',

        // stack ops
        0x6b => 'OP_TOALTSTACK',
        0x6c => 'OP_FROMALTSTACK',
        0x6d => 'OP_2DROP',
        0x6e => 'OP_2DUP',
        0x6f => 'OP_3DUP',
        0x70 => 'OP_2OVER',
        0x71 => 'OP_2ROT',
        0x72 => 'OP_2SWAP',
        0x73 => 'OP_IFDUP',
        0x74 => 'OP_DEPTH',
        0x75 => 'OP_DROP',
        0x76 => 'OP_DUP',
        0x77 => 'OP_NIP',
        0x78 => 'OP_OVER',
        0x79 => 'OP_PICK',
        0x7a => 'OP_ROLL',
        0x7b => 'OP_ROT',
        0x7c => 'OP_SWAP',
        0x7d => 'OP_TUCK',

        // splice ops
        0x7e => 'OP_CAT',
        0x7f => 'OP_SUBSTR',
        0x80 => 'OP_LEFT',
        0x81 => 'OP_RIGHT',
        0x82 => 'OP_SIZE',

        // bit logic
        0x83 => 'OP_INVERT',
        0x84 => 'OP_AND',
        0x85 => 'OP_OR',
        0x86 => 'OP_XOR',
        0x87 => 'OP_EQUAL',
        0x88 => 'OP_EQUALVERIFY',
        0x89 => 'OP_RESERVED1',
        0x8a => 'OP_RESERVED2',

        // numeric
        0x8b => 'OP_1ADD',
        0x8c => 'OP_1SUB',
        0x8d => 'OP_2MUL',
        0x8e => 'OP_2DIV',
        0x8f => 'OP_NEGATE',
        0x90 => 'OP_ABS',
        0x91 => 'OP_NOT',
        0x92 => 'OP_0NOTEQUAL',

        0x93 => 'OP_ADD',
        0x94 => 'OP_SUB',
        0x95 => 'OP_MUL',
        0x96 => 'OP_DIV',
        0x97 => 'OP_MOD',
        0x98 => 'OP_LSHIFT',
        0x99 => 'OP_RSHIFT',

        0x9a => 'OP_BOOLAND',
        0x9b => 'OP_BOOLOR',
        0x9c => 'OP_NUMEQUAL',
        0x9d => 'OP_NUMEQUALVERIFY',
        0x9e => 'OP_NUMNOTEQUAL',
        0x9f => 'OP_LESSTHAN',
        0xa0 => 'OP_GREATERTHAN',
        0xa1 => 'OP_LESSTHANOREQUAL',
        0xa2 => 'OP_GREATERTHANOREQUAL',
        0xa3 => 'OP_MIN',
        0xa4 => 'OP_MAX',

        0xa5 => 'OP_WITHIN',

        // crypto
        0xa6 => 'OP_RIPEMD160',
        0xa7 => 'OP_SHA1',
        0xa8 => 'OP_SHA256',
        0xa9 => 'OP_HASH160',
        0xaa => 'OP_HASH256',
        0xab => 'OP_CODESEPARATOR',
        0xac => 'OP_CHECKSIG',
        0xad => 'OP_CHECKSIGVERIFY',
        0xae => 'OP_CHECKMULTISIG',
        0xaf => 'OP_CHECKMULTISIGVERIFY',

        // expansion
        0xb0 => 'OP_NOP1',
        0xb1 => 'OP_CHECKLOCKTIMEVERIFY',
        0xb2 => 'OP_CHECKSEQUENCEVERIFY',
        0xb3 => 'OP_NOP4',
        0xb4 => 'OP_NOP5',
        0xb5 => 'OP_NOP6',
        0xb6 => 'OP_NOP7',
        0xb7 => 'OP_NOP8',
        0xb8 => 'OP_NOP9',
        0xb9 => 'OP_NOP10',

        0xff => 'OP_INVALIDOPCODE',
    ];
}