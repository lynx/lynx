<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use Doctrine\DBAL\Query\QueryBuilder;
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
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('*')
            ->from($this->metaData->getTableName())
            ->where('id = :id')
            ->setParameter('id', $id);

        return $this->findOneByQueryBuilder($queryBuilder);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param integer|null $limit
     * @param integer|null $offset
     * @return null|object
     */
    public function findOneBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->em->createQueryBuilder()
            ->select('*')
            ->from($this->metaData->getTableName());

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

        $qb->setMaxResults($limit)
            ->setFirstResult($offset);

        return $this->findOneByQueryBuilder($qb);
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
        $qb = $this->em->createQueryBuilder()
            ->select('*')
            ->from($this->metaData->getTableName());

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
            ->setFirstResult($offset)
            ->execute()
            ->fetchAll();

        if (!$queryResult) {
            return null;
        }

        $result = [];

        $entity = $this->em->getObject($this->className);

        foreach ($queryResult as $row) {
            $result[] = $this->hydrate(clone $entity, $row);
        }

        return $result;
    }

    /**
     * @param object $entity
     * @return object
     */
    public function refresh($entity)
    {
        $column = $this->metaData->getIdentifier()[0];

        $queryResult = $this->em->createQueryBuilder()
            ->select('*')
            ->from($this->metaData->getTableName())
            ->where($column . ' = :id')
            ->setParameter('id', $entity->{$column})
            ->execute()
            ->fetch();

        if (!$queryResult) {
            throw new RuntimeException('Cannot refresh entity with ' . $column . ' = ' . $entity->{$column});
        }

        return $this->hydrate($entity, $queryResult);
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
     * @param QueryBuilder $queryBuilder
     * @return null|object
     */
    public function findOneByQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryResult = $queryBuilder
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (!$queryResult) {
            return null;
        }

        $entity = $this->em->getObject($this->className);
        return $this->hydrate($entity, $queryResult);
    }

    /**
     * @param object $entity
     * @param array $queryResult
     * @return object
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function hydrate($entity, $queryResult)
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();

        foreach ($queryResult as $columnName => $value) {
            $fieldName = $this->metaData->getFieldForColumn($columnName);
            $typeForField = $this->metaData->getTypeOfField($fieldName);

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
