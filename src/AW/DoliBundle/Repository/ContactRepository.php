<?php

namespace AW\DoliBundle\Repository;

use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Entity\Commande;
use AW\DoliBundle\Entity\ContactLink;

class ContactRepository extends \Doctrine\ORM\EntityRepository
{
  public function getSocieteContactsQueryBuilder(Societe $societe)
  {
    $qb = $this
      ->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
    ;

    if($societe->getParent() === null){
      $qb
        ->where('s = :societe')
        ->setParameter('societe', $societe)
      ;
    }else{
      $qb
        ->where($qb->expr()->orX(
          $qb->expr()->eq('s', ':societe'),
          $qb->expr()->eq('s', ':parent')
        ))
        ->setParameter('societe', $societe)
        ->setParameter('parent', $societe->getParent())
      ;
    }

    $qb
      ->andWhere('c.status = 1')
      ->orderBy('c.firstname')
    ;

    return $qb;
  }

  public function getSocieteShippingContactsQueryBuilder(Societe $societe)
  {
    return $this
      ->getSocieteContactsQueryBuilder($societe)
      ->andWhere('c.status = true')
      ->andWhere("c.address != ''")
      ->andWhere('c.address IS NOT NULL')
      ->andWhere("c.zip != ''")
      ->andWhere('c.zip IS NOT NULL')
      ->andWhere("c.town != ''")
      ->andWhere('c.town IS NOT NULL')
      ->andWhere('c.country IS NOT NULL')
    ;
  }

  public function getSocieteShippingContacts(Societe $societe)
  {
    return $this
      ->getSocieteShippingContactsQueryBuilder($societe)
      ->getQuery()
      ->getResult()
    ;
  }

  public function getCommandeContactShipping(Commande $commande)
  {
    return $this
      ->createQueryBuilder('c')
      ->leftJoin('c.links', 'l')
      ->where('l.typeContact = :type')
        ->setParameter('type', ContactLink::TYPE_COMMANDE_SHIPPING)
      ->andWhere('l.elementId = :elementid')
        ->setParameter('elementid', $commande->getId())
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }
}
