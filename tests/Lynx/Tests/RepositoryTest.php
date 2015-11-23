<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

use Model\User;

class RepositoryTest extends TestCase
{
    public function testGetOneMethodForUser()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository('Model\User');

        /** @var User $result */
        $result = $repository->getOne(1);

        static::assertInstanceOf(\Model\User::class, $result);
        static::assertInternalType('integer', $result->id);
        static::assertInternalType('string', $result->name);
        static::assertInstanceOf(\DateTime::class, $result->dateCreated);
        static::assertInternalType('integer', $result->group_id);
    }
}
