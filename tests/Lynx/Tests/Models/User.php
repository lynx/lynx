<?php

/**
 * @author Patsura Dmitry <zaets28rus@gmail.com>
 */

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="users"
 * );
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id")
     */
    public $id;

    /**
     * @ORM\Column(name="date_created", type="date")
     */
    public $dateCreated;

    /**
     * @ORM\Column(length=50, name="name")
     */
    public $name;

    /**
     * @ORM\Column(type="integer", length=11, name="group_id")
     */
    public $group_id;
}
