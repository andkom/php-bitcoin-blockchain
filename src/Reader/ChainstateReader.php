<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Reader;

use AndKom\BCDataStream\Reader;
use AndKom\Bitcoin\Blockchain\Database\UnspentOutput;
use AndKom\Bitcoin\Blockchain\Exceptions\DatabaseException;
use AndKom\Bitcoin\Blockchain\Utils;

/**
 * Class ChainstateReader
 * @package AndKom\Bitcoin\Blockchain\Reader
 */
class ChainstateReader
{
    const PREFIX_COIN = 'C';
    const KEY_BEST_BLOCK = 'B';
    const KEY_OBFUSCATE_KEY = "\x0e\x00obfuscate_key";

    /**
     * @var string
     */
    protected $chainstateDir;

    /**
     * @var \LevelDB
     */
    protected $db;

    /**
     * @var string
     */
    protected $obfuscateKey;

    /**
     * ChainstateReader constructor.
     * @param string $chainstateDir
     */
    public function __construct(string $chainstateDir = '')
    {
        $this->chainstateDir = $chainstateDir;
    }

    /**
     * @return ChainstateReader
     * @throws DatabaseException
     */
    protected function openDb(): self
    {
        if (!class_exists('\LevelDB')) {
            throw new DatabaseException('Extension leveldb is not installed.');
        }

        $this->db = new \LevelDB($this->chainstateDir);

        return $this;
    }

    /**
     * @return ChainstateReader
     * @throws DatabaseException
     */
    protected function assertDbOpened(): self
    {
        if (!$this->db) {
            throw new DatabaseException('Database is not opened.');
        }

        return $this;
    }

    /**
     * @return ChainstateReader
     * @throws DatabaseException
     */
    protected function closeDb(): self
    {
        $this->assertDbOpened();

        $this->db->close();
        $this->db = null;

        return $this;
    }

    /**
     * @return string
     * @throws DatabaseException
     */
    protected function readObfuscateKey(): string
    {
        $this->assertDbOpened();

        $obfuscateKey = $this->db->get(static::KEY_OBFUSCATE_KEY);

        // first byte is key size
        $obfuscateKey = substr($obfuscateKey, 1);

        return $obfuscateKey;
    }

    /**
     * @param string $value
     * @return string
     * @throws DatabaseException
     */
    protected function deobfuscateValue(string $value): string
    {
        if (!$this->obfuscateKey) {
            $this->obfuscateKey = $this->readObfuscateKey();
        }

        return $this->obfuscateKey ? Utils::xor($this->obfuscateKey, $value) : $value;
    }

    /**
     * @return string
     * @throws DatabaseException
     */
    public function getBestBlock(): string
    {
        $this->openDb();

        $bestBlock = $this->db->get(static::KEY_BEST_BLOCK);
        $bestBlock = $this->deobfuscateValue($bestBlock);

        $this->closeDb();

        if (!$bestBlock) {
            throw new DatabaseException('Unable to get best block.');
        }

        return $bestBlock;
    }

    /**
     * @return string
     * @throws DatabaseException
     */
    public function getObfuscateKey(): string
    {
        $this->openDb();

        $obfuscateKey = $this->readObfuscateKey();

        $this->closeDb();

        return $obfuscateKey;
    }

    /**
     * @return \Generator
     * @throws DatabaseException
     */
    public function read(): \Generator
    {
        $this->openDb();

        foreach ($this->db->getIterator() as $key => $value) {
            $key = new Reader($key);
            $prefix = $key->read(1);

            if ($prefix != static::PREFIX_COIN) {
                continue;
            }

            $value = $this->deobfuscateValue($value);

            yield UnspentOutput::parse($key, new Reader($value));
        }

        $this->closeDb();
    }
}