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
    public function __toString()
    {
        return $this->getHumanReadable();
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
}