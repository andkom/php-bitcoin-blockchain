<?php

include '../vendor/autoload.php';

$blockDir = getenv('HOME') . '/Library/Application Support/Bitcoin/blocks';

$reader = new \AndKom\PhpBitcoinBlockchain\BlockFileReader();

// txid:index => balance
$outputs = [];

// txid:index => address
$addresses = [];

foreach ($reader->read($blockDir . '/blk00000.dat') as $block) {

    foreach ($block->transactions as $tx) {
        foreach ($tx->inputs as $in) {
            if ($in->isCoinbase()) continue;

            $prev = $in->prevTxHash . ':' . $in->prevTxOutIndex;
            $outputs[$prev] = 0;
        }

        $txid = $tx->getHash();

        foreach ($tx->outputs as $index => $out) {
            try {
                $address = $out->scriptPubKey->getOutputAddress();
            } catch (\Exception $e) {
                $address = 'unknown script: ' . $out->scriptPubKey;
            }

            $output = "$txid:$index";

            if (!isset($outputs[$output])) {
                $outputs[$output] = 0;
            }

            $outputs[$output] += $out->value;
            $addresses[$output] = $address;
        }
    }
}

$balances = [];

foreach ($outputs as $out => $value) {
    // @todo address may not exists because blocks are unordered
    $address = $addresses[$out];

    if (!isset($balances[$address])) {
        $balances[$address] = 0;
    }

    $balances[$address] += $value;
}

arsort($balances);

foreach ($balances as $address => $balance) {
    echo $address . ' => ' . bcdiv($balance, 1e8, 8) . " BTC\n";
}