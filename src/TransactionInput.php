<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class TransactionInput
 * @package AndKom\PhpBitcoinBlockchain
 */
class TransactionInput
{
    /**
     * @var string
     */
    public $prevTxHash;

    /**
     * @var int
     */
    public $prevTxOutIndex;

    /**
     * @var Script
     */
    public $script;

    /**
     * @var int
     */
    public $sequenceNo;

    /**
     * @param Reader $stream
     * @return TransactionInput
     */
    static public function parse(Reader $stream): self
    {
        $in = new self;
        $in->prevTxHash = bin2hex(strrev($stream->read(32)));
        $in->prevTxOutIndex = $stream->readInt32();
        $in->script = new Script($stream->readString());
        $in->sequenceNo = $stream->readInt32();
        return $in;
    }

    /**
     * @param Writer $stream
     * @return TransactionInput
     */
    public function serialize(Writer $stream): self
    {
        $stream->write(strrev(hex2bin($this->prevTxHash)));
        $stream->writeUInt32($this->prevTxOutIndex);
        $stream->writeString($this->script->getData());
        $stream->writeInt32($this->sequenceNo);
        return $this;
    }
}