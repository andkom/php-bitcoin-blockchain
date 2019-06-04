<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Script;

use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use AndKom\Bitcoin\Blockchain\Exceptions\AddressSerializeException;
use AndKom\Bitcoin\Blockchain\Exceptions\OutputDecodeException;
use AndKom\Bitcoin\Blockchain\Network\NetworkInterface;
use AndKom\Bitcoin\Blockchain\Serializer\AddressSerializer;

/**
 * Class ScriptPubKey
 * @package AndKom\Bitcoin\Blockchain\Script
 */
class ScriptPubKey extends Script
{
    /**
     * ScriptPubKey: OP_RETURN ...
     * @return bool
     */
    public function isReturn(): bool
    {
        return $this->size >= 1 && ord($this->data[0]) == Opcodes::OP_RETURN;
    }

    /**
     * ScriptPubKey: (empty)
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->size == 0;
    }

    /**
     * @param int $size
     * @param string $prefix
     * @return bool
     */
    protected function isPubKey(int $size, string $prefix): bool
    {
        if ($size == PublicKey::LENGTH_COMPRESSED &&
            ($prefix == PublicKey::PREFIX_COMPRESSED_EVEN ||
                $prefix == PublicKey::PREFIX_COMPRESSED_ODD)) {
            return true;
        }

        if ($size == PublicKey::LENGTH_UNCOMPRESSED &&
            $prefix == PublicKey::PREFIX_UNCOMPRESSED) {
            return true;
        }

        return false;
    }

    /**
     * ScriptPubKey: OP_PUSHDATA(33) [0x02] [pubkey compressed] OP_CHECKSIG
     * ScriptPubKey: OP_PUSHDATA(33) [0x03] [pubkey compressed] OP_CHECKSIG
     * ScriptPubKey: OP_PUSHDATA(65) [0x04] [pubkey] OP_CHECKSIG
     * @return bool
     */
    public function isPayToPubKey(): bool
    {
        return (($this->size == PublicKey::LENGTH_COMPRESSED + 2 ||
                $this->size == PublicKey::LENGTH_UNCOMPRESSED + 2) &&
            $this->isPubKey(ord($this->data[0]), $this->data[1]) &&
            ord($this->data[-1]) == Opcodes::OP_CHECKSIG);
    }

    /**
     * ScriptPubKey: OP_DUP OP_HASH160 OP_PUSHDATA(20) [pubkey hash] OP_EQUALVERIFY OP_CHECKSIG
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
     * ScriptPubKey: OP_HASH160 PUSHDATA(20) [script hash] OP_EQUAL
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
     * ScriptPubKey: [num sigs] [...pub keys..] [num pub keys] OP_CHECKMULTISIG
     * @return bool
     */
    public function isMultisig(): bool
    {
        // check pattern
        if (!($this->size >= 37 &&
            ord($this->data[0]) >= Opcodes::OP_1 && ord($this->data[0]) <= Opcodes::OP_16 &&
            ord($this->data[-2]) >= Opcodes::OP_1 && ord($this->data[-2]) <= Opcodes::OP_16 &&
            ord($this->data[-2]) >= ord($this->data[0]) &&
            ord($this->data[-1]) == Opcodes::OP_CHECKMULTISIG)) {
            return false;
        }

        // check valid keys
        for ($i = 1, $j = $k = 0; $i < $this->size - 2; $j++) {
            $size = ord($this->data[$i]);
            $k += $this->isPubKey($size, $this->data[$i + 1]);
            $i += $size + 1;
        }

        return $k > 0 && ord($this->data[-2]) - Opcodes::OP_1 + 1 == $j;
    }

    /**
     * ScriptPubKey: [version] OP_PUSHDATA(20) [pubkey hash]
     * @return bool
     */
    public function isPayToWitnessPubKeyHash(): bool
    {
        return $this->size == 22 &&
            ord($this->data[0]) >= Opcodes::OP_0 && ord($this->data[0]) <= Opcodes::OP_16 &&
            ord($this->data[1]) == 20;
    }

    /**
     * ScriptPubKey: [version] OP_PUSHDATA(32) [script hash]
     * @return bool
     */
    public function isPayToWitnessScriptHash(): bool
    {
        return $this->size == 34 &&
            ord($this->data[0]) >= Opcodes::OP_0 && ord($this->data[0]) <= Opcodes::OP_16 &&
            ord($this->data[1]) == 32;
    }

    /**
     * @param NetworkInterface|null $network
     * @return string
     * @throws OutputDecodeException
     * @throws AddressSerializeException
     */
    public function getOutputAddress(NetworkInterface $network = null): string
    {
        if ($this->isMultisig()) {
            throw new OutputDecodeException('Unable to decode output address (multisig).');
        }

        if ($this->isReturn()) {
            throw new OutputDecodeException('Unable to decode output address (OP_RETURN).');
        }

        if ($this->isEmpty()) {
            throw new OutputDecodeException('Unable to decode output address (empty).');
        }

        $addressSerializer = new AddressSerializer($network);

        if ($this->isPayToPubKey()) {
            if ($this->size == 35) {
                $pubKey = substr($this->data, 1, 33);
            } elseif ($this->size == 67) {
                $pubKey = substr($this->data, 1, 65);
            }

            if (!PublicKey::isFullyValid($pubKey)) {
                throw new OutputDecodeException('Unable to decode output address (invalid public key).');
            }

            return $addressSerializer->getPayToPubKey($pubKey);
        }

        if ($this->isPayToPubKeyHash()) {
            return $addressSerializer->getPayToPubKeyHash(substr($this->data, 3, 20));
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

        throw new OutputDecodeException('Unable to decode output address.');
    }
}