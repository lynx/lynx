<?php

/**
 * @author Patsura Dmitry <zaets28rus@gmail.com>
 */

namespace Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

/**
 * @entity
 * @table(name="groups")
 */
class Group
{
    /**
     * @Id
     * @Column(type="integer", length=11, name="id")
     */
    public $id;

    /**
     * @Column(length=50, , name="name")
     */
    public $name;
}
