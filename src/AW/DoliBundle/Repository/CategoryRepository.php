<?php

namespace AW\DoliBundle\Repository;

class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
  public function getRootAvailableOnline()
  {
    return $this
      ->createQueryBuilder('c')
      ->leftJoin('c.extrafields', 'e')
        ->addSelect('e')
      ->where('e.availableOnline = true')
      ->andWhere('c.type = 0')
      ->getQuery()
      ->getResult()
    ;
  }
}
