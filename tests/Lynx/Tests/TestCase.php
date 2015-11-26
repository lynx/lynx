<?php

/**
 * @author Patsura Dmitry <zaets28rus@gmail.com>
 */

namespace Lynx\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Configuration;
use Lynx\EntityManager;

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
                'host' => 'localhost',
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
}
