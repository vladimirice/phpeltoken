<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 24/10/2017
 * Time: 23:13
 */

namespace AppBundle\Repository\Blockchain;

use Doctrine\DBAL\Connection;

class BlockchainRepository
{
    /** @var  Connection */
    private $connection;

    /**
     * BlockchainRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int    $index
     * @param string $blockJson
     * @return int
     */
    public function insertBlock(int $index, string $blockJson): int
    {
        return $this->connection->insert('blockchain', [
            'id' => $index,
            'block_json' => $blockJson,
        ]);
    }

    /**
     * @return array
     */
    public function getAllBlocks(): array
    {
        $smtp = $this->connection->executeQuery('SELECT id, block_json FROM blockchain ORDER BY id ASC');

        return $smtp->fetchAll();
    }
}
