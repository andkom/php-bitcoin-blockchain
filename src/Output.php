<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class TransactionOutput
 * @package AndKom\PhpBitcoinBlockchain
 */
class Output
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
     * @return Output
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
     * @return Output
     */
    public function serialize(Writer $stream): self
    {
        $stream->writeUInt64($this->value);
        $stream->writeString($this->scriptPubKey->getData());
        return $this;
    }
}