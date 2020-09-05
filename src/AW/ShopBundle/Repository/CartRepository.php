<?php

namespace AW\ShopBundle\Repository;

use AW\DoliBundle\Entity\User;

class CartRepository extends \Doctrine\ORM\EntityRepository
{
  public function getUserCart(User $user)
  {
    return $this
      ->createQueryBuilder('c')
      ->leftJoin('c.product', 'p')
        ->addSelect('p')
      ->leftJoin('c.user', 'u')
        ->addSelect('u')
      ->where('c.user = :user')
        ->setParameter('user', $user)
      ->getQuery()
      ->getResult()
    ;
  }
}
