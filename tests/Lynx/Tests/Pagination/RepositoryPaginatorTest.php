<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Tests;

class RepositoryPaginatorTest extends TestCase
{
    public function testSimpleFetch()
    {
        $result = $this->em->getRepository('Model\User');
        static::assertInstanceOf(\Lynx\Repository::class, $result);

        $users = $result->getPagination(1000);
        $count = 0;

        foreach ($users as $user) {
            $count++;
        }

        static::assertEquals($result->count(), $users->getTotal());
        static::assertEquals($result->count(), $count);
    }

    public function testSimpleFetchWithOnePerPage()
    {
        $result = $this->em->getRepository('Model\User');
        static::assertInstanceOf(\Lynx\Repository::class, $result);

        $users = $result->getPagination(1);
        $count = 0;
        $page = 1;

        foreach ($users as $user) {
            static::assertEquals($page, $users->getPage());

            $count++;
            $page++;
        }

        $totalCount = $result->count();
        static::assertEquals($totalCount, $users->getTotal());
        static::assertEquals($totalCount, $count);
    }

    public function testSimpleFetchWithTwoPerPage()
    {
        $result = $this->em->getRepository('Model\User');
        static::assertInstanceOf(\Lynx\Repository::class, $result);

        $users = $result->getPagination(2);
        $count = 0;
        $page = 1;

        foreach ($users as $key => $user) {
            static::assertEquals($page, $users->getPage());

            $count++;
            if ($key % 2) {
                $page++;
            }
        }

        $totalCount = $result->count();
        static::assertEquals($totalCount, $users->getTotal());
        static::assertEquals($totalCount, $count);
    }

    public function testSimpleFetchAllPerOneLimit()
    {
        $result = $this->em->getRepository('Model\User');
        static::assertInstanceOf(\Lynx\Repository::class, $result);

        $totalCount = $result->count();
        $users = $result->getPagination();
        $count = 0;

        foreach ($users as $key => $user) {
            static::assertEquals(1, $users->getPage());

            $count++;
        }

        static::assertEquals($totalCount, $users->getTotal());
        static::assertEquals($totalCount, $count);
    }
}
