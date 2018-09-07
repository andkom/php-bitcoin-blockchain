<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class Transaction
 * @package AndKom\PhpBitcoinBlockchain
 */
class Transaction
{
    /**
     * @var int
     */
    public $version;

    /**
     * @var int
     */
    public $flag;

    /**
     * @var int
     */
    public $inCount = 0;

    /**
     * @var array
     */
    public $inputs = [];

    /**
     * @var int
     */
    public $outCount = 0;

    /**
     * @var array
     */
    public $outputs = [];

    /**
     * @var array
     */
    public $witnesses = [];

    /**
     * @var int
     */
    public $lockTime;

    /**
     * @param Reader $stream
     * @return Transaction
     */
    static public function parse(Reader $stream): self
    {
        $tx = new self;
        $tx->version = $stream->readUInt32();
        $tx->inCount = $stream->readVarInt();

        for ($i = 0; $i < $tx->inCount; $i++) {
            $tx->inputs[] = TransactionInput::parse($stream);
        }

        $tx->outCount = $stream->readVarInt();

        for ($i = 0; $i < $tx->outCount; $i++) {
            $tx->outputs[] = TransactionOutput::parse($stream);
        }

        $tx->lockTime = $stream->readInt32();

        return $tx;
    }

    /**
     * @param Writer $stream
     * @return Transaction
     */
    public function serialize(Writer $stream): self
    {
        $stream->writeUInt32($this->version);
        $stream->writeVarInt($this->inCount);

        foreach ($this->inputs as $in) {
            $in->serialize($stream);
        }

        $stream->writeVarInt($this->outCount);

        foreach ($this->outputs as $out) {
            $out->serialize($stream);
        }

        $stream->writeInt32($this->lockTime);
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