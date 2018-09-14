<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testSegwit()
    {
        $hex = '01000000000101a4be5f1271ba9742925a13ad6dbde1edba930691235ce0c1ea5f19d17c50795c0100000000ffffffff024c69c60700000000160014536523b38a207338740797cd03c3312e81408d5319d916000000000017a9140bd991e785337804568db45edb4ef00aa89377a9870247304402204dfb80ded7cdd60ae9b702d428c8f27c4c4dafda65452c7efd48d0d5b1f1766d022026423d627f851b94e04b53dc2df93666d924067f87ca4c785ec0c3ef9491fd72012103eee8e1747dcdb2dceba826a1dc9ea701f438ff9aa1ee466df6372a05cb246d3000000000';

        $reader = new Reader(hex2bin($hex));
        $transaction = Transaction::parse($reader);

        $this->assertEquals($transaction->isSegwit, true);
        $this->assertEquals($transaction->getHash(), hex2bin('5ce9122211e921dc0d5bd2b6fd9b5c59f70a398eea42d6a1e0ee104b394fb62d'));

        $writer = new Writer();
        $transaction->serialize($writer);

        $this->assertEquals($writer->getBuffer(), hex2bin($hex));
    }
}