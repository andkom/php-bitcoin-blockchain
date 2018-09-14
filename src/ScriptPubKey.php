<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

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
        $operations = $this->parse();

        return count($operations) >= 1 &&
            $operations[0]->code == Opcodes::OP_RETURN;
    }

    /**
     * @return bool
     */
    public function isPayToPubKey(): bool
    {
        $operations = $this->parse();

        return count($operations) == 2 &&
            ($operations[0]->size == 33 || $operations[0]->size == 65) &&
            $operations[1]->code == Opcodes::OP_CHECKSIG;
    }

    /**
     * @return bool
     */
    public function isPayToPubKeyHash(): bool
    {
        $operations = $this->parse();

        return count($operations) == 5 &&
            $operations[0]->code == Opcodes::OP_DUP &&
            $operations[1]->code == Opcodes::OP_HASH160 &&
            $operations[2]->size == 20 &&
            $operations[3]->code == Opcodes::OP_EQUALVERIFY &&
            $operations[4]->code == Opcodes::OP_CHECKSIG;
    }

    /**
     * @return bool
     */
    public function isPayToScriptHash(): bool
    {
        $operations = $this->parse();

        return count($operations) == 3 &&
            $operations[0]->code == Opcodes::OP_HASH160 &&
            $operations[1]->size == 20 &&
            $operations[2]->code == Opcodes::OP_EQUAL;
    }

    /**
     * @return bool
     */
    public function isMultisig(): bool
    {
        $operations = $this->parse();

        return ($count = count($operations)) >= 4 &&
            $operations[0]->code >= Opcodes::OP_1 &&
            $operations[$count - 2]->code >= Opcodes::OP_1 &&
            $operations[$count - 1]->code == Opcodes::OP_CHECKMULTISIG;
    }

    /**
     * @return bool
     */
    public function isPayToWitnessPubKeyHash(): bool
    {
        $operations = $this->parse();

        return count($operations) == 2 &&
            $operations[0]->code >= Opcodes::OP_0 &&
            $operations[0]->code <= Opcodes::OP_16 &&
            $operations[1]->size == 20;
    }

    /**
     * @return bool
     */
    public function isPayToWitnessScriptHash(): bool
    {
        $operations = $this->parse();

        return count($operations) == 2 &&
            $operations[0]->code >= Opcodes::OP_0 &&
            $operations[0]->code <= Opcodes::OP_16 &&
            $operations[1]->size == 32;
    }

    /**
     * @param Bitcoin|null $network
     * @return string
     * @throws Exception
     * @throws \Exception
     * @throws \BitWasp\Bech32\Exception\Bech32Exception
     */
    public function getOutputAddress(Bitcoin $network = null): string
    {
        try {
            $operations = $this->parse();
        } catch (\Exception $exception) {
            throw new Exception('Unable to decode output address (script parse error).');
        }

        $addressSerializer = new AddressSerializer($network);

        if ($this->isPayToPubKey()) {
            return $addressSerializer->getPayToPubKeyAddress($operations[0]->data);
        }

        if ($this->isPayToPubKeyHash()) {
            return $addressSerializer->getPayToPubKeyHashAddress($operations[2]->data);
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
            throw new Exception('Unable to decode output address (multisig).');
        }

        if ($this->isReturn()) {
            throw new Exception('Unable to decode output address (OP_RETURN).');
        }

        throw new Exception('Unable to decode output address.');
    }
}