<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

use DateTime;
use Model\User;

class RepositoryTest extends TestCase
{
    public function testGetOneMethodSuccessForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);

        /** @var User $result */
        $userOne = $repository->findOne(1);
        static::assertSuccessUser($userOne);
        static::assertSame(1, $userOne->id);

        /** @var User $result */
        $userTwo = $repository->findOne(2);
        static::assertSuccessUser($userTwo);
        static::assertSame(2, $userTwo->id);
    }

    public function testGetOneMethodNotFoundForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        static::assertNull($repository->findOne(100000000));
    }

    public function testCountMethodForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        $result = $repository->count();

        static::assertInternalType('integer', $result);
        static::assertTrue($result > 0);
    }

    public function testRefreshMethodForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);

        /** @var User $entity */
        $entity = $repository->findOne(1);
        self::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        $splHash = spl_object_hash($entity);

        /** @var User $entity */
        $entity = $repository->refresh($entity);
        self::assertSuccessUser($entity);
        static::assertSame(1, $entity->id);
        static::assertSame($splHash, spl_object_hash($entity));
    }

    public function testFindByWithoutParametersForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        /** @var User[] $result */
        $result = $repository->findBy([]);

        foreach ($result as $user) {
            self::assertSuccessUser($user);
        }
    }
}
