<?php

namespace AW\DoliBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use AW\DoliBundle\Entity\Commande;

class CommandeListener
{
  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    if(empty($entity->getRef())){
      $nextRef = $args
        ->getObjectManager()
        ->getRepository('AWDoliBundle:Commande')
        ->getNextRef()
      ;

      $entity->setRef($nextRef);
    }

    $entity
      ->setCondReglement($entity->getSociete()->getCondReglement())
      ->setModeReglement($entity->getSociete()->getModeReglement())
    ;

    $this->autoComplete($entity);
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    $this->autoComplete($entity);
  }

  private function autoComplete(Commande $commande)
  {
    $commande
      ->setMulticurrencyTotalHt($commande->getTotalHt())
      ->setMulticurrencyTotalTtc($commande->getTotalTtc())
      ->setMulticurrencyTotalTva($commande->getTva())
    ;
  }
}
