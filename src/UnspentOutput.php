<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Exception\ScriptException;
use AndKom\Bitcoin\Blockchain\Network\Bitcoin;

/**
 * Class UnspentOutput
 * @package AndKom\Bitcoin\Blockchain
 */
class UnspentOutput
{
    const SPECIAL_SCRIPTS = 6;

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
     * @throws ScriptException
     * @throws \Exception
     */
    public function getAddress(Bitcoin $network = null): string
    {
        $addressSerializer = new AddressSerializer($network);

        // try do decompress script first
        switch ($this->type) {
            case 0:
                return $addressSerializer->getPayToPubKeyHash($this->script);

            case 1:
                return $addressSerializer->getPayToScriptHash($this->script);

            case 2:
            case 3:
                $pubKey = chr($this->type) . $this->script;
                return $addressSerializer->getPayToPubKey($pubKey);

            case 4:
            case 5:
                $pubKey = chr($this->type - 2) . $this->script;
                $decompressed = PublicKey::parse($pubKey)->decompress()->serialize();
                return $addressSerializer->getPayToPubKey($decompressed);

            case 22 + static::SPECIAL_SCRIPTS;
                $addressSerializer->getPayToWitnessPubKeyHash($this->script);
                break;

            case 34 + static::SPECIAL_SCRIPTS:
                $addressSerializer->getPayToWitnessScriptHash($this->script);
                break;

            // invalid public key prefix
            case 35 + static::SPECIAL_SCRIPTS:
            case 67 + static::SPECIAL_SCRIPTS:
                throw new ScriptException('Unable to decode output address.');
        }

        // fallback
        return (new ScriptPubKey($this->script))->getOutputAddress($network);
    }
}