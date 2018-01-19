<?php
/**
 * Created by PhpStorm.
 * User: aleksandr
 * Date: 12.01.18
 * Time: 16:08
 */

namespace CTOBundle\Repository;


use Doctrine\ORM\EntityManager;

interface NewsAPI
{
    public function createFromNewsApi(array $param);
    public function existByName($name);
}