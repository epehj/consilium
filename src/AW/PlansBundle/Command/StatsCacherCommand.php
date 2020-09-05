<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class StatsCacherCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:stats:update')
      ->setDescription('Mettre à jour des caches de stats')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $cache = new FilesystemCache();

    $output->writeln([
      'Mise à jour des caches de stats',
      '===============================',
      ''
    ]);

    $cache->deleteMultiple(array(
      'stats.delay_bat.month',
      'stats.delay_bat.year',
      'stats.number_bat.month',
      'stats.number_bat.year',
      'stats.delay_bat_valid.month',
      'stats.delay_bat_valid.year',
      'stats.lastupdate'
    ));
    $output->writeln('Caches vidés...');

    $er = $this
      ->getContainer()
      ->get('doctrine')
      ->getManager()
      ->getRepository('AWPlansBundle:Commande')
    ;

    $monthStart = (new \DateTime('first day of this month'))
      ->setTime(0, 0, 0)
    ;

    $monthEnd = (new \DateTime('last day of this month'))
      ->setTime(23, 59, 59)
    ;

    $yearStart = (new \DateTime('first day of January'))
      ->setTime(0, 0, 0)
    ;

    $yearEnd = (new \DateTime('last day of December'))
      ->setTime(23, 59, 59)
    ;

    // calcul stats BAT du mois
    $commandes = $er
      ->getBetweenDateWithBAT($monthStart, $monthEnd)
    ;
    $nbCommandesTaken = count($commandes);
    $delayBATMonth = 0;
    $nbBATMonth = 0;
    foreach($commandes as $commande){
      $delay = $commande->getDelayMoyenBAT($this->getContainer()->get('aw.core.service.utils'));
      if($delay == 0){
        $nbCommandesTaken--;
      }

      $delayBATMonth += $delay;
      $nbBATMonth += count($commande->getBats());
    }
    $cache->setMultiple(array(
      'stats.delay_bat.month' => $nbCommandesTaken ? number_format($delayBATMonth / $nbCommandesTaken, 2, ',', ' ') : 0,
      'stats.number_bat.month' => $nbCommandesTaken ? number_format($nbBATMonth / $nbCommandesTaken, 2, ',', ' ') : 0
    ));
    $output->writeln('Calcul BAT du mois...');

    // calcul stats BAT de l'année
    $commandes = $er
      ->getBetweenDateWithBAT($yearStart, $yearEnd)
    ;
    $nbCommandesTaken = count($commandes);
    $delayBATYear = 0;
    $nbBATYear = 0;
    foreach($commandes as $commande){
      $delay = $commande->getDelayMoyenBAT($this->getContainer()->get('aw.core.service.utils'));
      if($delay == 0){
        $nbCommandesTaken--;
      }

      $delayBATYear += $delay;
      $nbBATYear += count($commande->getBats());
    }
    $cache->setMultiple(array(
      'stats.delay_bat.year' => $nbCommandesTaken ? number_format($delayBATYear / $nbCommandesTaken, 2, ',', ' ') : 0,
      'stats.number_bat.year' => $nbCommandesTaken ? number_format($nbBATYear / $nbCommandesTaken, 2, ',', ' ') : 0
    ));
    $output->writeln("Calcul BAT de l'année...");

    // calcul delai moyen BAT validé du mois
    $commandes = $er
      ->getBetweenDateBATValid($monthStart, $monthEnd)
    ;
    $nbCommandesTaken = count($commandes);
    $delayBATValidMonth = 0;
    foreach($commandes as $commande){
      $delay = $commande->getDelayBATValid($this->getContainer()->get('aw.core.service.utils'));
      if($delay == 0){
        $nbCommandesTaken--;
      }

      $delayBATValidMonth += $delay;
    }
    $cache->set('stats.delay_bat_valid.month', $nbCommandesTaken ? number_format($delayBATValidMonth / $nbCommandesTaken, 2, ',', ' ') : 0);
    $output->writeln('Calcul BAT validé du mois...');

    // calcul delai moyen BAT validé de l'année
    $commandes = $er
      ->getBetweenDateBATValid($yearStart, $yearEnd)
    ;
    $nbCommandesTaken = count($commandes);
    $delayBATValidYear = 0;
    foreach($commandes as $commande){
      $delay = $commande->getDelayBATValid($this->getContainer()->get('aw.core.service.utils'));
      if($delay == 0){
        $nbCommandesTaken--;
      }

      $delayBATValidYear += $delay;
    }
    $cache->set('stats.delay_bat_valid.year', $nbCommandesTaken ? number_format($delayBATValidYear / $nbCommandesTaken, 2, ',', ' ') : 0);
    $output->writeln("Calcul BAT validé de l'année...");

    $cache->set('stats.lastupdate', time());
  }
}
