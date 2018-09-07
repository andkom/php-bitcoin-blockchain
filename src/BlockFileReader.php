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
    /**
     * @param string $file
     * @return \Generator
     * @throws Exception
     */
    public function read(string $file): \Generator
    {
        $fp = fopen($file, 'r');

        if (!$fp) {
            throw new Exception("Unable to open file $file for reading.");
        }

        while (true) {
            $magic = fread($fp, 4);

            if (!$magic) {
                break;
            }

            if ($magic != "\xf9\xbe\xb4\xd9") {
                throw new Exception('Invalid magic number.');
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

            $stream = new Reader($data);

            yield Block::parse($stream);
        }

        fclose($fp);
    }
}