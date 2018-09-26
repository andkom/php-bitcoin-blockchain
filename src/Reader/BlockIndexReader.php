<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Reader;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Database\BlockIndex;
use AndKom\Bitcoin\Blockchain\Database\BlockInfo;
use AndKom\Bitcoin\Blockchain\Database\FileInfo;
use AndKom\Bitcoin\Blockchain\Exception\DatabaseException;

/**
 * Class BlockIndexReader
 * @package AndKom\Bitcoin\Blockchain\Reader
 */
class BlockIndexReader
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
     * @return BlockIndex
     * @throws DatabaseException
     * @throws \LevelDBException
     */
    public function read(): BlockIndex
    {
        if (!class_exists('\LevelDB')) {
            throw new DatabaseException('Extension leveldb is not installed.');
        }

        $index = new BlockIndex();

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