<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\Bitcoin\Blockchain\Network\Bitcoin;
use function BitWasp\Bech32\encodeSegwit;
use StephenHill\Base58;

/**
 * Class AddressSerializer
 * @package AndKom\Bitcoin\Blockchain
 */
class AddressSerializer
{
    /**
     * @var Bitcoin
     */
    protected $network;

    /**
     * AddressSerializer constructor.
     * @param Bitcoin $network
     */
    public function __construct(Bitcoin $network = null)
    {
        if (!$network) {
            $network = new Bitcoin();
        }

        $this->network = $network;
    }

    /**
     * @param string $hash160
     * @param int $prefix
     * @return string
     * @throws \Exception
     */
    public function hashToAddress(string $hash160, int $prefix): string
    {
        $hash160 = chr($prefix) . $hash160;
        $checksum = substr(Utils::hash256($hash160, true), 0, 4);
        $address = $hash160 . $checksum;
        return (new Base58())->encode($address);
    }

    /**
     * @param string $pubKey
     * @param int $prefix
     * @return string
     * @throws \Exception
     */
    public function pubKeyToAddress(string $pubKey, int $prefix): string
    {
        return $this->hashToAddress(Utils::hash160($pubKey, true), $prefix);
    }

    /**
     * @param string $pubKey
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKey(string $pubKey): string
    {
        return $this->pubKeyToAddress($pubKey, $this->network::P2PKH_PREFIX);
    }

    /**
     * @param string $pubKeyHash
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKeyHash(string $pubKeyHash): string
    {
        return $this->hashToAddress($pubKeyHash, $this->network::P2PKH_PREFIX);
    }

    /**
     * @param string $scriptHash
     * @return string
     * @throws \Exception
     */
    public function getPayToScriptHash(string $scriptHash): string
    {
        return $this->hashToAddress($scriptHash, $this->network::P2SH_PREFIX);
    }

    /**
     * @param string $pubKeyHash
     * @param int $version
     * @return string
     * @throws \BitWasp\Bech32\Exception\Bech32Exception
     */
    public function getPayToWitnessPubKeyHash(string $pubKeyHash, int $version = 0): string
    {
        return encodeSegwit($this->network::BECH32_HRP, $version, $pubKeyHash);
    }

    /**
     * @param string $scriptHash
     * @param int $version
     * @return string
     * @throws \BitWasp\Bech32\Exception\Bech32Exception
     */
    public function getPayToWitnessScriptHash(string $scriptHash, int $version = 0): string
    {
        return encodeSegwit($this->network::BECH32_HRP, $version, $scriptHash);
    }
}