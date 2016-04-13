<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Pagination;

use Doctrine\DBAL\Query\QueryBuilder;
use Iterator;
use Lynx\Repository;

class RepositoryPaginator implements Iterator
{
    /**
     * @var integer
     */
    protected $total;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    protected $results;

    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * @var integer
     */
    protected $page = 1;

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(QueryBuilder $queryBuilder, Repository $repository, $limit = 100)
    {
        $this->limit = $limit;
        $this->queryBuilder = $queryBuilder;
        $this->repository = $repository;

        /**
         * First, We need to check the number of total rows
         */
        if ($this->getTotal()) {
            $this->fetchPageResults();
        }
    }

    /**
     * @return bool|string
     */
    public function getTotal()
    {
        if ($this->total) {
            return $this->total;
        }

        $queryBuilder = $this->repository->getEm()->createQueryBuilder();

        $fromParts = $this->queryBuilder->getQueryPart('from');
        if ($fromParts) {
            foreach ($fromParts as $from) {
                $queryBuilder->from($from['table'], $from['alias']);
            }
        }
        
        $queryBuilder->select('COUNT(*)');

        $where = $this->queryBuilder->getQueryPart('where');
        if ($where) {
            $queryBuilder->add('where', (string) $where);
        }

        $parameters = $this->queryBuilder->getParameters();
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $queryBuilder->setParameter($key, $value);
            }
        }

        return $this->total = $queryBuilder
            ->execute()
            ->fetchColumn();
    }

    protected function fetchPageResults()
    {
        $this->results = $this->repository->findByQueryBuilder(
            $this->queryBuilder->setMaxResults($this->limit)
                ->setFirstResult(($this->page - 1) * $this->limit)
        );
    }

    public function current()
    {
        return $this->valid() ? current($this->results) : null;
    }

    public function next()
    {
        next($this->results);
        $this->position++;
    }

    public function key()
    {
        key($this->results);
        return $this->position;
    }

    public function valid()
    {
        if (count($this->results) >= $this->position + 1) {
            return true;
        }

        if ($this->position + 1 < $this->total) {
            if (($this->page - 1) * $this->limit <= $this->total) {
                $this->page++;
                $this->position = 0;

                $this->fetchPageResults();
                if ($this->results) {
                    return true;
                }
            }
        }

        return false;
    }

    public function rewind()
    {
        $this->page = 1;
        $this->position = 0;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
}
