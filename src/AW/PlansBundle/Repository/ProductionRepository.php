<?php

namespace AW\PlansBundle\Repository;

use AW\PlansBundle\Entity\Commande;
use AW\DoliBundle\Entity\User;

class ProductionRepository extends \Doctrine\ORM\EntityRepository
{
  public function getCurrent(Commande $commande)
  {
    return $this
      ->createQueryBuilder('p')
      ->leftJoin('p.commande', 'c')
        ->addSelect('c')
      ->where('p.commande = :commande')
        ->setParameter('commande', $commande)
      ->andWhere('p.dateEnd IS NULL')
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function getLast(Commande $commande)
  {
    return $this
      ->createQueryBuilder('p')
      ->leftJoin('p.commande', 'c')
        ->addSelect('c')
      ->where('p.commande = :commande')
        ->setParameter('commande', $commande)
      ->orderBy('p.dateStart', 'DESC')
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function getUserOngoing(User $user)
  {
    return $this
      ->createQueryBuilder('p')
      ->leftJoin('p.user', 'u')
        ->select('COUNT(u)')
      ->where('u = :user')
        ->setParameter('user', $user)
      ->andWhere('p.dateEnd IS NULL')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function getByUserAndStatusBetweenDateQueryBuilder(User $user, $status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->createQueryBuilder('p')
      ->leftJoin('p.commande', 'c')
        ->where('p.dateStart BETWEEN :start AND :end')
          ->setParameter('start', $start)
          ->setParameter('end', $end)
      ->leftJoin('p.user', 'u')
        ->andWhere('u = :user')
          ->setParameter('user', $user)
      ->andWhere('c.status != :canceledStatus')
        ->setParameter('canceledStatus', Commande::STATUS_CANCELED)
      ->andWhere('p.status = :status')
        ->setParameter('status', $status)
    ;
  }

  public function countByUserAndStatusBetweenDate(User $user, $status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->getByUserAndStatusBetweenDateQueryBuilder($user, $status, $start, $end)
        ->select('COUNT(c)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function sumByUserAndStatusBetweenDate(User $user, $status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->getByUserAndStatusBetweenDateQueryBuilder($user, $status, $start, $end)
      ->leftJoin('c.listDet', 'd')
        ->select('SUM(d.qty)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }
}
