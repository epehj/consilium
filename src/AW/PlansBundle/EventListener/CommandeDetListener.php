<?php

namespace AW\PlansBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use AW\PlansBundle\Entity\CommandeDet;
use AW\PlansBundle\PDF\Generator as PDFGenerator;

class CommandeDetListener
{
  private $doliCommandeListener;

  public function __construct(DoliCommandeListener $doliCommandeListener)
  {
    $this->doliCommandeListener = $doliCommandeListener;
  }

  private function updateCommande(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof CommandeDet){
      return;
    }

    $commande = $entity->getCommande();
    try{
      $args->getEntityManager()->refresh($commande);
    }catch(\Exception $e){
      return;
    }

    $commande->setDateModification(new \DateTime());

    $this->doliCommandeListener->updateDoliCommandeDet($commande);
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $this->updateCommande($args);
  }

  public function postUpdate(LifecycleEventArgs $args)
  {
    $this->updateCommande($args);
  }

  public function postRemove(LifecycleEventArgs $args)
  {
    $this->updateCommande($args);
  }
}
