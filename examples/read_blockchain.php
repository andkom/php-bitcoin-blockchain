<?php

require_once '../vendor/autoload.php';

$dataDir = getenv('HOME') . '/Library/Application Support/Bitcoin';

$reader = new \AndKom\Bitcoin\Blockchain\Reader\DatabaseReader($dataDir);

foreach ($reader->readBlocks(0, 100) as $height => $block) {
    echo $height . " => " . \AndKom\Bitcoin\Blockchain\Utils::hashToHex($block->header->getHash()) . "\n";
}