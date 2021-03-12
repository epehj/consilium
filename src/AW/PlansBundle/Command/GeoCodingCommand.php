<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeoCodingCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:geocoding')
      ->setDescription('Récupérer les coordonnées géographiques des sites')
      ->addOption('debug', 'd', InputOption::VALUE_NONE)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln([
      'Récupération des coordonnées géographiques des sites',
      '====================================================',
      ''
    ]);

    $em = $this
      ->getContainer()
      ->get('doctrine')
      ->getManager()
    ;

    $commandes = $em
      ->getRepository('AWPlansBundle:Commande')
      ->findEmptyGeoCode()
    ;
    foreach($commandes as $commande){
      $address = $commande->getAddress1().', '.$commande->getZip().' '.$commande->getTown().', France';
      $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.$this->getContainer()->getParameter('google_private_api_key');
      $response = file_get_contents($url);
      $json = json_decode($response, true);
      if($input->getOption('debug'))
          var_dump($json);

      if($json['status'] == 'OK'){
        $commande->setGeoCode(
          $json['results'][0]['geometry']['location']
        );

        $em->flush();
      }

      $output->writeln($commande->getRef().' : '.$json['status']);
    }
  }
}
