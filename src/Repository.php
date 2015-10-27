<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx;

use BlaBla\DAO\Type\Hstore;
use BlaBla\DAO\Type\IntegerArray;
use BlaBla\DAO\Type\Point;

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

        foreach ($queryResult as $key => $value) {
            switch ($metaData->getTypeOfField($key)) {
                case 'integer':
                    $entity->{$key} = (int) $value;
                    break;
                case 'float':
                    $entity->{$key} = (float) $value;
                    break;
                case 'hstore':
                    $entity->{$key} = new Hstore($value);
                    break;
                case 'integer[]':
                    $entity->{$key} = new IntegerArray($value);
                    break;
                case 'point':
                    $type = \Doctrine\DBAL\Types\Type::getType('geometry');
                    $entity->{$key} = $type->convertToPHPValue($value, $this->em->getConnection()->getDatabasePlatform());
                    break;
                case 'composite':
                    $type = '\BlaBla\DAO\Type\\' . $metaData->fieldMappings[$key]['columnName'];
                    $entity->{$key} = new $type($value);
                    break;
                default:
                    $entity->{$key} = $value;
                    break;
            }
        }

        return $entity;
    }
}
