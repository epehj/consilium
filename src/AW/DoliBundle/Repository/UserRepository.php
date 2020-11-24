<?php

namespace AW\DoliBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use AW\DoliBundle\Entity\Societe;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
  public function loadUserByUsername($username)
  {
    return $this
      ->createQueryBuilder('u')
      ->leftJoin('u.groups', 'g')
        ->addSelect('g')
      ->leftJoin('u.societe', 's')
        ->addSelect('s')
      ->leftJoin('u.param', 'p')
        ->addSelect('p')
      ->leftJoin('u.rightsDef', 'r')
        ->addSelect('r')
      ->leftJoin('g.rightsDef', 'rg')
        ->addSelect('rg')
      ->where('u.login = :username')
        ->setParameter('username', $username)
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function getSocieteUsersQueryBuilder(Societe $societe)
  {
    $qb = $this
      ->createQueryBuilder('u')
      ->leftJoin('u.param', 'p')
        ->addSelect('p')
      ->leftJoin('u.societe', 's')
        ->addSelect('s')
      ->leftJoin('s.infosPlans', 'i')
        ->addSelect('i')
    ;

    if($societe->getParent() === null){
      $qb
        ->where('u.societe = :societe')
        ->setParameter('societe', $societe)
      ;
    }else{
      $qb
        ->where($qb->expr()->orX(
          $qb->expr()->eq('u.societe', ':societe'),
          $qb->expr()->eq('u.societe', ':parent')
        ))
        ->setParameter('societe', $societe)
        ->setParameter('parent', $societe->getParent())
      ;
    }

    $qb
      ->andWhere('u.status = 1')
      ->orderBy('u.firstname')
    ;

    return $qb;
  }

  public function getSocieteUsers(Societe $societe=null)
  {
    if($societe === null){
      $qb = $this
        ->createQueryBuilder('u')
        ->leftJoin('u.param', 'p')
          ->addSelect('p')
        ->leftJoin('u.societe', 's')
          ->addSelect('s')
        ->leftJoin('s.infosPlans', 'i')
          ->addSelect('i')
        ->where('u.societe IS NOT NULL')
        ->orderBy('s.name')
        ->addOrderBy('u.firstname')
      ;
    }else{
      $qb = $this->getSocieteUsersQueryBuilder($societe);
    }

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getActive()
  {
    return $this
      ->createQueryBuilder('u')
      ->leftJoin('u.param', 'p')
        ->addSelect('p')
      ->leftJoin('u.societe', 's')
        ->addSelect('s')
      ->leftJoin('s.infosPlans', 'i')
        ->addSelect('i')
      ->where('u.status = 1')
      ->getQuery()
      ->getResult()
    ;
  }

  public function getJMOUnderResponsibilityQueryBuilder()
  {
    return $this
      ->createQueryBuilder('u')
      ->leftJoin('u.param', 'p')
        ->addSelect('p')
      ->leftJoin('u.manager', 'm')
        ->addSelect('m')
      ->where('u.societe IS NULL')
      ->andWhere('u.status = 1')
      ->andWhere('m.id = 2')
    ;
  }

    public function getUsersInReleveurGroupQueryBuilder()
    {
        return $this
            ->createQueryBuilder('u')
            ->leftJoin('u.groups', 'g')
            ->where('g.name = :releveurs')
            ->setParameter('releveurs', 'Releveur') // FIXME virer la partie en dur et faire �a via un class::name
            ;
    }
}
