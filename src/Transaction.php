<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;
use AndKom\BCDataStream\Writer;

/**
 * Class Transaction
 * @package AndKom\PhpBitcoinBlockchain
 */
class Transaction
{
    /**
     * @var int
     */
    public $version;

    /**
     * @var int
     */
    public $isSegwit = false;

    /**
     * @var int
     */
    public $inCount = 0;

    /**
     * @var array
     */
    public $inputs = [];

    /**
     * @var int
     */
    public $outCount = 0;

    /**
     * @var array
     */
    public $outputs = [];

    /**
     * @var int
     */
    public $lockTime;

    /**
     * @param Reader $stream
     * @return Transaction
     */
    static public function parse(Reader $stream): self
    {
        $tx = new self;
        $tx->version = $stream->readUInt32();

        $tx->isSegwit = $stream->read(2) == "\x00\x01";

        if (!$tx->isSegwit) {
            $stream->setPosition($stream->getPosition() - 2);
        }

        $tx->inCount = $stream->readVarInt();

        for ($i = 0; $i < $tx->inCount; $i++) {
            $tx->inputs[] = Input::parse($stream);
        }

        $tx->outCount = $stream->readVarInt();

        for ($i = 0; $i < $tx->outCount; $i++) {
            $tx->outputs[] = Output::parse($stream);
        }

        if ($tx->isSegwit) {
            foreach ($tx->inputs as $input) {
                $count = $stream->readVarInt();
                for ($i = 0; $i < $count; $i++) {
                    $input->witnesses[] = $stream->readString();
                }
            }
        }

        $tx->lockTime = $stream->readInt32();

        return $tx;
    }

    /**
     * @param Writer $stream
     * @param bool $segWit
     * @return Transaction
     */
    public function serialize(Writer $stream, bool $segWit = true): self
    {
        $stream->writeUInt32($this->version);

        if ($this->isSegwit && $segWit) {
            $stream->write("\x00\x01");
        }

        $stream->writeVarInt($this->inCount);

        foreach ($this->inputs as $in) {
            $in->serialize($stream);
        }

        $stream->writeVarInt($this->outCount);

        foreach ($this->outputs as $out) {
            $out->serialize($stream);
        }

        if ($this->isSegwit && $segWit) {
            foreach ($this->inputs as $input) {
                $stream->writeVarInt(count($input->witnesses));

                foreach ($input->witnesses as $witness) {
                    $stream->writeString($witness);
                }
            }
        }

        $stream->writeInt32($this->lockTime);

        return $this;
    }

    /**
     * @param bool $segWit
     * @return string
     */
    public function getHash(bool $segWit = false): string
    {
        $stream = new Writer();
        $this->serialize($stream, $segWit);
        $hash = Utils::hash($stream->getBuffer(), true);
        $hash = strrev($hash);
        $hash = bin2hex($hash);
        return $hash;
    }
}