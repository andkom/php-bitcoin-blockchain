<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

/**
 * Class Index
 * @package AndKom\PhpBitcoinBlockchain
 */
class Index
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
     * @throws Exception
     */
    public function getBlockInfoByHash(string $hash): BlockInfo
    {
        if (!isset($this->blockIndex[$hash])) {
            throw new Exception("Unknown block hash.");
        }

        return $this->blockIndex[$hash];
    }

    /**
     * @param int $height
     * @return BlockInfo
     * @throws Exception
     */
    public function getBlockInfoByHeight(int $height): BlockInfo
    {
        if (!isset($this->heightIndex[$height])) {
            throw new Exception("Unknown block height.");
        }

        return $this->getBlockInfoByHash($this->heightIndex[$height]);
    }

    /**
     * @param int $number
     * @return FileInfo
     * @throws Exception
     */
    public function getFileInfo(int $number): FileInfo
    {
        if (!isset($this->fileIndex[$number])) {
            throw new Exception("Unknown file number.");
        }

        return $this->fileIndex[$number];
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getLastFile(): int
    {
        if (is_null($this->lastFile)) {
            throw new Exception('Unknown last file.');
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