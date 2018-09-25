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
    public function isPayToPubKey(): bool
    {
        // compressed pubkey
        if ($this->size == 35 &&
            ord($this->data[0]) == 33 &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG &&
            ($this->data[1] == "\x02" || $this->data[1] == "\x03")) {
            return true;
        }

        // uncompressed pubkey
        if ($this->size == 67 &&
            ord($this->data[0]) == 65 &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG &&
            $this->data[1] == "\x04") {
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
        try {
            $operations = $this->parse();
        } catch (\Exception $exception) {
            throw new ScriptException('Unable to decode output address (script parse error).');
        }

        $addressSerializer = new AddressSerializer($network);

        if ($this->isPayToPubKey()) {
            return $addressSerializer->getPayToPubKey($operations[0]->data);
        }

        if ($this->isPayToPubKeyHash()) {
            return $addressSerializer->getPayToPubKeyHash($operations[2]->data);
        }

        if ($this->isPayToScriptHash()) {
            return $addressSerializer->getPayToScriptHash($operations[1]->data);
        }

        if ($this->isPayToWitnessPubKeyHash()) {
            return $addressSerializer->getPayToWitnessPubKeyHash($operations[1]->data);
        }

        if ($this->isPayToWitnessScriptHash()) {
            return $addressSerializer->getPayToWitnessScriptHash($operations[1]->data);
        }

        if ($this->isMultisig()) {
            throw new ScriptException('Unable to decode output address (multisig).');
        }

        if ($this->isReturn()) {
            throw new ScriptException('Unable to decode output address (OP_RETURN).');
        }

        throw new ScriptException('Unable to decode output address.');
    }
}