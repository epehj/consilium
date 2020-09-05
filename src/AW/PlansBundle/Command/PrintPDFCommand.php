<?php

namespace AW\PlansBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

use AW\PlansBundle\PDF\PrintPDF;

class PrintPDFCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('aw:plans:print:pdf')
      ->setDescription("Générer le fichier PDF pour l'impression")
      ->addArgument(
        'source_dir',
        InputArgument::REQUIRED,
        'Dossier contenant tous les fichiers PDF à grouper'
      )
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln([
      "Générer le fichier d'impression",
      '==========================================',
      ''
    ]);

    $output->writeln('Dossier source : '.$input->getArgument('source_dir'));

    $sourceDir = $input->getArgument('source_dir');

    $finder = new Finder();
    $finder
      ->files()
      ->name('*.pdf')
      ->notName('OUTPUT*')
      ->in($sourceDir)
    ;

    $groupedFiles = array();
    $i = 0;
    $key = 0;
    $groupedFiles[$i] = array();
    foreach($finder as $file){
      $groupedFiles[$i][] = $file;

      if(
        ($i == 0 and $key >= 7) or
        ($i > 0 and $i%8 == 0)
      ){
        $i++;
      }

      $key++;
    }

    foreach($groupedFiles as $key => $files){
      $output = $sourceDir.'/OUTPUT-'.$key.'.pdf';

      $printPDF = new PrintPDF();
      $printPDF->generate($files, $output);
    }
  }
}
