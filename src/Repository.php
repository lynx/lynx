<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

class Repository
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $name;

    public function __construct(EntityManager $em, $name)
    {
        $this->em = $em;
        $this->name = $name;
    }

    /**
     * @param $id
     * @return object|null
     */
    public function getOne($id)
    {
        $className = $this->name;

        $queryResult = $this->em->createQueryBuilder()
            ->select('*')
            ->from($className::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (!$queryResult) {
            return null;
        }

        $entity = $this->em->getObject($className);

        foreach ($queryResult as $key => $value) {
            $entity->{$key} = $value;
        }

        return $entity;
    }
}
