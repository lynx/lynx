<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;

class EntityManager
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Repository[]
     */
    protected $repositories;

    /**
     * @var ClassMetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Connection $connection
     * @param Configuration $configuration
     */
    public function __construct(Connection $connection, Configuration $configuration)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
        $this->cache = $configuration->getResultCacheImpl();
        $this->metadataFactory = new ClassMetadataFactory($this);
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @param $className
     * @param $id
     * @return null|object
     */
    public function find($className, $id)
    {
        return $this->getRepository($className)->getOne($id);
    }

    /**
     * @param $className
     * @return Repository
     */
    public function getRepository($className)
    {
        if (isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }

        if (class_exists($className . 'Repository', true)) {
            $class = $className . 'Repository';
            return $this->repositories[$className] = new $class($this, $className);
        }

        return $this->repositories[$className] = new Repository($this, $className);
    }

    /**
     * @param string $className
     * @return object
     */
    public function getObject($className)
    {
        $instantiator = new \Doctrine\Instantiator\Instantiator();
        return $instantiator->instantiate($className);
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
