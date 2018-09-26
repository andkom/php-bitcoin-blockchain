<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Serializer;

use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use AndKom\Bitcoin\Blockchain\Network\NetworkFactory;
use AndKom\Bitcoin\Blockchain\Network\NetworkInterface;
use AndKom\Bitcoin\Blockchain\Utils;
use function BitWasp\Bech32\encodeSegwit;
use BitWasp\Bech32\Exception\Bech32Exception;
use StephenHill\Base58;

/**
 * Class AddressSerializer
 * @package AndKom\Bitcoin\Blockchain\Serializer
 */
class AddressSerializer
{
    /**
     * @var NetworkInterface
     */
    protected $network;

    /**
     * AddressSerializer constructor.
     * @param NetworkInterface $network
     */
    public function __construct(NetworkInterface $network = null)
    {
        if (!$network) {
            $network = NetworkFactory::getDefault();
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
        if (strlen($hash160) != 20) {
            throw new \InvalidArgumentException('Invalid hash160 size.');
        }

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
        if (!PublicKey::isValid($pubKey)) {
            throw new \InvalidArgumentException('Invalid public key format.');
        }

        return $this->hashToAddress(Utils::hash160($pubKey, true), $prefix);
    }

    /**
     * @param string $pubKey
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKey(string $pubKey): string
    {
        return $this->pubKeyToAddress($pubKey, $this->network->getPayToPubKeyPrefix());
    }

    /**
     * @param string $pubKeyHash
     * @return string
     * @throws \Exception
     */
    public function getPayToPubKeyHash(string $pubKeyHash): string
    {
        return $this->hashToAddress($pubKeyHash, $this->network->getPayToPubKeyHashPrefix());
    }

    /**
     * @param string $scriptHash
     * @return string
     * @throws \Exception
     */
    public function getPayToScriptHash(string $scriptHash): string
    {
        return $this->hashToAddress($scriptHash, $this->network->getPayToScriptHashPrefix());
    }

    /**
     * @param string $pubKeyHash
     * @param int $version
     * @return string
     * @throws Bech32Exception
     */
    public function getPayToWitnessPubKeyHash(string $pubKeyHash, int $version = 0): string
    {
        return encodeSegwit($this->network->getBech32HumanReadablePart(), $version, $pubKeyHash);
    }

    /**
     * @param string $scriptHash
     * @param int $version
     * @return string
     * @throws Bech32Exception
     */
    public function getPayToWitnessScriptHash(string $scriptHash, int $version = 0): string
    {
        return encodeSegwit($this->network->getBech32HumanReadablePart(), $version, $scriptHash);
    }
}