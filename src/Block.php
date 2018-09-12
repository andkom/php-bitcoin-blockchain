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
     * @var Header
     */
    public $header;

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
        $block->header = Header::parse($stream);
        $block->transactionsCount = $stream->readCompactSize();

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
        $this->header->serialize($stream);
        $stream->writeCompactSize(count($this->transactions));

        foreach ($this->transactions as $transaction) {
            $transaction->serialize($stream);
        }

        return $this;
    }
}