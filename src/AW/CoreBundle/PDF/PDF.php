<?php

namespace AW\CoreBundle\PDF;

abstract class PDF extends \FPDI
{
  protected $pageWidth = 210;
  protected $pageHeight = 297;
  protected $marginX = 5;
  protected $marginY = 5;
  protected $headerHeight = 30;

  public function __construct($orientation='P', $unit='mm', $format='A4')
  {
    parent::__construct($orientation, $unit, $format);
    $this->SetMargins($this->marginX, $this->marginX * 1.5 +$this->headerHeight);
    $this->SetAutoPageBreak(true, 20);
    $this->AliasNbPages();
  }
}
