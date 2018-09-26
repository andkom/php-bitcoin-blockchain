<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Tests;

use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    protected $x = '6655feed4d214c261e0a6b554395596f1f1476a77d999560e5a8df9b8a1a3515';
    protected $y = '217e88dd05e938efdd71b2cce322bf01da96cd42087b236e8f5043157a9c068e';

    public function testCompress()
    {
        $pk = new PublicKey(\gmp_init($this->x, 16), \gmp_init($this->y, 16));

        $this->assertEquals(\gmp_strval($pk->getX(), 16), $this->x);
        $this->assertEquals(\gmp_strval($pk->getY(), 16), $this->y);
        $this->assertFalse($pk->isCompressed());

        $pkc = $pk->compress();

        $this->assertTrue($pkc->isCompressed());
        $this->assertFalse($pkc->wasOdd());
        $this->assertEquals(\gmp_strval($pkc->getX(), 16), $this->x);
    }

    public function testDecompress()
    {
        $pkc = new PublicKey(\gmp_init($this->x, 16), null, false);

        $pk = $pkc->decompress();

        $this->assertFalse($pk->isCompressed());
        $this->assertEquals(\gmp_strval($pk->getX(), 16), $this->x);
        $this->assertEquals(\gmp_strval($pk->getY(), 16), $this->y);
    }

    public function testParseUncompressed()
    {
        $key = "04{$this->x}{$this->y}";

        $pk = PublicKey::parse(hex2bin($key));

        $this->assertEquals(\gmp_strval($pk->getX(), 16), $this->x);
        $this->assertEquals(\gmp_strval($pk->getY(), 16), $this->y);
        $this->assertFalse($pk->isCompressed());
    }

    public function testParseCompressed()
    {
        $key = "02{$this->x}";

        $pk = PublicKey::parse(hex2bin($key));

        $this->assertEquals(\gmp_strval($pk->getX(), 16), $this->x);
        $this->assertTrue($pk->isCompressed());
    }

    public function testSerializeUncompressed()
    {
        $key = "04{$this->x}{$this->y}";

        $pk = new PublicKey(\gmp_init($this->x, 16), \gmp_init($this->y, 16));

        $this->assertEquals($pk->serialize(), hex2bin($key));
    }

    public function testSerializeCompressed()
    {
        $key = "02{$this->x}";

        $pk = new PublicKey(\gmp_init($this->x, 16), null, false);

        $this->assertEquals($pk->serialize(), hex2bin($key));
    }

    public function testValid()
    {
        $key = '040a464653204c756b652d4a72206c656176652074686520626c6f636b636861696e20616c6f6e65210a4f682c20616e6420676f642069736e2774207265616c0a';

        $this->assertFalse(PublicKey::isFullyValid(hex2bin($key)));
    }
}