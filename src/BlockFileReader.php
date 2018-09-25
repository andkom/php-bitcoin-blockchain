<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Exception\IOException;

/**
 * Class BlockFileReader
 * @package AndKom\Bitcoin\Blockchain
 */
class BlockFileReader
{
    const MAGIC = "\xf9\xbe\xb4\xd9";

    /**
     * @param $fp
     * @param int|null $pos
     * @return Block
     * @throws IOException
     */
    public function readBlockFromFile($fp, int $pos = null): Block
    {
        if (!is_resource($fp)) {
            throw new IOException('Invalid file resource.');
        }

        if ($pos && fseek($fp, $pos - 4) === false) {
            throw new IOException('Unable to seek block file.');
        }

        $size = fread($fp, 4);

        if ($size === false) {
            throw new IOException('Unable to read block size.');
        }

        $length = unpack('V', $size)[1];

        $data = fread($fp, $length);

        if ($data === false) {
            throw new IOException('Unable to read block data.');
        }

        return Block::parse(new Reader($data));
    }

    /**
     * @param string $file
     * @param int $pos
     * @return Block
     * @throws IOException
     */
    public function readBlock(string $file, int $pos): Block
    {
        $fp = fopen($file, 'r');

        if (!$fp) {
            throw new IOException("Unable to open block file '$file'.");
        }

        $block = $this->readBlockFromFile($fp, $pos);

        fclose($fp);

        return $block;
    }

    /**
     * @param string $file
     * @return \Generator
     * @throws IOException
     */
    public function read(string $file): \Generator
    {
        $fp = fopen($file, 'r');

        if (!$fp) {
            throw new IOException("Unable to open block file '$file'.");
        }

        while (fread($fp, 4) == static::MAGIC) {
            yield $this->readBlockFromFile($fp);
        }

        fclose($fp);
    }
}