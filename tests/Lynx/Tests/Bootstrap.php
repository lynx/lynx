<?php

namespace Lynx\Tests;

include_once __DIR__ . '/../../../vendor/autoload.php';

include_once __DIR__ . '/TestCase.php';
include_once __DIR__ . '/Utils.php';

include_once __DIR__ . '/Models/User.php';
include_once __DIR__ . '/Models/Product.php';
include_once __DIR__ . '/Models/Group.php';

class_exists('Doctrine\ORM\Mapping\Entity', true);
class_exists('Doctrine\ORM\Mapping\Column', true);
class_exists('Doctrine\ORM\Mapping\Id', true);
class_exists('Doctrine\ORM\Mapping\Table', true);
