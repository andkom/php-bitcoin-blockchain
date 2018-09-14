<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Network\Bitcoin;

/**
 * Class UnspentOutput
 * @package AndKom\Bitcoin\Blockchain
 */
class UnspentOutput
{
    /**
     * @var string
     */
    public $hash;

    /**
     * @var int
     */
    public $index;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $coinbase;

    /**
     * @var int
     */
    public $value;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $script;

    /**
     * @param Reader $keyReader
     * @param Reader $valueReader
     * @return UnspentOutput
     */
    static public function parse(Reader $keyReader, Reader $valueReader): self
    {
        $unspentOutput = new self;
        $unspentOutput->hash = $keyReader->read(32);
        $unspentOutput->index = $keyReader->readVarInt();

        $code = $valueReader->readVarInt();

        $unspentOutput->height = $code >> 1;
        $unspentOutput->coinbase = $code & 1;
        $unspentOutput->value = static::decompressAmount($valueReader->readVarInt());
        $unspentOutput->type = $valueReader->readVarInt();

        $script = $valueReader->read($valueReader->getSize() - $valueReader->getPosition());

        $unspentOutput->script = $script;

        return $unspentOutput;
    }

    /**
     * @param int $x
     * @return int
     */
    static public function decompressAmount(int $x)
    {
        if ($x == 0) {
            return 0;
        }
        $x--;
        $e = $x % 10;
        $x = floor($x / 10);
        if ($e < 9) {
            $d = $x % 9 + 1;
            $x = floor($x / 9);
            $n = $x * 10 + $d;
        } else {
            $n = $x + 1;
        }
        while ($e) {
            $n *= 10;
            $e--;
        }
        return $n;
    }

    /**
     * @param Bitcoin|null $network
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    public function getAddress(Bitcoin $network = null): string
    {
        $addressSerializer = new AddressSerializer($network);

        // try do decompress script first
        switch ($this->type) {
            case 0x00:
                return $addressSerializer->getPayToPubKeyHashAddress($this->script);

            case 0x01:
                return $addressSerializer->getPayToScriptHash($this->script);

            case 0x02:
            case 0x03:
            case 0x04:
            case 0x05:
                return $addressSerializer->getPayToPubKeyAddress($this->script);
        }

        // fallback
        return (new ScriptPubKey($this->script))->getOutputAddress($network);
    }
}