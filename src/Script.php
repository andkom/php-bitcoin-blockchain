<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;

/**
 * Class Script
 * @package AndKom\PhpBitcoinBlockchain
 */
class Script
{
    /**
     * @var string
     */
    public $data;

    /**
     * @var array
     */
    protected $operations;

    /**
     * Script constructor.
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->setData($data);
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return Script
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        $this->operations = null;
        return $this;
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        if (!is_null($this->operations)) {
            return $this->operations;
        }

        $this->operations = [];
        $stream = new Reader($this->data);

        while ($stream->getPosition() < $stream->getSize()) {
            $code = ord($stream->read(1));
            $data = null;
            $size = 0;

            if ($code == Opcodes::OP_0) {
                $data = '';
            } elseif ($code >= 0x01 && $code <= 0x4b) {
                $data = $stream->read($code);
                $size = $code;
            } elseif ($code >= Opcodes::OP_PUSHDATA1 && $code <= Opcodes::OP_PUSHDATA4) {
                $size = $stream->readVarInt();
                $data = $stream->read($size);
            } elseif ($code == Opcodes::OP_1NEGATE) {
                $data = chr(-1);
                $size = 1;
            } elseif ($code >= Opcodes::OP_1 && $code <= Opcodes::OP_16) {
                $data = chr($code - Opcodes::OP_1 + 1);
                $size = 1;
            }

            $this->operations[] = new Operation($code, $data, $size);
        }

        return $this->operations;
    }

    /**
     * @return string
     */
    public function getHumanReadable(): string
    {
        return implode(' ', $this->parse());
    }

    /**
     * @return bool
     */
    public function isP2PK(): bool
    {
        $operations = $this->parse();

        return count($operations) == 2 &&
            $operations[0]->size == 65 &&
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
     * @return bool
     */
    public function isMultisig(): bool
    {
        $operations = $this->parse();

        return ($count = count($operations)) >= 4 &&
            ord($operations[0]->data) >= 1 &&
            ord($operations[$count - 2]->data) >= 1 &&
            $operations[$count - 1]->code == Opcodes::OP_CHECKMULTISIG;
    }
}