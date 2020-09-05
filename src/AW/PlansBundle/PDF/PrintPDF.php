<?php

namespace AW\PlansBundle\PDF;

use Symfony\Component\Finder\SplFileInfo;

class PrintPDF extends \FPDI
{
  private $format;
  private $size;
  private $position;

  public function __construct($format)
  {
    $this->format = $format;

    switch($this->format){
      case 'A0':
        $this->planSize = array(1188, 840);
        $this->size = array(1188, 840);
        break;
      case 'A1':
        $this->planSize = array(594, 840);
        $this->size = array(1812, 840);
        break;
      case 'A2':
        $this->planSize = array(594, 420);
        $this->size = array(1812, 840);
        break;
      case 'A3':
        $this->planSize = array(297, 420);
        $this->size = array(1797, 840);
        break;
      case 'A4':
        $this->planSize = array(297, 210);
        $this->size = array(1797, 840);
        break;
      case 'A5':
        $this->planSize = array(210, 148.5);
        $this->size = array(1910, 891);
        break;
      default:
        throw new \Exception('Format inconnu');
        break;
    }

    parent::__construct('L', 'mm', $this->size);
  }

  private function addCuttingLine($x, $y)
  {
    $width = 0.1;
    $length = 1;

    $this->SetLineWidth($width);
    $this->SetDrawColor(85, 87, 83);

    $positions = array(
      array($x, $y),
      array($x+$this->planSize[0], $y),
      array($x+$this->planSize[0], $y+$this->planSize[1]),
      array($x, $y+$this->planSize[1])
    );

    foreach($positions as $position){
      $this->Line(
        $position[0]-$length,
        $position[1],
        $position[0]+$length,
        $position[1]
      );
      $this->Line(
        $position[0],
        $position[1]-$length,
        $position[0],
        $position[1]+$length
      );
    }
  }

  private function addPlan(SplFileInfo $file)
  {
    if($this->position === null){
      $this->position  = array(0, 0);
    }else{
      if($this->position[1] + $this->planSize[1] >= $this->size[1]){
        $this->position[0] += $this->planSize[0];
        if(
          $this->format == 'A1' or
          $this->format == 'A2' or
          ($this->format == 'A3' and $this->position[0] == $this->planSize[0]*3) or
          ($this->format == 'A4' and $this->position[0] == $this->planSize[0]*3) or
          ($this->format == 'A5' and $this->position[0] == $this->planSize[0]*5)
        ){
          $this->position[0] += 15;
        }
        $this->position[1] = 0;
      }else{
        $this->position[1] += $this->planSize[1];
      }
    }

    $nbPage = $this->setSourceFile($file->getRealPath());
    if($nbPage > 1){
      throw new \Exception('Le fichier '.$file->getFilename().' a '.$nbPage.' pages.');
    }

    $tplidx = $this->importPage(1);
    $this->useTemplate($tplidx, $this->position[0], $this->position[1]);
    $this->addCuttingLine($this->position[0], $this->position[1]);
  }

  public function generate($files, $output)
  {
    $this->AddPage();
    $this->position = null;

    foreach($files as $file){
      $this->addPlan($file);
    }

    $this->Output($output, 'F');
  }
}
