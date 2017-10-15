# phpeltoken.com

A blockchain sample project based on PHP Symfony framework.

*Goal - to implement basic blockchain ideas in order to understand them better.*

1. [Installation guide](INSTALLATION.md)
2. [External links](#external-links)

Very basic example:

```shell
make d_example
```

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

### API
* wallets

### External links
* [Minimum Viable Block Chain](https://www.igvita.com/2014/05/05/minimum-viable-block-chain/)
