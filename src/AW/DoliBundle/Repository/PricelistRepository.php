<?php

namespace AW\DoliBundle\Repository;

use AW\DoliBundle\Entity\Product;
use AW\DoliBundle\Entity\Societe;

class PricelistRepository extends \Doctrine\ORM\EntityRepository
{
  public function getCustomPrice($qty, Product $product, Societe $societe = null)
  {
    $qb = $this
      ->createQueryBuilder('p')
      ->leftJoin('p.product', 'prod')
        ->addSelect('prod')
        ->andWhere('p.product = :product')
          ->setParameter('product', $product)
      ->leftJoin('p.societe', 'soc')
        ->addSelect('soc')
      ->andWhere('p.fromQty <= :qty')
      ->setParameter('qty', $qty)
    ;

    if($societe){
      $qb
        ->andWhere('p.societe = :societe')
          ->setParameter('societe', $societe)
      ;
    }else{
      $qb->andWhere('p.societe IS NULL');
    }

    $list = $qb
      ->getQuery()
      ->getResult()
    ;

    if(empty($list)){
      return null;
    }

    $fromQty = 0;
    foreach($list as $pricelist){
      if($fromQty < $pricelist->getFromQty()){
        $fromQty = $pricelist->getFromQty();
        $r = $pricelist;
      }
    }

    return isset($r) ? $r : null;
  }

  public function getListCustomPrice(Product $product, Societe $societe)
  {
    return $this
      ->createQueryBuilder('p')
      ->leftJoin('p.product', 'prod')
        ->addSelect('prod')
        ->andWhere('p.product = :product')
          ->setParameter('product', $product)
      ->leftJoin('p.societe', 'soc')
        ->addSelect('soc')
        ->andWhere('p.societe = :societe')
          ->setParameter('societe', $societe)
      ->orderBy('p.fromQty')
      ->getQuery()
      ->getResult()
    ;
  }
}
