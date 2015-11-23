<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

class RepositoryTest extends TestCase
{
    public function testGetOneMethodForUser()
    {
        /** @var \Lynx\Repository $repository */
        $repository = $this->em->getRepository('Model\User');
        $result = $repository->getOne(1);

        static::assertInstanceOf(\Model\User::class, $result);
        static::assertInternalType('string', $result->firstname);
    }
}
