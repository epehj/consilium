<?php

namespace AW\DoliBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use AW\DoliBundle\Entity\CommandeDet;

class CommandeDetListener
{
  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof CommandeDet){
      return;
    }

    $this->autoComplete($entity);
  }

  /*
   * Calcul automatiquement certains champs.
   * Les données suivantes doivent être à minimum remplies :
   *    - qty : quantité
   *    - price : prix unitaire
   *    - tvaTx : taux de TVA
   */
  private function autoComplete(CommandeDet $det)
  {
    $qty = $det->getQty();
    $price = $det->getPrice();
    $tvaTx = $det->getTvaTx();
    $totalHt = ($price * $qty) - ($det->getRemise() * $qty);
    $totalTva = $totalHt * ($tvaTx / 100);
    $paHt = $det->getProduct()->getCostPrice() > 0 ? $det->getProduct()->getCostPrice() : $det->getProduct()->getPmp();

    if($det->getRemise() > 0){
      $remisePercent = round($det->getRemise() * 100 / $price, 2);
    }else{
      $remisePercent = 0;
    }

    $det
      ->setSubprice($price)
      ->setRemisePercent($remisePercent)
      ->setTotalHt($totalHt)
      ->setTotalTva($totalTva)
      ->setTotalTtc($totalHt+$totalTva)
      ->setBuyPriceHt($paHt)
    ;
  }
}
