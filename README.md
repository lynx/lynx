Lynx
====
[![Build Status](https://travis-ci.org/lynx/lynx.svg?branch=master)](https://travis-ci.org/lynx/lynx)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lynx/lynx/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/lynx/lynx/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/lynx/lynx/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/lynx/lynx/?branch=master)

> An awesome Mapper on top of Doctrine 2 components

## How to work?

First you need to setup EntityManager ($em) :

```php
$configuration = new Configuration();
$configuration->setResultCacheImpl($di->getCache());
$configuration->setMetadataDriverImpl(
    new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
        new AnnotationReader(),
        realpath(APP_ROOT_PATH . '/src/BlaBla/User/Model/')
    )
);
$configuration->setMetadataCacheImpl(
    new \Doctrine\Common\Cache\ApcCache()
);

$em = new \Lynx\EntityManager(
    $di->getDb(),
    $configuration
);
```

### Working with repository

You can get a `Repository` for `Model` by using method `getRepository` from `EntityManager`:

```php
$repository = $em->getRepository(User::class);
```

For example, you can get one row by using:

```php
$repository = $em->getRepository(User::class);
/** User|null $user */
$user = $repository->findOne(1);
```

## Testing

#### PostgresSQL

```
sudo docker run --name lynx-test -p 5432:5432 -e POSTGRES_PASSWORD= -d postgres
psql -p 5432 -h 127.0.0.1 -U postgres -c 'create database lynx_test;'
psql -p 5432 -h 127.0.0.1 -U postgres -d lynx_test -f tests/schemas/pqsql/lynx_test.sql
```

## LICENSE

MIT
