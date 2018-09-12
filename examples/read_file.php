<?php

include '../vendor/autoload.php';

$dataDir = getenv('HOME') . '/Library/Application Support/Bitcoin';
$blocksDir = "$dataDir/blocks";
$blockFile = "$blocksDir/blk00001.dat";

$reader = new \AndKom\PhpBitcoinBlockchain\BlockFileReader();

foreach ($reader->read($blockFile) as $block) {
    foreach ($block->transactions as $tx) {
        echo "\nTX: " . $tx->getHash() . "\n";

        foreach ($tx->inputs as $in) {
            if ($in->isCoinbase()) {
                echo "IN: Coinbase\n";
            } else {
                echo "IN: " . $in->prevTxHash . ':' . $in->prevTxOutIndex . "\n";
            }
        }

        foreach ($tx->outputs as $out) {
            try {
                $address = $out->scriptPubKey->getOutputAddress();
            } catch (\Exception $e) {
                $address = 'Unable to decode output address.';
            }

            echo "OUT: " . bcdiv($out->value, 1e8, 8) . " BTC => " . $address . "\n";
        }
    }
}