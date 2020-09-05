<?php

namespace AW\PlansBundle\PDF;

use Symfony\Component\Filesystem\Filesystem;
use AW\CoreBundle\PDF\PDF;

class OfpPDF extends PDF
{
  private $documentsDir;

  public function __construct($documentsDir)
  {
    $this->documentsDir = $documentsDir;

    parent::__construct();
  }

  public function generate($commandes, $ref, $user)
  {
    $output = $this->documentsDir.'/ofp/'.$ref.'.pdf';

    $this->SetAutoPageBreak(false);

    $this->AddPage();

    $this->SetFont('Arial', 'B', 15);
    $this->SetXY($this->pageWidth - $this->marginX - 100, $this->marginY * 2);
    $this->Cell(100, 0, 'BON DE FABRICATION', 0, 0, 'R');
    $this->SetFont('Arial', '', 10);
    $this->SetXY($this->pageWidth - $this->marginX - 100, $this->marginY * 3);
    $this->Cell(100, 0, utf8_decode('Réf: '.$ref), 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX - 100, $this->marginY * 4);
    $this->Cell(100, 0, utf8_decode('Créé le: '.date('d/m/Y H:i')), 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX - 100, $this->marginY * 5);
    $this->Cell(100, 0, utf8_decode('Par: '.$user->getFullname()), 0, 1, 'R');

    $this->SetFont('Arial', '', 7);
    $this->SetXY($this->marginX, $this->getY()+10);
    $this->Cell(20, 5, utf8_decode('Quantité'), 1);

    $qty = array(
      'A4' => 0,
      'A3' => 0,
      'A2' => 0,
      'A1' => 0,
      'A0' => 0
    );

    foreach($commandes as $commande){
      foreach($commande->getListDet() as $d){
        if($d->getMatiere() != 'PR'){
          continue;
        }

        $qty[$d->getFormat()]++;
      }
    }

    $x = $this->GetX();
    $this->Cell(15, 5, utf8_decode('A4 : '.$qty['A4']), 1);
    $this->SetXY($x, $this->GetY()+5);
    $this->Cell(15, 5, utf8_decode('A3 : '.$qty['A3']), 1);
    $this->SetXY($x, $this->GetY()+5);
    $this->Cell(15, 5, utf8_decode('A2 : '.$qty['A2']), 1);
    $this->SetXY($x, $this->GetY()+5);
    $this->Cell(15, 5, utf8_decode('A1 : '.$qty['A1']), 1);
    $this->SetXY($x, $this->GetY()+5);
    $this->Cell(15, 5, utf8_decode('A0 : '.$qty['A0']), 1);

    $this->SetXY($this->marginX, $this->getY()+10);

    $colRefWidth = 20;
    $colSiteWidth = 80;
    $colQtyWidth = 20;
    $colCkWidth = 5;
    $colClientWidth = $this->pageWidth - ($this->marginX*2) - $colRefWidth - $colSiteWidth - $colQtyWidth - $colCkWidth;

    $this->SetFillColor(115);
    $this->Cell($colRefWidth, 5, utf8_decode('N°'), 1, 0, '', true);
    $this->Cell($colClientWidth, 5, utf8_decode('Client'), 1, 0, '', true);
    $this->Cell($colSiteWidth, 5, utf8_decode('Site'), 1, 0, '', true);
    $this->Cell($colQtyWidth, 5, utf8_decode('Qté plans'), 1, 0, 'C', true);
    $this->Cell($colCkWidth, 5, '', 1, 0, '', true);

    $i = 0;
    foreach($commandes as $commande){
      $qty = 0;
      foreach($commande->getListDet() as $d){
        if($d->getMatiere() == 'PR'){
          $qty++;
        }
      }

      if($qty == 0){
        continue;
      }

      if($this->pageHeight - $this->marginY < $this->GetY() + $this->marginY){
        $this->AddPage();
        $this->SetXY($this->marginX, $this->marginY);
      }else{
        $this->SetXY($this->marginX, $this->getY()+5);
      }

      if($i%2 == 0){
        $this->SetFillColor(214);
      }else{
        $this->SetFillColor(255);
      }

      $this->Cell($colRefWidth, 5, utf8_decode($commande->getRef()), 1, 0, '', true);
      $this->Cell($colClientWidth, 5, utf8_decode($commande->getSociete()->getName()), 1, 0, '', true);
      $this->Cell($colSiteWidth, 5, utf8_decode($commande->getSite()), 1, 0, '', true);
      $this->Cell($colQtyWidth, 5, utf8_decode($qty), 1, 0, 'C', true);
      $this->Cell($colCkWidth, 5, '', 1, 0, '', true);

      $i++;
    }

    if($this->pageHeight - $this->marginY < $this->GetY() + 30){
      $this->AddPage();
      $this->SetXY($this->marginX, $this->marginY);
    }else{
      $this->SetY($this->GetY()+10);
    }

    $this->SetX($this->marginX);
    $this->Rect($this->GetX(), $this->GetY(), 50, 20);
    $this->Cell(50, 5, utf8_decode('Signature du demandeur'), 0, 0, 'C');
    $this->SetX($this->pageWidth - $this->marginX - 50);
    $this->Rect($this->GetX(), $this->GetY(), 50, 20);
    $this->Cell(50, 5, utf8_decode("Signature de l'opérateur"), 0, 0, 'C');

    $this->Output($output, 'F');
  }
}
