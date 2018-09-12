<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;

/**
 * Class BlockFileReader
 * @package AndKom\PhpBitcoinBlockchain
 */
class BlockFileReader
{
    const MAGIC = "\xf9\xbe\xb4\xd9";

    /**
     * @param string $file
     * @return \Generator
     * @throws Exception
     */
    public function read(string $file): \Generator
    {
        $fp = fopen($file, 'r');

        if (!$fp) {
            throw new Exception("Unable to open block file '$file' for reading.");
        }

        while (fread($fp, 4) == static::MAGIC) {
            yield $this->readBlock($fp);
        }

        fclose($fp);
    }

    /**
     * @param resource $fp
     * @return Block
     * @throws Exception
     */
    public function readBlock($fp): Block
    {
        if (!is_resource($fp)) {
            throw new Exception('Invalid file resource.');
        }

        $size = fread($fp, 4);

        if ($size === false) {
            throw new Exception('Unable to read block size.');
        }

        $length = unpack('V', $size)[1];

        $data = fread($fp, $length);

        if ($data === false) {
            throw new Exception('Unable to read block data.');
        }

        return Block::parse(new Reader($data));
    }
}