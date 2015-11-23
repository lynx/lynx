<?php

/**
 * @author Patsura Dmitry <zaets28rus@gmail.com>
 */

namespace Lynx\Tests;

use Lynx\DBAL;
use Lynx\Stdlib\Events\Manager;

class Utils
{
    public static function getConnection()
    {
        $eventsManager = new Manager();

        switch ($GLOBALS['db_type']) {
            case 'mysql':
            case 'pdo_mysql':
                //@todo
                break;
            case 'pgsql':
            case 'pdo_pgsql':
                //@todo
                break;
            default:
                throw new \InvalidArgumentException('Unsupported db type : ' . $GLOBALS['db_type']);
            break;
        }

        $connection = new DBAL\Connection(['driver' => $driver], $eventsManager);

        return $connection;
    }
}
