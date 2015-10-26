<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

class CachedRepository extends Repository
{
    /**
     * @param $id
     * @return object|null
     */
    public function getOne($id)
    {
        $cache = $this->em->getCache();
        $cacheKey = $this->name . $id;

        if ($cache->contains($cacheKey)) {
            return $cache->fetch($cacheKey);
        }

        $result = parent::getOne($id);
        if ($result) {
            $cache->save($cacheKey, $result, 60*10);
        }

        return $result;
    }
}
