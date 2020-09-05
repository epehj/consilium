<?php

namespace AW\UserBundle\Repository;

class UserParamRepository extends \Doctrine\ORM\EntityRepository
{
  public function findWithDataTablesState()
  {
    return $this
      ->createQueryBuilder('u')
      ->where('u.plansDataTablesState IS NOT NULL')
      ->getQuery()
      ->getResult()
    ;
  }
}
