<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use AW\PlansBundle\Entity\Commande;

class AlertCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:alert')
      ->setDescription('Mettre à jour les alertes')
      ->addOption(
        'all',
        null,
        InputOption::VALUE_NONE,
        'Mettre à jour toutes les commandes sans exception (peu être très lent)'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln([
      'Mise à jour des alertes de commandes plans',
      '==========================================',
      ''
    ]);

    $em = $this
      ->getContainer()
      ->get('doctrine')
      ->getManager()
    ;

    if($input->getOption('all')){
      $commandes = $em
        ->getRepository('AWPlansBundle:Commande')
        ->findAll()
      ;
    }else{
      $commandes = $em
        ->getRepository('AWPlansBundle:Commande')
        ->findForAlertUpdate()
      ;
    }

    $now = new \DateTime();

    foreach($commandes as $commande){
      if($commande->getStatus() == Commande::STATUS_ATTENTE_VALIDATION or $commande->getStatus() == Commande::STATUS_VALIDATED){
        $date = ($commande->getStatus() == Commande::STATUS_ATTENTE_VALIDATION) ? $commande->getDateCreation() : $commande->getDateValidation();

        $interval = $date !== null ? $date->diff($now) : $now->diff($now);

        if($interval->d >= 4){
          $commande->setAlert(3);
        }elseif($interval->d >= 2){
          $commande->setAlert(2);
        }else{
          $commande->setAlert(1);
        }
      }elseif($commande->getStatus() == Commande::STATUS_BAT_MODIF or $commande->getStatus() == Commande::STATUS_BAT_VALIDATED){
        $lastbat = null;
        foreach($commande->getBats() as $bat){
          if($lastbat === null or $bat->getNumero() > $lastbat->getNumero()){
            $lastbat = $bat;
          }
        }

        if($lastbat === null){
          $commande->setAlert(0);
        }else{
          $date = ($commande->getStatus() == Commande::STATUS_BAT_MODIF) ? $lastbat->getDateModification() : $lastbat->getDateValidation();

          $interval = $date !== null ? $date->diff($now) : $now->diff($now);

          if($interval->d >= 2){
            $commande->setAlert(3);
          }elseif($interval->d >= 1){
            $commande->setAlert(2);
          }else{
            $commande->setAlert(1);
          }
        }
      }else{
        $commande->setAlert(0);
      }
    }

    $em->flush();

    $output->writeln('Toutes les commandes ont été mise à jour');
  }
}
