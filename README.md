## PHP Bitcoin Blockchain Parser

A PHP implementation of Bitcoin blockchain database parser.

### Features:

- Parse unordered block data
- Parse ordered block data
- Parse block index
- Parse chain state (UTXO database)

### Requirements

- PHP >= 7.1
- Bitcoin Core >= 0.15.1
- leveldb >= 1.20
- php-leveldb >= 0.2.1
- php-gmp >= 7.1

### Installation

```
composer require andkom/php-bitcoin-blockchain
```

### Examples

```php
$databaseReader = new DatabaseReader('/path/to/bitcoin');

// read ordered blocks
foreach ($databaseReader->readBlocks() as $block) {
}

// read unordered blocks
foreach ($databaseReader->readBlocksUnordered() as $block) {
}

// read UTXO 
foreach ($databaseReader->getChainstate()->read() as $utxo) {
}

// get block by hash
$block = $databaseReader->getBlockByHash('binary hash in little endian'); 

// get block by height
$block = $databaseReader->getBlockByHeight(12345);

// get best block hash
$hash = $databaseReader->getChainstate()->getBestBlock();
```

See more examples in the examples dir.

### LevelDB installation

Ubuntu/Debian:

```bash
apt-get install libleveldb-dev
pecl install leveldb-0.2.1
```

Mac OS:

```bash
brew install leveldb
pecl install leveldb-0.2.1
```

Or compile from source:

```bash
git clone https://github.com/google/leveldb.git
cd leveldb
mkdir -p build && cd build
cmake -DCMAKE_BUILD_TYPE=Release .. && cmake --build .
make install
cd ../../
git clone https://github.com/reeze/php-leveldb.git
cd php-leveldb
phpize
./configure --with-leveldb
make
make install
```

Make sure you've enabled leveldb.so extension in your php.ini.
