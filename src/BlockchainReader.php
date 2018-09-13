<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

/**
 * Class BlockchainReader
 * @package AndKom\PhpBitcoinBlockchain
 */
class BlockchainReader
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
    protected $chainStateDir;

    /**
     * @var Index
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
        $this->chainStateDir = implode(DIRECTORY_SEPARATOR, [$dataDir, 'chainstate']);
    }

    /**
     * @return Index
     * @throws Exception
     */
    public function getIndex(): Index
    {
        if ($this->index) {
            return $this->index;
        }

        $reader = new IndexReader($this->blockIndexDir);

        return $this->index = $reader->read();
    }

    /**
     * @return ChainStateReader
     */
    public function getChainState(): ChainStateReader
    {
        return new ChainStateReader($this->chainStateDir);
    }

    /**
     * @param int|null $minHeight
     * @param int|null $maxHeight
     * @return \Generator
     * @throws Exception
     */
    public function readBlocks(int $minHeight = null, int $maxHeight = null): \Generator
    {
        $index = $this->getIndex();
        $minHeight = $minHeight ?? $index->getMinHeight();
        $maxHeight = $maxHeight ?? $index->getMaxHeight();
        $reader = new BlockFileReader();

        $handles = [];

        for ($i = $minHeight; $i <= $maxHeight; $i++) {
            $blockInfo = $index->getBlockInfoByHeight($i);

            if (!($blockInfo->status & BlockInfo::BLOCK_HAVE_DATA)) {
                continue;
            }

            $file = $blockInfo->getFileName();

            if (isset($handles[$file])) {
                $fp = $handles[$file];
            } else {
                $path = $this->blocksDir . DIRECTORY_SEPARATOR . $file;

                if (!is_readable($path)) {
                    throw new Exception("Block file '$file' not found.");
                }

                $fp = $handles[$file] = fopen($path, 'r');
            }

            fseek($fp, $blockInfo->dataPos - 4);

            yield $i => $reader->readBlock($fp);
        }

        foreach ($handles as $file => $fp) {
            fclose($fp);
        }
    }

    /**
     * @return \Generator
     * @throws Exception
     */
    public function readBlocksUnordered(): \Generator
    {
        if (!file_exists($this->blocksDir)) {
            throw new Exception("Blocks dir '{$this->blocksDir}' not found.");
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