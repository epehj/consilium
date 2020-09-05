<?php

namespace AW\PlansBundle\PDF;

use Symfony\Component\Filesystem\Filesystem;
use AW\CoreBundle\PDF\PDF;
use AW\PlansBundle\Entity\Commande;

class CommandePDF extends PDF
{
  private $documentsDir;
  private $doliDocumentsDir;
  private $logo;

  public function __construct($documentsDir, $doliDocumentsDir)
  {
    $this->documentsDir = $documentsDir;
    $this->doliDocumentsDir = $doliDocumentsDir;
    $this->logo = $documentsDir.'/logo.png';

    parent::__construct();
  }

  public function Header()
  {
    $this->SetFillColor(214);
    $this->Rect($this->marginX, $this->marginY, $this->pageWidth - $this->marginX * 2, $this->headerHeight, 'F');

    if(file_exists($this->logo)){
      $this->Image($this->logo, $this->marginX + 5, $this->marginY + 5, 0, 20);
    }

    $this->SetFont('Arial', 'B', 15);
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 2);
    $this->Cell(100, 0, 'COMMANDE PLANS', 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 3);
    $this->SetFont('Arial', '', 10);
    $this->Cell(100, 0, utf8_decode('19 Rue d’Arsonval, 69680 Chassieu'), 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 4);
    $this->Cell(100, 0, 'Tel.: ', 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 5);
    $this->Cell(100, 0, 'Email.:', 0, 0, 0, 'R');

    $this->SetXY($this->marginX, $this->marginY * 1.5 + $this->headerHeight);
  }

  function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial', '', 8);
    $this->Cell(0, 5, utf8_decode('Le client a lu les conditions générales de vente et y adhère sans reserve.'), 'T', 1, 'C');
    $this->Cell(0, 5, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
  }

  public function generate(Commande $commande)
  {
    $fs = new Filesystem();
    $dir = $this->documentsDir.'/cmdplan/'.$commande->getDir().'/creation';
    if(!file_exists($dir)){
      $fs->mkdir($dir);
    }

    $this->AddPage();

    // Informations générales de la commande
    $labelWidth = 40;
    $clientNameWidth = 85;
    $zipWidth = 40;

    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, utf8_decode('N° commande: '));
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, $commande->getRef(), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Date de commande: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getDateCreation()->format('d/m/Y à H:i')), 1, 1);

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Client: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($clientNameWidth, 5, utf8_decode($commande->getSociete()->getName()), 1);

    $this->SetFont('Arial', 'B', 10);
    $this->Cell(20, 5, 'Code Client: ');
    $this->SetFont('Arial', '', 10);
    $this->SetX($this->GetX() + $this->marginX);
    $this->Cell($this->pageWidth - $this->marginX * 4 - $labelWidth - $clientNameWidth - 20, 5, utf8_decode($commande->getSociete()->getCodeClient()), 1, 1);

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

    $this->SetY($this->GetY() + 1);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell($labelWidth, 5, 'Remarques: ', 0, 1);
    $this->SetFont('Arial', '', 10);
    $this->MultiCell($this->pageWidth - $this->marginX * 2, 5, utf8_decode($commande->getRemarques()), 1);

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

    // sauvegarder
    $output = $dir.'/commande.pdf';
    $this->Output($output, 'F');
  }
}
