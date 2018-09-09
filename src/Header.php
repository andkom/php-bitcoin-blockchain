<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class Header
 * @package AndKom\PhpBitcoinBlockchain
 */
class Header
{
    /**
     * @var int
     */
    public $version;

    /**
     * @var string
     */
    public $prevBlockHash;

    /**
     * @var string
     */
    public $merkleRootHash;

    /**
     * @var int
     */
    public $time;

    /**
     * @var int
     */
    public $bits;

    /**
     * @var int
     */
    public $nonce;

    /**
     * @param Reader $stream
     * @return Header
     */
    static public function parse(Reader $stream): self
    {
        $header = new self;
        $header->version = $stream->readUInt32();
        $header->prevBlockHash = bin2hex(strrev($stream->read(32)));
        $header->merkleRootHash = bin2hex(strrev($stream->read(32)));
        $header->time = $stream->readUInt32();
        $header->bits = $stream->readUInt32();
        $header->nonce = $stream->readUInt32();
        return $header;
    }

    /**
     * @param Writer $stream
     * @return Header
     */
    public function serialize(Writer $stream): self
    {
        $stream->writeUInt32($this->version);
        $stream->write(strrev(hex2bin($this->prevBlockHash)));
        $stream->write(strrev(hex2bin($this->merkleRootHash)));
        $stream->writeUInt32($this->time);
        $stream->writeUInt32($this->bits);
        $stream->writeUInt32($this->nonce);
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        $stream = new Writer();
        $this->serialize($stream);
        $hash = Utils::hash($stream->getBuffer(), true);
        $hash = strrev($hash);
        $hash = bin2hex($hash);
        return $hash;
    }
}