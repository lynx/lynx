<?php

/**
 * @author Patsura Dmitry <zaets28rus@gmail.com>
 */

namespace Lynx\Tests;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Lynx\EntityManager;
use Model\User;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->setResultCacheImpl(
            new \Doctrine\Common\Cache\ArrayCache()
        );

        $configuration->setMetadataDriverImpl(
            new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
                new AnnotationReader(),
                realpath(__DIR__ . '/Models/')
            )
        );

        $connection = \Doctrine\DBAL\DriverManager::getConnection(
            array(
                'driver' => $GLOBALS['db_type'],
                'host' => $GLOBALS['db_host'],
                'dbname' => $GLOBALS['db_name'],
                'user' => $GLOBALS['db_username'],
                'password' => $GLOBALS['db_password']
            )
        );

        $configuration->setMetadataCacheImpl(
            new \Doctrine\Common\Cache\ZendDataCache()
        );

        $this->em = new \Lynx\EntityManager(
            $connection,
            $configuration
        );
    }

    /**
     * @param User $entity
     */
    public static function assertSuccessUser(User $entity)
    {
        static::assertInternalType('integer', $entity->id);
        static::assertInternalType('string', $entity->name);
        static::assertInstanceOf(DateTime::class, $entity->dateCreated);
        static::assertInternalType('integer', $entity->groupId);
    }
}
