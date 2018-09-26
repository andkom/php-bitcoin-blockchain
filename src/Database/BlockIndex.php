<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Database;

use AndKom\Bitcoin\Blockchain\Exception\DatabaseException;

/**
 * Class BlockIndex
 * @package AndKom\Bitcoin\Blockchain\Database
 */
class BlockIndex
{
    /**
     * @var array
     */
    public $blockIndex = [];

    /**
     * @var array
     */
    public $heightIndex = [];

    /**
     * @var array
     */
    public $fileIndex = [];

    /**
     * @var int
     */
    public $lastFile;

    /**
     * @param string $hash
     * @return BlockInfo
     * @throws DatabaseException
     */
    public function getBlockInfoByHash(string $hash): BlockInfo
    {
        if (!isset($this->blockIndex[$hash])) {
            throw new DatabaseException('Unknown block hash.');
        }

        return $this->blockIndex[$hash];
    }

    /**
     * @param int $height
     * @return BlockInfo
     * @throws DatabaseException
     */
    public function getBlockInfoByHeight(int $height): BlockInfo
    {
        if (!isset($this->heightIndex[$height])) {
            throw new DatabaseException('Unknown block height.');
        }

        return $this->getBlockInfoByHash($this->heightIndex[$height]);
    }

    /**
     * @param int $number
     * @return FileInfo
     * @throws DatabaseException
     */
    public function getFileInfo(int $number): FileInfo
    {
        if (!isset($this->fileIndex[$number])) {
            throw new DatabaseException('Unknown file number.');
        }

        return $this->fileIndex[$number];
    }

    /**
     * @return int
     * @throws DatabaseException
     */
    public function getLastFile(): int
    {
        if (is_null($this->lastFile)) {
            throw new DatabaseException('Unknown last file.');
        }

        return $this->lastFile;
    }

    /**
     * @return int
     */
    public function getMinHeight(): int
    {
        return min(array_keys($this->heightIndex));
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return max(array_keys($this->heightIndex));
    }
}