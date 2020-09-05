<?php

namespace AW\PlansBundle\PDF;

use Symfony\Component\Filesystem\Filesystem;
use AW\CoreBundle\PDF\PDF;
use AW\PlansBundle\Entity\Commande;

class PosePDF extends PDF
{
  private $documentsDir;
  private $doliDocumentsDir;

  public function __construct($documentsDir, $doliDocumentsDir)
  {
    $this->documentsDir = $documentsDir;
    $this->doliDocumentsDir = $doliDocumentsDir;

    parent::__construct();
  }

  public function generate(Commande $commande, $nameClient='')
  {
    $fs = new Filesystem();
    $dir = $this->documentsDir.'/cmdplan/'.$commande->getDir().'/validation';
    if(!file_exists($dir)){
      $fs->mkdir($dir);
    }

    $this->AddPage();

    /**
     * HEADER
     */
    $this->SetFillColor(214);
    $this->Rect($this->marginX, $this->marginY, $this->pageWidth - $this->marginX * 2, $this->headerHeight, 'F');

    // Logo du client
    if($commande->getSociete()->getLogo()){
      $logoClient = $this->doliDocumentsDir.'/societe/'.$commande->getSociete()->getId().'/logos/'.$commande->getSociete()->getLogo();
      if(file_exists($logoClient)){
        $this->Image($logoClient, $this->marginX + 5, $this->marginY + 5, 0, 20);
      }
    }

    $y = 2;
    $this->SetFont('Arial', 'B', 15);
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * $y);
    $this->Cell(100, 0, 'BON DE POSE PLANS', 0, 0, 0, 'R');

    $y++;
    $this->SetFont('Arial', '', 10);
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * $y);
    $this->Cell(100, 0, utf8_decode($commande->getSociete()->getName()), 0, 0, 0, 'R');

    $y++;
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * $y);
    $this->Cell(100, 0, utf8_decode($commande->getSociete()->getAddress1()), 0, 0, 0, 'R');

    if($commande->getSociete()->getAddress2()){
      $y++;
      $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * $y);
      $this->Cell(100, 0, utf8_decode($commande->getSociete()->getAddress1()), 0, 0, 0, 'R');
    }

    $y++;
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * $y);
    $this->Cell(100, 0, utf8_decode($commande->getSociete()->getZip().' '.$commande->getSociete()->getTown()), 0, 0, 0, 'R');

    $this->SetY($this->headerHeight + $this->marginY + 5);

    /**
     * INFOS SITE
     */
    $labelWidth = 40;
    $zipWidth = 40;

    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, utf8_decode('N° commande: '));
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, $commande->getRef(), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, utf8_decode('Référence: '));
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getRefClient()), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Site: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getSite()), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Adresse: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getAddress1()), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, utf8_decode('Adresse complémentaire: '));
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getAddress2()), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Code postal: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($zipWidth, 5, utf8_decode($commande->getZip()), 1);

    $this->SetFont('Arial', 'B', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell(20, 5, 'Ville: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($labelWidth + $zipWidth + $this->marginX, 5, utf8_decode($commande->getTown()), 1, 1);

    // Liste des plans
    $colQtyWidth = 18;
    $colFormatWidth = 16;
    $colMatiereWidth = 42;
    $colDesignWidth = 16;
    $colVueWidth = 60;

    $typePlan = '';
    $this->SetFillColor(214);
    foreach($commande->getListDet() as $det){
      if($typePlan != $det->getType()){
        $typePlan = $det->getType();

        $this->SetFont('Arial', 'B', 10);
        $this->SetY($this->GetY() + 5); // espace entre table
        $this->Cell($this->pageWidth - $this->marginX * 2, 5, utf8_decode($det->getTypeName()), 1, 1, 'C', 1);

        $this->Cell($colQtyWidth, 5, utf8_decode('Quantité'), 1, 0, 'C');
        $this->Cell($colFormatWidth, 5, 'Format', 1, 0, 'C');
        $this->Cell($colMatiereWidth, 5, utf8_decode('Matière'), 1, 0, 'C');
        if($typePlan != 'PM')
          $this->Cell($colDesignWidth, 5, 'Design', 1, 0, 'C');
        $this->Cell($colVueWidth, 5, 'Vue', 1, 0, 'C');
        if($typePlan != 'PM')
          $this->Cell($this->pageWidth - $this->marginX * 2 - $colQtyWidth - $colFormatWidth - $colMatiereWidth - $colDesignWidth - $colVueWidth, 5, 'Finition', 1, 1, 'C');
        else
          $this->Cell($this->pageWidth - $this->marginX * 2 - $colQtyWidth - $colFormatWidth - $colMatiereWidth - $colVueWidth, 5, 'Finition', 1, 1, 'C');
      }

      $this->SetFont('Arial', '', 10);
      $this->Cell($colQtyWidth, 5, $det->getQty(), 1, 0, 'C');
      $this->Cell($colFormatWidth, 5, utf8_decode($det->getFormatName()), 1, 0, 'C');
      $this->Cell($colMatiereWidth, 5, utf8_decode($det->getMatiereName()), 1, 0, 'C');
      if($typePlan != 'PM')
        $this->Cell($colDesignWidth, 5, utf8_decode($det->getDesignName()), 1, 0, 'C');
      $this->Cell($colVueWidth, 5, utf8_decode($det->getVueName()), 1, 0, 'C');
      if($typePlan != 'PM')
        $this->Cell($this->pageWidth - $this->marginX * 2 - $colQtyWidth - $colFormatWidth - $colMatiereWidth - $colDesignWidth - $colVueWidth, 5, utf8_decode($det->getFinitionName()), 1, 1, 'C');
      else
        $this->Cell($this->pageWidth - $this->marginX * 2 - $colQtyWidth - $colFormatWidth - $colMatiereWidth - $colVueWidth, 5, utf8_decode($det->getFinitionName()), 1, 1, 'C');
    }

    /**
     * Signatures
     */
    $this->SetY($this->GetY()+10);

    $this->SetFont('Arial', 'I', 8);
    $this->Cell($this->pageWidth - $this->marginX * 2, 5, utf8_decode('À '.$commande->getTown().', le '.date('d/m/Y')), 0, 1);
    $this->Ln();

    $this->Cell(40, 5, 'Le technicien', 0, 0, 'C');
    $this->SetX($this->GetX() + 60);
    $this->Cell(40, 5, 'Le client'.(empty($nameClient) ? '' : ' ('.utf8_decode($nameClient).')'), 0, 1, 'C');

    $signatureTech = $this->documentsDir.'/cmdplan/'.$commande->getDir().'/validation/signature_technicien.png';
    if(file_exists($signatureTech)){
      $this->Image($signatureTech, $this->GetX(), $this->GetY(), 40);
    }

    $signatureClient = $this->documentsDir.'/cmdplan/'.$commande->getDir().'/validation/signature_client.png';
    if(file_exists($signatureClient)){
      $this->Image($signatureClient, $this->GetX()+100, $this->GetY(), 40);
    }else{
      $this->SetX($this->GetX()+100);
      $this->Cell(40, 5, utf8_decode('Pas de représentant sur site'), 0, 1, 'C');
    }

    $output = $dir.'/BL-POSE.pdf';
    $this->Output($output, 'F');
  }
}
