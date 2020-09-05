<?php

namespace AW\DoliBundle\Service;

use Doctrine\ORM\EntityManager;

use AW\DoliBundle\Entity\Product;
use AW\DoliBundle\Entity\Societe;

class PricelistUtils
{
  private $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function getCustomerPrice($qty, Product $product, Societe $societe)
  {
    $er = $this->em
      ->getRepository('AWDoliBundle:Pricelist')
    ;

    $pricelist = $er->getCustomPrice($qty, $product, $societe);
    if($pricelist !== null){
      return $pricelist->getPrice();
    }

    $pricelist = $er->getCustomPrice($qty, $product);
    if($pricelist !== null){
      return $pricelist->getPrice();
    }

    return $product->getPrice();
  }

  public function getListCustomerPrice(Product $product, Societe $societe)
  {
    return $this->em
      ->getRepository('AWDoliBundle:Pricelist')
      ->getListCustomPrice($product, $societe)
    ;
  }
}
