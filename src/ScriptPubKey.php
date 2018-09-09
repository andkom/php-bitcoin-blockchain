<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

/**
 * Class ScriptPubKey
 * @package AndKom\PhpBitcoinBlockchain
 */
class ScriptPubKey extends Script
{
    /**
     * @return bool
     */
    public function isP2PK(): bool
    {
        $operations = $this->parse();

        return count($operations) == 2 &&
            ($operations[0]->size == 33 || $operations[0]->size == 65) &&
            $operations[1]->code == Opcodes::OP_CHECKSIG;
    }

    /**
     * @return bool
     */
    public function isP2PKH(): bool
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
    public function isP2SH(): bool
    {
        $operations = $this->parse();

        return count($operations) == 3 &&
            $operations[0]->code == Opcodes::OP_HASH160 &&
            $operations[1]->size == 20 &&
            $operations[2]->code == Opcodes::OP_EQUAL;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getOutputAddress(): string
    {
        $operations = $this->parse();

        if ($this->isP2PK()) {
            $pubKey = $operations[0]->data;
            return Utils::pubKeyToAddress($pubKey, 0x00);
        }

        if ($this->isP2PKH()) {
            $pubKeyHash = $operations[2]->data;
            return Utils::hash160ToAddress($pubKeyHash, 0x00);
        }

        if ($this->isP2SH()) {
            $hash = $operations[1]->data;
            return Utils::hash160ToAddress($hash, 0x05);
        }

        throw new Exception('Unable to decode output address.');
    }
}