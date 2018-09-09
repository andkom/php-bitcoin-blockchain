<?php

include '../vendor/autoload.php';

$blockDir = getenv('HOME') . '/Library/Application Support/Bitcoin/blocks';

$reader = new \AndKom\PhpBitcoinBlockchain\BlockFileReader();

foreach ($reader->read($blockDir . '/blk00000.dat') as $block) {

    echo str_repeat('=', 71) . "\n";
    echo "Block: " . $block->header->getHash() . "\n";
    echo "Date: " . date('r', $block->header->time) . "\n";

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