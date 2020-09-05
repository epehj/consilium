<?php

namespace AW\DoliBundle\Repository;

use AW\DoliBundle\Entity\User;

class SocieteRepository extends \Doctrine\ORM\EntityRepository
{
  public function getClientsQueryBuilder(User $user)
  {
    $qb = $this->createQueryBuilder('s');
    $qb
      ->leftJoin('s.infosPlans', 'i')
        ->addSelect('i')
      ->where($qb->expr()->in('s.client', array(1, 3)))
      ->andWhere('s.status = 1')
      ->orderBy('s.name')
    ;

    if($user->getSociete()){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->eq('s', ':societe'),
          $qb->expr()->eq('s.parent', ':societe')
        ))
        ->setParameter('societe', $user->getSociete())
      ;
    }

    return $qb;
  }
}
