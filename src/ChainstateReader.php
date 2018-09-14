<?php

declare(strict_types=1);

namespace AndKom\PhpBitcoinBlockchain;

use AndKom\BCDataStream\Reader;

/**
 * Class ChainstateReader
 * @package AndKom\PhpBitcoinBlockchain
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
     * @return \LevelDB
     */
    protected function openDb(): \LevelDB
    {
        if ($this->db) {
            return $this->db;
        }

        return $this->db = new \LevelDB($this->chainstateDir);
    }

    /**
     * @return ChainstateReader
     */
    protected function closeDb(): self
    {
        if ($this->db) {
            $this->db->close();
            $this->db = null;
        }

        return $this;
    }

    /**
     * @param \LevelDB $db
     * @return string
     */
    protected function getObfuscateKeyFromDb(\LevelDB $db): string
    {
        $obfuscateKey = $db->get(static::KEY_OBFUSCATE_KEY);

        // first byte is key size
        $obfuscateKey = substr($obfuscateKey, 1);

        return $obfuscateKey;
    }

    /**
     * @param \LevelDB $db
     * @param string $value
     * @return string
     */
    protected function deobfuscateValue(\LevelDB $db, string $value): string
    {
        if (!$this->obfuscateKey) {
            $this->obfuscateKey = $this->getObfuscateKeyFromDb($db);
        }

        return $this->obfuscateKey ? Utils::xor($this->obfuscateKey, $value) : $value;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \LevelDBException
     */
    public function getBestBlock(): string
    {
        $db = $this->openDb();

        $bestBlock = $db->get(static::KEY_BEST_BLOCK);
        $bestBlock = $this->deobfuscateValue($db, $bestBlock);

        $this->closeDb();

        if (!$bestBlock) {
            throw new Exception('Unable to get best block.');
        }

        return $bestBlock;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \LevelDBException
     */
    public function getObfuscateKey(): string
    {
        $db = $this->openDb();

        $obfuscateKey = $this->getObfuscateKeyFromDb($db);

        $this->closeDb();

        if (!$obfuscateKey) {
            throw new Exception('Unable to get obfuscate key.');
        }

        return $obfuscateKey;
    }

    /**
     * @return \Generator
     * @throws Exception
     * @throws \LevelDBException
     */
    public function read(): \Generator
    {
        if (!class_exists('\LevelDB')) {
            throw new Exception('Extension leveldb is not installed.');
        }

        $db = new \LevelDB($this->chainstateDir);

        foreach ($db->getIterator() as $key => $value) {
            $key = new Reader($key);
            $prefix = $key->read(1);

            if ($prefix != static::PREFIX_COIN) {
                continue;
            }

            $value = $this->deobfuscateValue($db, $value);

            yield UnspentOutput::parse($key, new Reader($value));
        }

        $this->closeDb();
    }
}