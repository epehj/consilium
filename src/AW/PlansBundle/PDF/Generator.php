<?php

namespace AW\PlansBundle\PDF;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Expedition;
use AW\PlansBundle\PDF\CommandePDF;
use AW\PlansBundle\PDF\PosePDF;
use AW\PlansBundle\PDF\ShippingPDF;
use AW\PlansBundle\PDF\OfpPDF;

class Generator
{
  private $documentsDir;
  private $doliDocumentsDir;

  public function __construct($documentsDir, $doliDocumentsDir)
  {
    $this->documentsDir = $documentsDir;
    $this->doliDocumentsDir = $doliDocumentsDir;
  }

  public function generateCommandePDF(Commande $commande)
  {
    $commandePDF = new CommandePDF($this->documentsDir, $this->doliDocumentsDir);
    $commandePDF->generate($commande);
  }

  public function generatePosePDF(Commande $commande, $nameClient)
  {
    $posePDF = new PosePDF($this->documentsDir, $this->doliDocumentsDir);
    $posePDF->generate($commande, $nameClient);
  }

  public function generateShippingPDF(Expedition $expedition, $shippingFilePDF=null)
  {
    $shippingPDF = new ShippingPDF($this->documentsDir, $this->doliDocumentsDir);
    $shippingPDF->generate($expedition, $shippingFilePDF);
  }

  public function generateOfpPDF($commandes, $ref, $user)
  {
    $ofpPDF = new OfpPDF($this->documentsDir);
    $ofpPDF->generate($commandes, $ref, $user);
  }
}
