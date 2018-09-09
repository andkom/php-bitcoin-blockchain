<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class TransactionOutput
 * @package AndKom\PhpBitcoinBlockchain
 */
class TransactionOutput
{
    /**
     * @var int
     */
    public $value;

    /**
     * @var Script
     */
    public $scriptPubKey;

    /**
     * @param Reader $stream
     * @return TransactionOutput
     */
    static public function parse(Reader $stream): self
    {
        $out = new self;
        $out->value = $stream->readUInt64();
        $out->scriptPubKey = new ScriptPubKey($stream->readString());
        return $out;
    }

    /**
     * @param Writer $stream
     * @return TransactionOutput
     */
    public function serialize(Writer $stream): self
    {
        $stream->writeUInt64($this->value);
        $stream->writeString($this->scriptPubKey->getData());
        return $this;
    }
}