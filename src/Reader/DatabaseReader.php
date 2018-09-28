<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Reader;

use AndKom\Bitcoin\Blockchain\Block\Block;
use AndKom\Bitcoin\Blockchain\Database\BlockIndex;
use AndKom\Bitcoin\Blockchain\Database\BlockInfo;
use AndKom\Bitcoin\Blockchain\Exceptions\DatabaseException;

/**
 * Class DatabaseReader
 * @package AndKom\Bitcoin\Blockchain\Reader
 */
class DatabaseReader
{
    /**
     * @var
     */
    protected $dataDir;

    /**
     * @var
     */
    protected $blocksDir;

    /**
     * @var string
     */
    protected $blockIndexDir;

    /**
     * @var string
     */
    protected $chainstateDir;

    /**
     * @var BlockIndex
     */
    protected $index;

    /**
     * Blockchain constructor.
     * @param string $dataDir
     */
    public function __construct(string $dataDir)
    {
        $this->dataDir = $dataDir;
        $this->blocksDir = implode(DIRECTORY_SEPARATOR, [$dataDir, 'blocks']);
        $this->blockIndexDir = implode(DIRECTORY_SEPARATOR, [$dataDir, 'blocks', 'index']);
        $this->chainstateDir = implode(DIRECTORY_SEPARATOR, [$dataDir, 'chainstate']);
    }

    /**
     * @return BlockIndex
     * @throws DatabaseException
     * @throws \LevelDBException
     */
    public function getIndex(): BlockIndex
    {
        if ($this->index) {
            return $this->index;
        }

        $reader = new BlockIndexReader($this->blockIndexDir);

        return $this->index = $reader->read();
    }

    /**
     * @return ChainstateReader
     */
    public function getChainstate(): ChainstateReader
    {
        return new ChainstateReader($this->chainstateDir);
    }

    /**
     * @param BlockInfo $blockInfo
     * @return Block
     * @throws DatabaseException
     */
    public function getBlockByInfo(BlockInfo $blockInfo): Block
    {
        $blockFile = $this->blocksDir . DIRECTORY_SEPARATOR . $blockInfo->getFileName();

        return (new BlockFileReader())->readBlock($blockFile, $blockInfo->dataPos);
    }

    /**
     * @param string $hash
     * @return Block
     * @throws DatabaseException
     * @throws \LevelDBException
     */
    public function getBlockByHash(string $hash): Block
    {
        return $this->getBlockByInfo($this->getIndex()->getBlockInfoByHash($hash));
    }

    /**
     * @param int $height
     * @return Block
     * @throws DatabaseException
     * @throws \LevelDBException
     */
    public function getBlockByHeight(int $height): Block
    {
        return $this->getBlockByInfo($this->getIndex()->getBlockInfoByHeight($height));
    }

    /**
     * @param int|null $minHeight
     * @param int|null $maxHeight
     * @return \Generator
     * @throws DatabaseException
     */
    public function readBlocks(int $minHeight = null, int $maxHeight = null): \Generator
    {
        $index = $this->getIndex();

        $minHeight = $minHeight ?? $index->getMinHeight();
        $maxHeight = $maxHeight ?? $index->getMaxHeight();

        for ($i = $minHeight; $i <= $maxHeight; $i++) {
            $blockInfo = $index->getBlockInfoByHeight($i);

            if (!($blockInfo->status & BlockInfo::BLOCK_HAVE_DATA)) {
                continue;
            }

            yield $i => $this->getBlockByInfo($blockInfo);
        }
    }

    /**
     * @return \Generator
     * @throws DatabaseException
     */
    public function readBlocksUnordered(): \Generator
    {
        if (!file_exists($this->blocksDir)) {
            throw new DatabaseException("Blocks dir '{$this->blocksDir}' not found.");
        }

        $dir = dir($this->blocksDir);
        $reader = new BlockFileReader();

        while (false !== ($entry = $dir->read())) {
            if (strpos($entry, 'blk') !== 0) {
                continue;
            }

            $file = $this->blocksDir . DIRECTORY_SEPARATOR . $entry;

            yield from $reader->read($file);
        }

        $dir->close();
    }
}