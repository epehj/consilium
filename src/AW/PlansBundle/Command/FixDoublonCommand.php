<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AW\PlansBundle\Entity\Commande;

class FixDoublonCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:fixdoublon')
      ->setDescription('Corriger les doublons dans les commandes Dolibarr')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln([
      'Correction des commandes Dolibarr',
      '=================================',
      ''
    ]);

    $em = $this
      ->getContainer()
      ->get('doctrine')
      ->getManager()
    ;

    $doliCommandeListener = $this
      ->getContainer()
      ->get('aw_plans.eventlistener.doli_commande')
    ;

    $user = $em
      ->getRepository('AWDoliBundle:User')
      ->find(1)
    ;
    if($user === null){
      $output->writeln('User Admin introuvable');
      return;
    }

    $columns = array(
      5 => array(
        'data' => 'date',
        'name' => 'date',
        'search' => array(
          'value' => '2018'
        )
      )
    );

    $qb = $em
      ->getRepository('AWPlansBundle:Commande')
      ->getListQueryBuilder($columns, array(), $user)
    ;

    $commandes = $qb
      ->getQuery()
      ->getResult()
    ;

    $nbBug = 0;
    $perteHT = 0;
    $ca = 0;
    foreach($commandes as $commande){
      if($commande->getDoliCommande() === null){
        continue;
      }

      $totalHt = 0;
      foreach($commande->getDoliCommande()->getListDet() as $det){
        $totalHt += $det->getTotalHt();
      }

      $ca += $totalHt;
      $epsilon = 0.01;
      if(abs($totalHt - $commande->getDoliCommande()->getTotalHt()) >= $epsilon){
        $nbBug++;
        $perte = round($totalHt, 2) - round($commande->getDoliCommande()->getTotalHt(), 2);
        $perteHT += $perte;
        $output->write($commande->getRef().' : '.$totalHt.' - '.$commande->getDoliCommande()->getTotalHt().' = '.$perte);

        $factures = array();
        foreach($commande->getDoliCommande()->getCoFactures() as $coFacture){
          $factures[] = $coFacture->getFacture()->getRef();
        }

        $doliCommandeListener->updateDoliCommandeDet($commande);
        $em->flush();

        if(empty($factures)){
          $output->writeln('');
        }else{
          $output->writeln(' : '.implode($factures, ' - '));
        }
      }
    }

    $output->writeln('CA : '.$ca.' / Perte : '.$perteHT);
    $output->writeln($nbBug . ' / ' . count($commandes));
  }
}
