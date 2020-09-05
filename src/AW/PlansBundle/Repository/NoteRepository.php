<?php

namespace AW\PlansBundle\Repository;

use AW\PlansBundle\Entity\Commande;

class NoteRepository extends \Doctrine\ORM\EntityRepository
{
  public function getLast(Commande $commande, $lastId, $private)
  {
    $qb = $this->createQueryBuilder('n')
      ->where('n.commande = :commandeid')
        ->setParameter('commandeid', $commande->getId())
      ->andWhere('n.id > :lastid')
        ->setParameter('lastid', $lastId)
    ;

    if($private){
      $qb->andWhere('n.private = true');
    }else{
      $qb->andWhere('n.private = false');
    }

    $query = $qb
      ->orderBy('n.date')
      ->getQuery()
    ;

    return $query
      ->getResult()
    ;
  }

  public function getListDeadlineNotSeen()
  {
    $now = new \DateTime();

    return $this->createQueryBuilder('n')
      ->leftJoin('n.commande', 'c')
        ->addSelect('c')
      ->where('n.deadline < :now')
        ->setParameter('now', $now)
      ->andWhere('n.seen = 0')
      ->getQuery()
      ->getResult()
    ;
  }

  public function getPublicListNotSeen()
  {
    return $this->createQueryBuilder('n')
      ->leftJoin('n.commande', 'c')
        ->addSelect('c')
      ->where('n.private = false')
      ->andWhere('n.seen = 0')
      ->getQuery()
      ->getResult()
    ;
  }
}
