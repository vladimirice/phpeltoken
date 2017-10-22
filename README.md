# phpeltoken.com

A blockchain sample project based on PHP Symfony framework.

*Goal - to implement basic blockchain ideas in order to understand them better.*

1. [Installation guide](INSTALLATION.md)
2. [External links](#external-links)

Very basic example:

```shell
make d_example
```

## Transactions

### Transaction steps
1. Node broadcasts new transaction to the network
2. Other nodes receive transaction and add it to transaction pending list
3. Node-miner wants to accept this transaction (or set of transactions)
4. Node miner calculates proof of future block
5. In parallel miner checks if somebody already calculated the same block.
6. After success calculation node miner broadcasts new block to the network
7. Other nodes receives the block, validates it and add it to their chain.


### Transaction validation
1. There is a valid block.
2. There are N amount of blocks right after the block (long chain).
3. There are a valid transaction input outputs
4. Sender signature is ok
5. Transaction hash is unique
6. Miner reward validation
7. Miner commission validation

### Simplifications
1. Sender and recipient addresses are just pure public keys without any other encoding


## Node types
1. Listener - everybody
2. Broadcaster - wallet
3. Miner

## Node
1. Fresh node receives all the chain and validate all the chain.
2. Next time node validates only new transactions and block (based on their timestamps)


## Components location

Transaction class
```php
src/AppBundle/Model/Transaction.php
```



Genesis transaction has only output and no inputs


1. Sender, receiver, amount fields
2. Sender signature.
3. Transaction hash
4. Block hash - in order to find the block quicker





## To implement:

### Consensus and validations
* Longest valid chain is authoritative
* Sum of deposit and withdrawal must be 0
* Do not allow overdraft
* double transaction resistance
* public-private key transactions signature
* Link proof of work and related transactions
* Miner himself creates a lot of small transactions and receive rewards - how to resist

### Block validity
* Check block hash
* Check is transaction correct
* Check chain is correct by previous block checks

### Node scenarios
1. Download chains
2. Validate chains - store a valid copy
3. Announce their appearance in network
4. Receive new blocks and validate them
4. Start to listen for the new transactions
5. Mine blocks and broadcast new blocks to the network
6. Blockchain fork

### Proof-of-work
* An algorithm to decrease block proof time dispersion 

### Transactions
* Transactions with multiple inputs and outputs

### API
* wallets

### External links
* [Minimum Viable Block Chain](https://www.igvita.com/2014/05/05/minimum-viable-block-chain/)
* [How the Bitcoin protocol actually works](http://www.michaelnielsen.org/ddi/how-the-bitcoin-protocol-actually-works/)
* [Crackcoin tutorial project (Python)](https://github.com/DutchGraa/crackcoin)