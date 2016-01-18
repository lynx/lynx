<?php
/**
 * @author: Patsura Dmitry http://github.com/ovr <talk@dmtry.me>
 */

namespace Lynx\Persister;

use Lynx\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class DBAL
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    private $metaData;

    public function __construct(EntityManager $em, ClassMetadata $metadata)
    {
        $this->em = $em;
        $this->metaData = $metadata;
    }

    /**
     * @param $entity
     * @return array
     */
    protected function prepareSet($entity)
    {
        $data = [];

        foreach ($this->metaData->getFieldNames() as $fieldName) {
            $columnName = $this->metaData->getColumnName($fieldName);
            $type = $this->metaData->getTypeOfField($fieldName);
            $fieldValue = $entity->{$fieldName};
            $type = \Doctrine\DBAL\Types\Type::getType($type);

            $value = $type->convertToDatabaseValue(
                $fieldValue,
                $this->em->getConnection()->getDatabasePlatform()
            );

            var_dump($value);
//            die();
            $data[$columnName] = $fieldValue;
        }
        die();

        return $data;
    }

    /**
     * It's needed when you need to update the field of specified entity without getting it
     *
     * @param string $column
     * @param mixed $value
     * @param mixed|integer $id
     * @param integer|null $type
     * @return int
     */
    public function updateFieldById($column, $value, $id, $type = null)
    {
        $types = [];

        if ($type) {
            $types[$column] = $type;
        }

        return $this->em->getConnection()->update(
            $this->metaData->getTableName(),
            [
                $column => $value
            ],
            [
                'id' => $id
            ],
            $types
        );
    }

    /**
     * @param object $entity
     * @return int
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function update($entity)
    {
        $identifiers = [];

        foreach ($this->metaData->getIdentifierColumnNames() as $columnName) {
            $value = $this->metaData->getFieldValue(
                $entity,
                $this->metaData->getFieldForColumn($columnName)
            );

            $identifiers[$columnName] = $value;
        }

        $updateSet = $this->prepareSet($entity);

        return $this->em->getConnection()->update(
            $this->metaData->getTableName(),
            $updateSet,
            $identifiers
        );
    }
}
