<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain\Tests;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;
use AndKom\PhpBitcoinBlockchain\Block;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public function testParse()
    {
        $genesis = "01000000"; // version
        $genesis .= "0000000000000000000000000000000000000000000000000000000000000000"; // prev block
        $genesis .= "3BA3EDFD7A7B12B27AC72C3E67768F617FC81BC3888A51323A9FB8AA4B1E5E4A"; // merkle root
        $genesis .= "29AB5F49"; // timestamp
        $genesis .= "FFFF001D"; // bits
        $genesis .= "1DAC2B7C"; // nonce
        $genesis .= "01"; // tx count
        $genesis .= "01000000"; // version
        $genesis .= "01"; // input count
        $genesis .= "0000000000000000000000000000000000000000000000000000000000000000FFFFFFFF"; // prev output
        $genesis .= "4D"; // scriptSig length
        $genesis .= "04FFFF001D0104455468652054696D65732030332F4A616E2F32303039204368616E63656C6C6F72206F6E206272696E6B206F66207365636F6E64206261696C6F757420666F722062616E6B73"; // scriptSig
        $genesis .= "FFFFFFFF"; // sequence
        $genesis .= "01"; // outputs
        $genesis .= "00F2052A01000000"; // 50 BTC
        $genesis .= "43"; // scriptPubKey length
        $genesis .= "4104678AFDB0FE5548271967F1A67130B7105CD6A828E03909A67962E0EA1F61DEB649F6BC3F4CEF38C4F35504E51EC112DE5C384DF7BA0B8D578A4C702B6BF11D5FAC"; // scriptPubKey
        $genesis .= "00000000"; // lock time

        $block = Block::parse(new Reader(hex2bin($genesis)));
        $this->assertEquals($block->header->getHash(), hex2bin('6fe28c0ab6f1b372c1a6a246ae63f74f931e8365e15a089c68d6190000000000'));
        $this->assertEquals($block->header->version, 1);
        $this->assertEquals($block->header->prevBlockHash, hex2bin('0000000000000000000000000000000000000000000000000000000000000000'));
        $this->assertEquals($block->header->merkleRootHash, hex2bin('3ba3edfd7a7b12b27ac72c3e67768f617fc81bc3888a51323a9fb8aa4b1e5e4a'));
        $this->assertEquals($block->header->time, 1231006505);
        $this->assertEquals($block->header->bits, 486604799);
        $this->assertEquals($block->header->nonce, 2083236893);
        $this->assertEquals($block->transactions[0]->getHash(), hex2bin('3ba3edfd7a7b12b27ac72c3e67768f617fc81bc3888a51323a9fb8aa4b1e5e4a'));
        $this->assertEquals($block->transactions[0]->version, 1);
        $this->assertEquals($block->transactions[0]->inCount, 1);
        $this->assertEquals($block->transactions[0]->inputs[0]->isCoinbase(), true);
        $this->assertEquals($block->transactions[0]->inputs[0]->prevTxHash, hex2bin('0000000000000000000000000000000000000000000000000000000000000000'));
        $this->assertEquals($block->transactions[0]->inputs[0]->prevTxOutIndex, -1);
        $this->assertEquals($block->transactions[0]->inputs[0]->sequenceNo, -1);
        $this->assertEquals($block->transactions[0]->inputs[0]->scriptSig->getData(), hex2bin('04ffff001d0104455468652054696d65732030332f4a616e2f32303039204368616e63656c6c6f72206f6e206272696e6b206f66207365636f6e64206261696c6f757420666f722062616e6b73'));
        $this->assertEquals($block->transactions[0]->outputs[0]->value, 5000000000);
        $this->assertEquals($block->transactions[0]->outputs[0]->scriptPubKey->getData(), hex2bin('4104678afdb0fe5548271967f1a67130b7105cd6a828e03909a67962e0ea1f61deb649f6bc3f4cef38c4f35504e51ec112de5c384df7ba0b8d578a4c702b6bf11d5fac'));
        $this->assertEquals($block->transactions[0]->lockTime, 0);

        $stream = new Writer();
        $block->serialize($stream);

        $this->assertEquals($stream->getBuffer(), hex2bin($genesis));
    }
}