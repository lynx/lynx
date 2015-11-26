<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

class CachedRepository extends Repository
{
    /**
     * @param integer $id
     * @return object|null
     */
    public function getOne($id)
    {
        $cache = $this->em->getCache();
        $cacheKey = $this->className . $id;

        if ($cache->contains($cacheKey)) {
            return $cache->fetch($cacheKey);
        }

        $result = parent::getOne($id);
        if ($result) {
            $cache->save($cacheKey, $result, 60*10);
        }

        return $result;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param integer|null $limit
     * @param integer|null $offset
     * @return array|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $cache = $this->em->getCache();
        $cacheKey = $this->className . '-';

        if ($criteria) {
            $cacheKey .= implode(',', $criteria);
        }

        if ($orderBy) {
            $cacheKey .= implode(',', $orderBy);
        }

        if ($limit) {
            $cacheKey .= $limit;
        }

        if ($offset) {
            $cacheKey .= $offset;
        }

        if ($cache->contains($cacheKey)) {
            return $cache->fetch($cacheKey);
        }

        $result = parent::findBy($criteria, $orderBy, $limit, $offset);
        if ($result) {
            $cache->save($cacheKey, $result, 60*10);
        }

        return $result;
    }
}
