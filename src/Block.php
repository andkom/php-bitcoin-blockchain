<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class Block
 * @package AndKom\PhpBitcoinBlockchain
 */
class Block
{
    /**
     * @var int
     */
    public $version;

    /**
     * @var string
     */
    public $prevBlockHash;

    /**
     * @var string
     */
    public $merkleRootHash;

    /**
     * @var int
     */
    public $time;

    /**
     * @var int
     */
    public $bits;

    /**
     * @var int
     */
    public $nonce;

    /**
     * @var int
     */
    public $transactionsCount = 0;

    /**
     * @var array
     */
    public $transactions = [];

    /**
     * @param Reader $stream
     * @return Block
     */
    static public function parse(Reader $stream): self
    {
        $block = new self;
        $block->version = $stream->readUInt32();
        $block->prevBlockHash = bin2hex(strrev($stream->read(32)));
        $block->merkleRootHash = bin2hex(strrev($stream->read(32)));
        $block->time = $stream->readUInt32();
        $block->bits = $stream->readUInt32();
        $block->nonce = $stream->readUInt32();
        $block->transactionsCount = $stream->readVarInt();

        for ($i = 0; $i < $block->transactionsCount; $i++) {
            $block->transactions[] = Transaction::parse($stream);
        }

        return $block;
    }

    /**
     * @param Writer $stream
     * @return Block
     */
    public function serialize(Writer $stream): self
    {
        $stream->writeUInt32($this->version);
        $stream->write(strrev(hex2bin($this->prevBlockHash)));
        $stream->write(strrev(hex2bin($this->merkleRootHash)));
        $stream->writeUInt32($this->time);
        $stream->writeUInt32($this->bits);
        $stream->writeUInt32($this->nonce);
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        $stream = new Writer();
        $this->serialize($stream);
        $hash = hash('sha256', $stream->getBuffer(), true);
        $hash = hash('sha256', $hash, true);
        $hash = strrev($hash);
        $hash = bin2hex($hash);
        return $hash;
    }
}