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
        return $this;
    }

    /**
     * @param bool $readable
     * @return array
     */
    public function parse(bool $readable = false): array
    {
        $operations = [];
        $stream = new Reader($this->data);

        while ($stream->getPosition() < $stream->getSize()) {
            $opcode = ord($stream->read(1));

            if ($opcode == Opcodes::OP_0) {
                $operations[] = '';
            } elseif ($opcode >= 0x01 && $opcode <= 0x4b) {
                $data = $stream->read($opcode);
                if ($readable) {
                    $operations[] = "PUSHDATA($opcode)[" . bin2hex($data) . ']';
                } else {
                    $operations[] = $data;
                }
            } elseif ($opcode >= Opcodes::OP_PUSHDATA1 && $opcode <= Opcodes::OP_PUSHDATA4) {
                $size = $stream->readVarInt();
                $data = $stream->read($size);
                if ($readable) {
                    $operations[] = "PUSHDATA($size)[" . bin2hex($data) . ']';
                } else {
                    $operations[] = $data;
                }
            } elseif ($opcode == Opcodes::OP_NEGATE) {
                $operations[] = -1;
            } elseif ($opcode >= Opcodes::OP_1 && $opcode <= Opcodes::OP_16) {
                $operations[] = $opcode - Opcodes::OP_1 + 1;
            } else {
                if ($readable) {
                    $operations[] = Opcodes::$names[$opcode];
                } else {
                    $operations[] = $opcode;
                }
            }
        }

        return $operations;
    }
}