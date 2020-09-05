<?php

namespace AW\PlansBundle\PDF;

use Symfony\Component\Filesystem\Filesystem;
use AW\CoreBundle\PDF\PDF;
use AW\PlansBundle\Entity\Expedition;

class ShippingPDF extends PDF
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
    $this->Cell(100, 0, 'BON DE LIVRAISON', 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 3);
    $this->SetFont('Arial', '', 10);
    $this->Cell(100, 0, utf8_decode('19 Rue d’Arsonval, 69680 Chassieu'), 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 4);
    $this->Cell(100, 0, 'Tel.: ', 0, 0, 0, 'R');
    $this->SetXY($this->pageWidth - $this->marginX * 2 - 100, $this->marginY * 5);
    $this->Cell(100, 0, 'Email:', 0, 0, 0, 'R');

    $this->SetXY($this->marginX, $this->marginY * 1.5 + $this->headerHeight);
  }

  public function Footer()
  {
    $this->SetY(-15);
    $this->SetFont('Arial', '', 8);
    $this->Cell(0, 5, utf8_decode('Le client a lu les conditions générales de vente et y adhère sans reserve.'), 'T', 1, 'C');
    $this->Cell(0, 5, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
  }

  public function generate(Expedition $expedition, $shippingPDF=null)
  {
    $fs = new Filesystem();
    $dir = $this->documentsDir.'/expplan/'.$expedition->getId();
    if(!file_exists($dir)){
      $fs->mkdir($dir);
    }

    $this->AddPage();

    // Adresse du client
    $commande = $expedition->getCommandes()[0];
		$x = $this->GetX(); $y = $this->GetY();
		$address_w = ($this->pageWidth - $this->marginX - $this->marginY - 20) / 2;
		$addr = "Adresse du client".PHP_EOL.PHP_EOL;
		$addr.= $commande->getSociete()->getName().PHP_EOL;
		$addr.= $commande->getSociete()->getAddress1().PHP_EOL;
    if($commande->getSociete()->getAddress2()){
      $addr.= $commande->getSociete()->getAddress2().PHP_EOL;
    }
		$addr.= $commande->getSociete()->getZip().' '.$commande->getSociete()->getTown().PHP_EOL;
		$addr.= $commande->getSociete()->getCountry()->getName().PHP_EOL.PHP_EOL;
		$addr.= 'Code client: '.$commande->getSociete()->getCodeClient();
		$this->SetFont('Arial', '', 10);
		$this->MultiCell($address_w, 5, utf8_decode($addr), 1, 'L');

    // Adresse de livraison
		if($shippingPDF){
			$yEnd = $this->GetY();

			$this->SetXY($x+$address_w+20, $y);
			$addr = "Adresse de livraison".PHP_EOL.PHP_EOL;
			$addr.= $commande->getShippingRecipient().PHP_EOL;
			$addr.= $commande->getShippingAddress1().PHP_EOL;
      if($commande->getShippingAddress2()){
        $addr.= $commande->getShippingAddress2().PHP_EOL;
      }
			$addr.= $commande->getShippingZip().' '.$commande->getShippingTown().PHP_EOL;
			$addr.= $commande->getShippingCountry()->getName();
			$this->SetFont('Arial', '', 10);
			$this->MultiCell($address_w, 5, utf8_decode($addr), 1, 'L');

			if($yEnd > $this->GetY()){
				$this->SetY($yEnd);
			}
		}

    $labelWidth = 40;
    $colQtyWidth = 18;
    $colFormatWidth = 16;
    $colMatiereWidth = 42;
    $colDesignWidth = 16;
    $colVueWidth = 60;

    foreach($expedition->getCommandes() as $commande){
      $this->SetY($this->GetY() + 1);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell($labelWidth, 5, utf8_decode('N° commande: '));
      $this->SetFont('Arial', '', 10);
      $this->SetX($this->GetX() + $this->marginX);
      $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, $commande->getRef(), 1, 1);

      $this->SetY($this->GetY() + 1);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell($labelWidth, 5, 'Site: ');
      $this->SetFont('Arial', '', 10);
      $this->SetX($this->GetX() + $this->marginX);
      $this->Cell($this->pageWidth - $this->marginX * 3 - $labelWidth, 5, utf8_decode($commande->getSite()), 1, 1);

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
    }

    if(!$shippingPDF){
      $y = $this->GetY();
      $this->SetY($y+10);

      $this->SetFont('Arial', '', 10);
      $this->Cell(0, 5, utf8_decode("Récupéré(s) le ".date('d/m/Y')." à Chassieu,"), 0, 1);

      $this->SetFont('Arial', 'I', 7);
      $x = $this->GetX();
      $this->SetX($x+20);
      $this->Cell(0, 5, utf8_decode("Signature du client"));
    }

    $output = $dir.'/BL.pdf';
    $this->Output($output, 'F');

    if($shippingPDF){
      $this->merge(array($shippingPDF, $output), $output);
    }
  }

  private function merge(array $files, $ouput){
		$pdf = new \FPDI();
		foreach($files as $file){
			$pageCount = $pdf->setSourceFile($file);
			for($pageNo=1; $pageNo<=$pageCount; $pageNo++){
				$tplIdx = $pdf->ImportPage($pageNo);
				$s = $pdf->getTemplatesize($tplIdx);
				$pdf->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
				$pdf->useTemplate($tplIdx);
			}
		}

		$pdf->Output($ouput, 'F');
	}
}
