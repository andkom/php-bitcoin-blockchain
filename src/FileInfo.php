<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;

/**
 * Class FileInfo
 * @package AndKom\Bitcoin\Blockchain
 */
class FileInfo
{
    /**
     * @var int
     */
    public $blocks;

    /**
     * @var int
     */
    public $fileSize;

    /**
     * @var int
     */
    public $undoSize;

    /**
     * @var int
     */
    public $heightFirst;

    /**
     * @var int
     */
    public $heightLast;

    /**
     * @var int
     */
    public $timeFirst;

    /**
     * @var int
     */
    public $timeLast;

    /**
     * @param Reader $reader
     * @return FileInfo
     */
    static public function parse(Reader $reader): self
    {
        $file = new self;
        $file->blocks = $reader->readVarInt();
        $file->fileSize = $reader->readVarInt();
        $file->undoSize = $reader->readVarInt();
        $file->heightFirst = $reader->readVarInt();
        $file->heightLast = $reader->readVarInt();
        $file->timeFirst = $reader->readVarInt();
        $file->timeLast = $reader->readVarInt();

        return $file;
    }
}