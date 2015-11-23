<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use BlaBla\DAO\Type\Hstore;
use BlaBla\DAO\Type\IntegerArray;
use BlaBla\DAO\Type\Point;
use Doctrine\ORM\Mapping\ClassMetadata;

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

        $metaData = $this->em->getMetadataFactory()
            ->getMetadataFor($className);

        $queryResult = $this->em->createQueryBuilder()
            ->select('*')
            ->from($metaData->table['name'])
            ->where('id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        if (!$queryResult) {
            return null;
        }

        $entity = $this->em->getObject($className);
        return $this->hydrate($entity, $queryResult, $metaData);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $className = $this->name;

        $metaData = $this->em->getMetadataFactory()
            ->getMetadataFor($className);

        $qb = $this->em->createQueryBuilder()
            ->select('*')
            ->from($metaData->table['name']);

        if ($orderBy) {
            foreach ($orderBy as $field => $type) {
                $qb->orderBy($field, $type);
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
     * @param object $entity
     * @param array $queryResult
     * @param ClassMetadata $metaData
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function hydrate($entity, $queryResult, ClassMetadata $metaData)
    {
        foreach ($queryResult as $key => $value) {
            switch ($metaData->getTypeOfField($key)) {
                case 'float':
                    $entity->{$key} = (float) $value;
                    break;
                case 'hstore':
                    $entity->{$key} = new Hstore($value);
                    break;
                case 'integer[]':
                    $entity->{$key} = new IntegerArray($value);
                    break;
                default:
                    $fieldName = $metaData->fieldNames[$key];
                    $type = $metaData->getTypeOfField($fieldName);

                    if (\Doctrine\DBAL\Types\Type::hasType($type)) {
                        $type = \Doctrine\DBAL\Types\Type::getType($type);
                        $entity->{$fieldName} = $type->convertToPHPValue($value, $this->em->getConnection()->getDatabasePlatform());
                    } else {
                        throw new \RuntimeException('Unsupported type: '. $type . ' for ' . $key);
//                        $entity->{$key} = $value;
                    }
                    break;
            }
        }

        return $entity;
    }
}
