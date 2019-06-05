<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Database;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use AndKom\Bitcoin\Blockchain\Exceptions\AddressSerializeException;
use AndKom\Bitcoin\Blockchain\Exceptions\OutputDecodeException;
use AndKom\Bitcoin\Blockchain\Network\NetworkInterface;
use AndKom\Bitcoin\Blockchain\Script\Opcodes;
use AndKom\Bitcoin\Blockchain\Script\ScriptPubKey;
use AndKom\Bitcoin\Blockchain\Serializer\AddressSerializer;

/**
 * Class UnspentOutput
 * @package AndKom\Bitcoin\Blockchain\Database
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
        $unspentOutput = new static;
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
     * @param string $compressed
     * @return string
     * @throws OutputDecodeException
     */
    static public function decompressPubKey(string $compressed): string
    {
        try {
            $decompressed = PublicKey::parse($compressed)->decompress()->serialize();
        } catch (\Exception $exception) {
            throw new OutputDecodeException('Unable to decompress public key.', 0, $exception);
        }

        return $decompressed;
    }

    /**
     * @return ScriptPubKey
     * @throws OutputDecodeException
     */
    public function decompressScript(): ScriptPubKey
    {
        switch ($this->type) {
            case 0:
                $script = chr(Opcodes::OP_DUP);
                $script .= chr(Opcodes::OP_HASH160);
                $script .= chr(20);
                $script .= $this->script;
                $script .= chr(Opcodes::OP_EQUALVERIFY);
                $script .= chr(Opcodes::OP_CHECKSIG);
                break;

            case 1:
                $script = chr(Opcodes::OP_HASH160);
                $script .= chr(20);
                $script .= $this->script;
                $script .= chr(Opcodes::OP_EQUAL);
                break;

            case 2:
            case 3:
                $script = chr(33);
                $script .= chr($this->type);
                $script .= $this->script;
                $script .= chr(Opcodes::OP_CHECKSIG);
                break;

            case 4:
            case 5:
                $script = chr(65);
                $script .= static::decompressPubKey(chr($this->type - 2) . $this->script);
                $script .= chr(Opcodes::OP_CHECKSIG);
                break;

            default:
                $script = $this->script;
        }

        return new ScriptPubKey($script);
    }

    /**
     * @param NetworkInterface|null $network
     * @return string
     * @throws OutputDecodeException
     * @throws AddressSerializeException
     */
    public function getAddress(NetworkInterface $network = null): string
    {
        $addressSerializer = new AddressSerializer($network);

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
                $pubKey = static::decompressPubKey(chr($this->type - 2) . $this->script);
                return $addressSerializer->getPayToPubKey($pubKey);

            case 28:
                return $addressSerializer->getPayToWitnessPubKeyHash(substr($this->script, 2, 20));

            case 40:
                return $addressSerializer->getPayToWitnessScriptHash(substr($this->script, 2, 32));
        }

        throw new OutputDecodeException('Unable to decode output (unknown script type).');
    }
}