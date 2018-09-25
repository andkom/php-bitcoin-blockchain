<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Exception\Exception;

/**
 * Class BlockInfo
 * @package AndKom\Bitcoin\Blockchain
 */
class BlockInfo
{
    const BLOCK_VALID_UNKNOWN = 0;
    const BLOCK_VALID_HEADER = 1;
    const BLOCK_VALID_TREE = 2;
    const BLOCK_VALID_TRANSACTIONS = 3;
    const BLOCK_VALID_CHAIN = 4;
    const BLOCK_VALID_SCRIPTS = 5;
    const BLOCK_VALID_MASK = self::BLOCK_VALID_HEADER | self::BLOCK_VALID_TREE | self::BLOCK_VALID_TRANSACTIONS | self::BLOCK_VALID_CHAIN | self:: BLOCK_VALID_SCRIPTS;

    const BLOCK_HAVE_DATA = 8;
    const BLOCK_HAVE_UNDO = 16;
    const BLOCK_HAVE_MASK = self::BLOCK_HAVE_DATA | self::BLOCK_HAVE_UNDO;

    const BLOCK_FAILED_VALID = 32;
    const BLOCK_FAILED_CHILD = 64;
    const BLOCK_FAILED_MASK = self::BLOCK_FAILED_VALID | self::BLOCK_FAILED_CHILD;

    const BLOCK_OPT_WITNESS = 128;

    /**
     * @var int
     */
    public $version;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int
     */
    public $txCount;

    /**
     * @var int
     */
    public $file;

    /**
     * @var int
     */
    public $dataPos;

    /**
     * @var int
     */
    public $undoPos;

    /**
     * @var Header
     */
    public $header;

    /**
     * @param Reader $reader
     * @return BlockInfo
     */
    static public function parse(Reader $reader): self
    {
        $block = new self;
        $block->version = $reader->readVarInt();
        $block->height = $reader->readVarInt();
        $block->status = $reader->readVarInt();
        $block->txCount = $reader->readVarInt();

        if ($block->status & (static::BLOCK_HAVE_DATA | static::BLOCK_HAVE_UNDO)) {
            $block->file = $reader->readVarInt();
        }

        if ($block->status & static::BLOCK_HAVE_DATA) {
            $block->dataPos = $reader->readVarInt();
        }

        if ($block->status & static::BLOCK_HAVE_UNDO) {
            $block->undoPos = $reader->readVarInt();
        }

        $block->header = Header::parse($reader);

        return $block;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFileName(): string
    {
        if (is_null($this->file)) {
            throw new Exception('Unknown block file number.');
        }

        return sprintf('blk%05d.dat', $this->file);
    }
}