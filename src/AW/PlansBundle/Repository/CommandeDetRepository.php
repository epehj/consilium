<?php

namespace AW\PlansBundle\Repository;

class CommandeDetRepository extends \Doctrine\ORM\EntityRepository
{
  public function findWithCommande($id)
  {
    return $this
      ->createQueryBuilder('d')
      ->leftJoin('d.commande', 'c')
        ->addSelect('c')
      ->where('c.id = :id')
        ->setParameter('id', $id)
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }
}
