<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use Doctrine\DBAL\Connection;

class EntityManager
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Repository[]
     */
    protected $repositories;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    public function getRepository($name)
    {
        if (isset($this->repositories[$name])) {
            return $this->repositories[$name];
        }

        return $this->repositories[$name] = new Repository($this, $name);
    }
}
