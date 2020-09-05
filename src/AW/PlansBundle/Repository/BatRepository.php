<?php

namespace AW\PlansBundle\Repository;

use AW\PlansBundle\Entity\Commande;

class BatRepository extends \Doctrine\ORM\EntityRepository
{
  public function getCurrent(Commande $commande)
  {
    return $this
      ->createQueryBuilder('b')
      ->leftJoin('b.commande', 'c')
        ->addSelect('c')
      ->where('b.commande = :commande')
        ->setParameter('commande', $commande)
      ->andWhere('b.dateModification IS NULL')
      ->andWhere('b.dateValidation IS NULL')
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function getValidated(Commande $commande)
  {
    return $this
      ->createQueryBuilder('b')
      ->leftJoin('b.commande', 'c')
        ->addSelect('c')
      ->where('b.commande = :commande')
        ->setParameter('commande', $commande)
      ->andWhere('b.dateModification IS NULL')
      ->andWhere('b.dateValidation IS NOT NULL')
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }
}
