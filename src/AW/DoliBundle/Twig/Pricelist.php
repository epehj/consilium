<?php

namespace AW\DoliBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use AW\DoliBundle\Service\PricelistUtils;
use AW\DoliBundle\Entity\Product;
use AW\DoliBundle\Entity\Societe;

class Pricelist extends AbstractExtension
{
  private $priceListUtils;

  public function __construct(PricelistUtils $priceListUtils)
  {
    $this->priceListUtils = $priceListUtils;
  }

  public function getCustomPrice($qty, Product $product, Societe $societe)
  {
    return $this->priceListUtils->getCustomerPrice($qty, $product, $societe);
  }

  public function getFunctions()
  {
    return array(
      new TwigFunction('getCustomPrice', array($this, 'getCustomPrice'))
    );
  }
}
