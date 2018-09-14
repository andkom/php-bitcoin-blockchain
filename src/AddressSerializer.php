<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain;

use AndKom\Bitcoin\Blockchain\Network\Bitcoin;
use function BitWasp\Bech32\encodeSegwit;

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
     * @param string $pubKey
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKeyAddress(string $pubKey): string
    {
        return Utils::pubKeyToAddress($pubKey, $this->network::P2PKH_PREFIX);
    }

    /**
     * @param string $pubKeyHash
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKeyHashAddress(string $pubKeyHash): string
    {
        return Utils::hash160ToAddress($pubKeyHash, $this->network::P2PKH_PREFIX);
    }

    /**
     * @param string $scriptHash
     * @return string
     * @throws \Exception
     */
    public function getPayToScriptHash(string $scriptHash): string
    {
        return Utils::hash160ToAddress($scriptHash, $this->network::P2SH_PREFIX);
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