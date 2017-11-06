# phpeltoken.com

A blockchain sample project based on PHP Symfony framework.

*Goal - to implement basic blockchain ideas in order to understand them better.*

1. [Installation guide](INSTALLATION.md)
2. [How to use](#how-to-use)
3. [Structure](#structure)
4. [Programming](#programming-features)
5. [Restrictions](#restrictions)
6. [Tags](#tags)
7. [TODO](#todo)
8. [External links](#external-links)

## How to use:
```shell
make d_init_blockchain
```

This command creates genesis block and a couple of sample blocks. At the end it prints
available inputs (input transactions) for every sample wallet.

Other commands:

```shell
make d_check_blockchain_validity
make d_check_transaction_pool_validity
make d_show_available_input
```

In order to publish new transaction run interactive command. Choose sender, recipient address
from the list of sample wallets and type in amount. It must be possible to use full amount of one of available inputs.
```shell
make d_publish_transaction
```

In order to mine new block with current transaction pool run interactive command. Choose miner.
```shell
make d_mine_block
```

## Programming features:
* Code sniffer and PHP Extended PHPStorm validations. All files are "green marked"
* Custom exception classes, ex. [BlockchainDoesNotExistException](src/AppBundle/Exception/Blockchain/BlockchainDoesNotExistException.php)
* fixtures and migrations for postgresql
* small methods - one task for everyone
* Block and transaction hash calculations are inside services - models are free from these details.
* DDD-featured method

## Structure

Models - condition only, no business logic, DDD features like named methods isRecipient
* [Block](src/AppBundle/Model/Blockchain/Block.php)
* [Transaction](src/AppBundle/Model/Blockchain/Transaction.php)

DTO:
* [InputOutputDto](src/AppBundle/Model/Blockchain/InputOutputDto.php)

Services (business logic):
* [Blockchain](src/AppBundle/Service/Blockchain.php) - for blocks as chain. It includes other services.
* [BlockService](src/AppBundle/Service/Blockchain/BlockService.php) - for separate block (not blocks!). It includes TransactionService.
* [TransactionService](src/AppBundle/Service/Blockchain/TransactionService.php) - for pool of transactions
* [MiningService](src/AppBundle/Service/Blockchain/MiningService.php) - mining-related logic
* [WalletService](src/AppBundle/Service/Blockchain/WalletService.php) - wallet-related logic (fetch only)
* [SecurityService](src/AppBundle/Service/Security/SecurityService.php) - asymmetric cryptography related logic

Other:
* [docker-compose file](docker-compose.yml)
* [PHP Dockerfile](etc/docker/php/Dockerfile)

Persistence:
* wallets and serialized blocks - in postgreSQL
* transaction pool - in redis (just for convenience)

## Restrictions:
* It is possible to transact only full amount of coins of previous transaction.
* Only integer amount of transaction

## Tags:
* #refactor tag marks places for future refactoring

## TODO
* Transactions pool as separate class not inside persistence.
* move blockchain check to separate checker.
* place blockchain settings (ex miner reward) separately.
* REST API
* Unit tests

### External links
* [Minimum Viable Block Chain](https://www.igvita.com/2014/05/05/minimum-viable-block-chain/)
* [How the Bitcoin protocol actually works](http://www.michaelnielsen.org/ddi/how-the-bitcoin-protocol-actually-works/)
* [Crackcoin tutorial project (Python)](https://github.com/DutchGraa/crackcoin)