<?php

require_once '../vendor/autoload.php';

$dataDir = getenv('HOME') . '/Library/Application Support/Bitcoin';

$reader = new \AndKom\PhpBitcoinBlockchain\BlockchainReader($dataDir);

foreach ($reader->readBlocks(0, 100) as $height => $block) {
    echo $height . " => " . $block->header->getHash() . "\n";
}