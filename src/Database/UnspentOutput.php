<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Database;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use AndKom\Bitcoin\Blockchain\Exception\ScriptException;
use AndKom\Bitcoin\Blockchain\Script\Opcodes;
use AndKom\Bitcoin\Blockchain\Script\ScriptPubKey;

/**
 * Class UnspentOutput
 * @package AndKom\Bitcoin\Blockchain\Database
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
     * @return ScriptPubKey
     * @throws ScriptException
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
                $compressed = chr($this->type - 2) . $this->script;
                try {
                    $decompressed = PublicKey::parse($compressed)->decompress()->serialize();
                } catch (\Exception $exception) {
                    throw new ScriptException('Unable to decompress public key.', 0, $exception);
                }
                $script = chr(65);
                $script .= $decompressed;
                $script .= chr(Opcodes::OP_CHECKSIG);
                break;

            default:
                $script = $this->script;
        }

        return new ScriptPubKey($script);
    }
}