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
        $userOne = $repository->getOne(1);
        static::assertSuccessUser($userOne);
        static::assertSame(1, $userOne->id);

        /** @var User $result */
        $userTwo = $repository->getOne(2);
        static::assertSuccessUser($userTwo);
        static::assertSame(2, $userTwo->id);
    }

    public function testGetOneMethodNotFoundForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        static::assertNull($repository->getOne(100000000));
    }

    public function testCountMethodForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        $result = $repository->count();

        static::assertInternalType('integer', $result);
        static::assertTrue($result > 0);
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
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        /** @var User[] $result */
        $result = $repository->findBy([]);

        foreach ($result as $user) {
            static::assertSuccessUser($user);
        }
    }
}
