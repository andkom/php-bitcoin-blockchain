<?php

require_once '../vendor/autoload.php';

$dataDir = getenv('HOME') . '/Library/Application Support/Bitcoin';
$blocksDir = "$dataDir/blocks";
$indexDir = "$blocksDir/index";

$reader = new \AndKom\Bitcoin\Blockchain\Reader\BlockIndexReader($indexDir);

echo "Reading block index...\n";

$index = $reader->read();

echo 'Last file: ' . $index->getLastFile() . "\n";
echo 'Min height: ' . $index->getMinHeight() . "\n";
echo 'Max height: ' . $index->getMaxHeight() . "\n";

var_dump($index->getFileInfo(0));

var_dump($index->getBlockInfoByHeight(0));