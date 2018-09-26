<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Transaction;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;
use AndKom\Bitcoin\Blockchain\Script\ScriptPubKey;

/**
 * Class TransactionOutput
 * @package AndKom\Bitcoin\Blockchain\Transaction
 */
class Output
{
    /**
     * @var int
     */
    public $value;

    /**
     * @var ScriptPubKey
     */
    public $scriptPubKey;

    /**
     * @param Reader $stream
     * @return Output
     */
    static public function parse(Reader $stream): self
    {
        $out = new static;
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