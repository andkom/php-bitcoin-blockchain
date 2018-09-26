<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Transaction;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;
use AndKom\Bitcoin\Blockchain\Script\ScriptSig;

/**
 * Class TransactionInput
 * @package AndKom\Bitcoin\Blockchain\Transaction
 */
class Input
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
     * @var ScriptSig
     */
    public $scriptSig;

    /**
     * @var int
     */
    public $sequenceNo;

    /**
     * @var array
     */
    public $witnesses = [];

    /**
     * @param Reader $stream
     * @return Input
     */
    static public function parse(Reader $stream): self
    {
        $in = new static;
        $in->prevTxHash = $stream->read(32);
        $in->prevTxOutIndex = $stream->readInt32();
        $in->scriptSig = new ScriptSig($stream->readString());
        $in->sequenceNo = $stream->readInt32();
        return $in;
    }

    /**
     * @param Writer $stream
     * @return Input
     */
    public function serialize(Writer $stream): self
    {
        $stream->write($this->prevTxHash);
        $stream->writeUInt32($this->prevTxOutIndex);
        $stream->writeString($this->scriptSig->getData());
        $stream->writeInt32($this->sequenceNo);
        return $this;
    }

    /**
     * @return bool
     */
    public function isCoinbase(): bool
    {
        return $this->prevTxHash == str_repeat("\x00", 32);
    }
}