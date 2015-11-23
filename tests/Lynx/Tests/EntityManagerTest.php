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
        $this->assertInstanceOf('Lynx\\ORM\\EntityRepository', $result);
    }
}
