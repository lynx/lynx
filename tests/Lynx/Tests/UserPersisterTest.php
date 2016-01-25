<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

use DateTime;
use Model\User;

class UserPersisterTest extends TestCase
{
    /**
     * @return \Lynx\CachedRepository
     */
    protected function getRepository()
    {
        return new \Lynx\CachedRepository($this->em, User::class);
    }

    public function testNameUpdate()
    {
        $repository = $this->getRepository();

        $user = $repository->findOne(1);

        $user->name .= mt_rand(0, PHP_INT_MAX);

        $repository->getPersister()->update($user);
    }
}
