<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

class EntityManagerTest extends TestCase
{
    public function testGetRepositorySuccess()
    {
        $result = $this->em->getRepository('Model\User');
        static::assertInstanceOf(\Lynx\Repository::class, $result);
    }
}
