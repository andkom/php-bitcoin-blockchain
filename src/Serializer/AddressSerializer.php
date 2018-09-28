<?php

declare(strict_types=1);

namespace AndKom\Bitcoin\Blockchain\Serializer;

use AndKom\Bitcoin\Blockchain\Crypto\PublicKey;
use AndKom\Bitcoin\Blockchain\Exceptions\AddressSerializeException;
use AndKom\Bitcoin\Blockchain\Network\NetworkFactory;
use AndKom\Bitcoin\Blockchain\Network\NetworkInterface;
use AndKom\Bitcoin\Blockchain\Utils;
use function BitWasp\Bech32\encodeSegwit;
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
     * @throws AddressSerializeException
     */
    public function hashToAddress(string $hash160, int $prefix): string
    {
        if (strlen($hash160) != 20) {
            throw new AddressSerializeException('Invalid hash160 size.');
        }

        $hash160 = chr($prefix) . $hash160;
        $checksum = substr(Utils::hash256($hash160, true), 0, 4);
        $address = $hash160 . $checksum;

        try {
            $base58address = (new Base58())->encode($address);
        } catch (\Exception $exception) {
            throw new AddressSerializeException('Unable to encode base58 address.', 0, $exception);
        }

        return $base58address;
    }

    /**
     * @param string $hash
     * @param int $version
     * @return string
     * @throws AddressSerializeException
     */
    public function hashToSegwitAddress(string $hash, int $version = 0): string
    {
        $length = strlen($hash);

        if ($length != 20 && $length != 32) {
            throw new AddressSerializeException('Invalid hash size.');
        }

        try {
            $segwitAddress = encodeSegwit($this->network->getBech32HumanReadablePart(), $version, $hash);
        } catch (\Exception $exception) {
            throw new AddressSerializeException('Unable to encode segwit address.', 0, $exception);
        }

        return $segwitAddress;
    }

    /**
     * @param string $pubKey
     * @param int $prefix
     * @return string
     * @throws AddressSerializeException
     */
    public function pubKeyToAddress(string $pubKey, int $prefix): string
    {
        if (!PublicKey::isValid($pubKey)) {
            throw new AddressSerializeException('Invalid public key format.');
        }

        return $this->hashToAddress(Utils::hash160($pubKey, true), $prefix);
    }

    /**
     * @param string $pubKey
     * @return string
     * @throws AddressSerializeException
     */
    public function getPayToPubKey(string $pubKey): string
    {
        return $this->pubKeyToAddress($pubKey, $this->network->getPayToPubKeyPrefix());
    }

    /**
     * @param string $pubKeyHash
     * @return string
     * @throws AddressSerializeException
     */
    public function getPayToPubKeyHash(string $pubKeyHash): string
    {
        return $this->hashToAddress($pubKeyHash, $this->network->getPayToPubKeyHashPrefix());
    }

    /**
     * @param string $scriptHash
     * @return string
     * @throws AddressSerializeException
     */
    public function getPayToScriptHash(string $scriptHash): string
    {
        return $this->hashToAddress($scriptHash, $this->network->getPayToScriptHashPrefix());
    }

    /**
     * @param string $pubKeyHash
     * @param int $version
     * @return string
     * @throws AddressSerializeException
     */
    public function getPayToWitnessPubKeyHash(string $pubKeyHash, int $version = 0): string
    {
        return $this->hashToSegwitAddress($pubKeyHash, $version);
    }

    /**
     * @param string $scriptHash
     * @param int $version
     * @return string
     * @throws AddressSerializeException
     */
    public function getPayToWitnessScriptHash(string $scriptHash, int $version = 0): string
    {
        return $this->hashToSegwitAddress($scriptHash, $version);
    }
}