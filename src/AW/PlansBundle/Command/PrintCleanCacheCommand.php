<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class PrintCleanCacheCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:print:clean')
      ->setDescription("Supprimer les fichiers temporaires de génération d'impression")
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln('Vidange du dossier...');

    $fs = new Filesystem();
    $dir = $this->getContainer()->getParameter('documents_dir').'/impression/';
    $fs->remove($dir);
  }
}
