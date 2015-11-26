<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

use DateTime;
use Model\User;

class CachedRepositoryTest extends TestCase
{
    /**
     * @return \Lynx\CachedRepository
     */
    protected function getRepository()
    {
        return new \Lynx\CachedRepository($this->em, User::class);
    }

    public function testGetOneMethodSuccessForUserEntity()
    {
        $repository = $this->getRepository();

        /** @var User $result */
        $userOne = $repository->getOne(1);
        static::assertSuccessUser($userOne);
        static::assertSame(1, $userOne->id);

        for ($i = 0; $i < 5; $i++) {
            /** @var User $result */
            $userOne = $repository->getOne(1);
            static::assertSuccessUser($userOne);
            static::assertSame(1, $userOne->id);
        }
    }

    public function testGetOneMethodNotFoundForUserEntity()
    {
        $repository = $this->getRepository();
        static::assertNull($repository->getOne(100000000));
        static::assertNull($repository->getOne(100000000));
    }

    public function testCountMethodForUserEntity()
    {
        $repository = $this->getRepository();

        $result = $repository->count();
        static::assertInternalType('integer', $result);
        static::assertTrue($result > 0);

        $result = $repository->count();
        static::assertInternalType('integer', $result);
        static::assertTrue($result > 0);
    }

    public function testRefreshMethodForUserEntity()
    {
        $repository = $this->getRepository();

        $entity = $repository->getOne(1);
        static::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        $splHash = spl_object_hash($entity);

        $entity = $repository->refresh($entity);
        static::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        static::assertSame($splHash, spl_object_hash($entity));


        $entity = $repository->getOne(1);
        static::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        $splHash = spl_object_hash($entity);

        $entity = $repository->refresh($entity);
        static::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        static::assertSame($splHash, spl_object_hash($entity));
    }

    protected static function assertSuccessUser($result)
    {
        static::assertInstanceOf(User::class, $result);
        static::assertInternalType('integer', $result->id);
        static::assertInternalType('string', $result->name);
        static::assertInstanceOf(DateTime::class, $result->dateCreated);
        static::assertInternalType('integer', $result->groupId);
    }

    public function testFindByWithoutParametersForUserEntity()
    {
        $repository = $this->getRepository();

        /** @var User[] $result */
        $result = $repository->findBy([]);

        foreach ($result as $user) {
            static::assertSuccessUser($user);
        }

        /** @var User[] $result */
        $result = $repository->findBy([]);

        foreach ($result as $user) {
            static::assertSuccessUser($user);
        }
    }
}