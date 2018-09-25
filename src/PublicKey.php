<?php

namespace AndKom\Bitcoin\Blockchain;

use AndKom\Bitcoin\Blockchain\Exception\PublicKeyException;
use Mdanter\Ecc\EccFactory;

/**
 * Class PublicKey
 * @package AndKom\Bitcoin\Blockchain
 */
class PublicKey
{
    const PREFIX_UNCOMPRESSED = "\x04";
    const PREFIX_COMPRESSED_EVEN = "\x02";
    const PREFIX_COMPRESSED_ODD = "\x03";

    const LENGTH_UNCOMPRESSED = 65;
    const LENGTH_COMPRESSED = 33;

    /**
     * @var \GMP
     */
    protected $x;

    /**
     * @var \GMP
     */
    protected $y;

    /**
     * @var bool
     */
    protected $wasOdd;

    /**
     * PublicKey constructor.
     * @param \GMP $x
     * @param \GMP|null $y
     * @param bool $wasOdd
     */
    public function __construct(\GMP $x, \GMP $y = null, bool $wasOdd = false)
    {
        $this->x = $x;
        $this->y = $y;
        $this->wasOdd = $wasOdd;
    }

    /**
     * @return \GMP
     */
    public function getX(): \GMP
    {
        return $this->x;
    }

    /**
     * @return \GMP
     * @throws PublicKeyException
     */
    public function getY(): \GMP
    {
        if ($this->isCompressed()) {
            throw new PublicKeyException("Compressed public key doesn't have Y coordinate.");
        }

        return $this->y;
    }

    /**
     * @return bool
     */
    public function wasOdd(): bool
    {
        return $this->wasOdd;
    }

    /**
     * @return bool
     */
    public function isCompressed(): bool
    {
        return is_null($this->y);
    }

    /**
     * @return PublicKey
     * @throws PublicKeyException
     */
    public function compress(): self
    {
        if ($this->isCompressed()) {
            throw new PublicKeyException('Public key is already compressed.');
        }

        $wasOdd = \gmp_cmp(
                \gmp_mod($this->y, \gmp_init(2)),
                \gmp_init(0)
            ) !== 0;

        return new static($this->x, null, $wasOdd);
    }

    /**
     * @return PublicKey
     * @throws PublicKeyException
     */
    public function decompress(): self
    {
        if (!$this->isCompressed()) {
            throw new PublicKeyException('Public key is already decompressed.');
        }

        $curve = EccFactory::getSecgCurves()->generator256k1()->getCurve();
        $y = $curve->recoverYfromX($this->wasOdd, $this->x);

        return new static($this->x, $y);
    }

    /**
     * @return \Mdanter\Ecc\Crypto\Key\PublicKey
     * @throws PublicKeyException
     */
    public function getEccPublicKey(): \Mdanter\Ecc\Crypto\Key\PublicKey
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getSecgCurves()->generator256k1();
        $curve = $generator->getCurve();

        if ($this->isCompressed()) {
            $key = $this->decompress();
        } else {
            $key = $this;
        }

        $point = $curve->getPoint($key->getX(), $this->getY());

        return new \Mdanter\Ecc\Crypto\Key\PublicKey($adapter, $generator, $point);
    }

    /**
     * @param string $data
     * @return PublicKey
     * @throws PublicKeyException
     */
    static public function parse(string $data): self
    {
        $length = strlen($data);

        if ($length == static::LENGTH_COMPRESSED) {
            $prefix = substr($data, 0, 1);

            if ($prefix != static::PREFIX_COMPRESSED_ODD && $prefix != static::PREFIX_COMPRESSED_EVEN) {
                throw new PublicKeyException('Invalid compressed public key prefix.');
            }

            $x = \gmp_init(bin2hex(substr($data, 1, 32)), 16);
            $y = null;
        } elseif ($length == static::LENGTH_UNCOMPRESSED) {
            $prefix = substr($data, 0, 1);

            if ($prefix != static::PREFIX_UNCOMPRESSED) {
                throw new PublicKeyException('Invalid uncompressed public key prefix.');
            }

            $x = \gmp_init(bin2hex(substr($data, 1, 32)), 16);
            $y = \gmp_init(bin2hex(substr($data, 33, 32)), 16);
        } else {
            throw new PublicKeyException('Invalid public key size.');
        }

        return new static($x, $y, $prefix == static::PREFIX_COMPRESSED_ODD);
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $x = hex2bin(\gmp_strval($this->x, 16));

        if ($this->isCompressed()) {
            $prefix = $this->wasOdd ? static::PREFIX_COMPRESSED_ODD : static::PREFIX_COMPRESSED_EVEN;
            $y = '';
        } else {
            $prefix = static::PREFIX_UNCOMPRESSED;
            $y = hex2bin(\gmp_strval($this->y, 16));
        }

        return $prefix . $x . $y;
    }
}