<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\Bitcoin\Blockchain\Exception\ScriptException;
use AndKom\Bitcoin\Blockchain\Network\Bitcoin;

/**
 * Class ScriptPubKey
 * @package AndKom\Bitcoin\Blockchain
 */
class ScriptPubKey extends Script
{
    /**
     * @return bool
     */
    public function isReturn(): bool
    {
        return $this->size >= 1 && ord($this->data[0]) == Opcodes::OP_RETURN;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size == 0;
    }

    /**
     * @return bool
     */
    public function isPayToPubKey(): bool
    {
        // compressed pubkey
        if ($this->size == PublicKey::LENGTH_COMPRESSED + 2 &&
            ord($this->data[0]) == PublicKey::LENGTH_COMPRESSED &&
            ($this->data[1] == PublicKey::PREFIX_COMPRESSED_EVEN || $this->data[1] == PublicKey::PREFIX_COMPRESSED_ODD) &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG) {
            return true;
        }

        // uncompressed pubkey
        if ($this->size == PublicKey::LENGTH_UNCOMPRESSED + 2 &&
            ord($this->data[0]) == PublicKey::LENGTH_UNCOMPRESSED &&
            $this->data[1] == PublicKey::PREFIX_UNCOMPRESSED &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPayToPubKeyHash(): bool
    {
        return $this->size == 25 &&
            ord($this->data[0]) == Opcodes::OP_DUP &&
            ord($this->data[1]) == Opcodes::OP_HASH160 &&
            ord($this->data[2]) == 20 &&
            ord($this->data[-2]) == Opcodes::OP_EQUALVERIFY &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG;
    }

    /**
     * handle output of TX 059787f0673ab2c00b8f2f9810fdd14b0cd6a3034cc44dc30de124f606d3670a
     * @return bool
     */
    public function isPayToPubKeyHashAlt(): bool
    {
        return $this->size == 26 &&
            ord($this->data[0]) == Opcodes::OP_DUP &&
            ord($this->data[1]) == Opcodes::OP_HASH160 &&
            ord($this->data[2]) == Opcodes::OP_PUSHDATA1 &&
            ord($this->data[3]) == 20 &&
            ord($this->data[-2]) == Opcodes::OP_EQUALVERIFY &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG;
    }

    /**
     * @return bool
     */
    public function isPayToScriptHash(): bool
    {
        return $this->size == 23 &&
            ord($this->data[0]) == Opcodes::OP_HASH160 &&
            ord($this->data[1]) == 20 &&
            ord($this->data[-1]) == Opcodes::OP_EQUAL;
    }

    /**
     * @return bool
     */
    public function isMultisig(): bool
    {
        $keys = ord($this->data[0]);
        $sigs = ord($this->data[-2]);

        if (!($this->size >= 24 &&
            $keys >= Opcodes::OP_1 && $keys <= Opcodes::OP_16 &&
            $sigs && $sigs <= Opcodes::OP_16 &&
            $keys >= $sigs &&
            ord($this->data[-1]) == Opcodes::OP_CHECKMULTISIG)) {
            return false;
        }

        for ($i = 1, $k = 0; $i < $this->size - 2; $i += 21, $k++) {
            if (ord($this->data[$i]) != 20) {
                return false;
            }
        }

        return $keys - Opcodes::OP_1 + 1 == $k;
    }

    /**
     * @return bool
     */
    public function isPayToWitnessPubKeyHash(): bool
    {
        $version = ord($this->data[0]);

        return $this->size == 22 &&
            $version >= Opcodes::OP_0 && $version <= Opcodes::OP_16 &&
            ord($this->data[1]) == 20;
    }

    /**
     * @return bool
     */
    public function isPayToWitnessScriptHash(): bool
    {
        $version = ord($this->data[0]);

        return $this->size == 34 &&
            $version >= Opcodes::OP_0 && $version <= Opcodes::OP_16 &&
            ord($this->data[1]) == 32;
    }

    /**
     * @param Bitcoin|null $network
     * @return string
     * @throws ScriptException
     * @throws \Exception
     * @throws \BitWasp\Bech32\Exception\Bech32Exception
     */
    public function getOutputAddress(Bitcoin $network = null): string
    {
        $addressSerializer = new AddressSerializer($network);

        if ($this->isPayToPubKey()) {
            if ($this->size == 35) {
                return $addressSerializer->getPayToPubKey(substr($this->data, 1, 33));
            } elseif ($this->size == 67) {
                return $addressSerializer->getPayToPubKey(substr($this->data, 1, 65));
            }
        }

        if ($this->isPayToPubKeyHash()) {
            return $addressSerializer->getPayToPubKeyHash(substr($this->data, 3, 20));
        }

        if ($this->isPayToPubKeyHashAlt()) {
            return $addressSerializer->getPayToPubKeyHash(substr($this->data, 4, 20));
        }

        if ($this->isPayToScriptHash()) {
            return $addressSerializer->getPayToScriptHash(substr($this->data, 2, 20));
        }

        if ($this->isPayToWitnessPubKeyHash()) {
            return $addressSerializer->getPayToWitnessPubKeyHash(substr($this->data, 2, 20));
        }

        if ($this->isPayToWitnessScriptHash()) {
            return $addressSerializer->getPayToWitnessScriptHash(substr($this->data, 2, 32));
        }

        throw new ScriptException('Unable to decode output address.');
    }
}