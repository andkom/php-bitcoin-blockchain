<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;

/**
 * Class IndexReader
 * @package AndKom\Bitcoin\Blockchain
 */
class IndexReader
{
    const PREFIX_BLOCK = 'b';
    const PREFIX_FILE = 'f';
    const PREFIX_LAST = 'l';

    /**
     * @var string
     */
    protected $blockIndexDir;

    /**
     * BlockIndexReader constructor.
     * @param string $blockIndexDir
     */
    public function __construct(string $blockIndexDir = '')
    {
        $this->blockIndexDir = $blockIndexDir;
    }

    /**
     * @return Index
     * @throws Exception
     * @throws \LevelDBException
     */
    public function read(): Index
    {
        if (!class_exists('\LevelDB')) {
            throw new Exception('Extension leveldb is not installed.');
        }

        $index = new Index();

        $db = new \LevelDB($this->blockIndexDir);

        foreach ($db->getIterator() as $key => $value) {
            $key = new Reader($key);
            $prefix = $key->read(1);

            if ($prefix == static::PREFIX_BLOCK) {
                $hash = $key->read(32);
                $block = BlockInfo::parse(new Reader($value));
                $index->blockIndex[$hash] = $block;
                $index->heightIndex[$block->height] = $hash;
            }

            if ($prefix == static::PREFIX_FILE) {
                $number = $key->readUInt32();
                $index->fileIndex[$number] = FileInfo::parse(new Reader($value));
            }

            if ($prefix == static::PREFIX_LAST) {
                $index->lastFile = (new Reader($value))->readUInt32();
            }
        }

        $db->close();

        return $index;
    }
}