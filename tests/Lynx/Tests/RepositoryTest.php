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
        $result = $repository->getOne(1);

        static::assertInstanceOf(User::class, $result);
        static::assertInternalType('integer', $result->id);
        static::assertInternalType('string', $result->name);
        static::assertInstanceOf(DateTime::class, $result->dateCreated);
        static::assertInternalType('integer', $result->groupId);
    }

    public function testGetOneMethodNotFoundForUserEntity()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository(User::class);
        static::assertNull($repository->getOne(100000000));
    }
}
