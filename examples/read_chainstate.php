<?php

include '../vendor/autoload.php';

$dataDir = getenv('HOME') . '/Library/Application Support/Bitcoin';
$chainStateDir = "$dataDir/chainstate";

$reader = new \AndKom\PhpBitcoinBlockchain\ChainStateReader($chainStateDir);

foreach ($reader->read() as $unspentOutput) {
    echo "TX:       " . bin2hex(strrev($unspentOutput->hash)) . "\n";
    echo "Index:    " . $unspentOutput->index . "\n";
    echo "Height:   " . $unspentOutput->height . "\n";
    echo "Coinbase: " . $unspentOutput->coinbase . "\n";
    echo "Value:    " . bcdiv($unspentOutput->value, 1e8, 8) . " BTC\n";
    try {
        echo "Address:  " . $unspentOutput->getAddress() . "\n";
    } catch (\Exception $exception) {
        echo $exception->getMessage() . "\n";
    }
    echo "\n";
}

echo "Done.\n";