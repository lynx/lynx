<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use RuntimeException;

class Repository
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var ClassMetadata
     */
    protected $metaData;

    public function __construct(EntityManager $em, $className)
    {
        $this->em = $em;
        $this->className = $className;

        $this->metaData = $this->em
            ->getMetadataFactory()
            ->getMetadataFor($this->className);
    }

    /**
     * @param $id
     * @return object|null
     */
    public function getOne($id)
    {
        $queryResult = $this->em->createQueryBuilder()
            ->select('*')
            ->from($this->metaData->getTableName())
            ->where('id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (!$queryResult) {
            return null;
        }

        $entity = $this->em->getObject($this->className);
        return $this->hydrate($entity, $queryResult, $this->metaData);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $className = $this->className;

        /** @var ClassMetadata $metaData */
        $metaData = $this->em->getMetadataFactory()
            ->getMetadataFor($className);

        $qb = $this->em->createQueryBuilder()
            ->select('*')
            ->from($metaData->getTableName());

        if ($criteria) {
            foreach ($criteria as $field => $type) {
                $qb->andWhere($field, $type);
            }
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $type) {
                $qb->addOrderBy($field, $type);
            }
        }

        $queryResult = $qb->setMaxResults($limit)
            ->execute()
            ->fetchAll();

        if (!$queryResult) {
            return null;
        }

        $result = [];

        $entity = $this->em->getObject($className);

        foreach ($queryResult as $row) {
            $result[] = $this->hydrate(clone $entity, $row, $metaData);
        }

        return $result;
    }

    /**
     * @return integer
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function count()
    {
        $qb = $this->em->createQueryBuilder()
            ->select('count(*)')
            ->from($this->metaData->getTableName());

        $queryResult = $qb->setMaxResults(1)
            ->execute()
            ->fetchColumn();

        return (int) $queryResult;
    }

    /**
     * @param object $entity
     * @param array $queryResult
     * @param ClassMetadata $metaData
     * @return object
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function hydrate($entity, $queryResult, ClassMetadata $metaData)
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();

        foreach ($queryResult as $columnName => $value) {
            $fieldName = $metaData->getFieldForColumn($columnName);
            $typeForField = $metaData->getTypeOfField($fieldName);

            if ($typeForField) {
                $type = Type::getType($typeForField);
                $entity->{$fieldName} = $type->convertToPHPValue($value, $platform);
            } else {
                throw new RuntimeException('Unknown type for field ' . $fieldName);
            }
        }

        return $entity;
    }
}
