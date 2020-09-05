<?php

namespace AW\ChronopostBundle\Shipping;

class Skybill
{
  protected $evtCode;      // string DC
  protected $productCode;  // string [a-zA-Z0-9](2)
  protected $shipDate;     // dateTime
  protected $shipHour;     // int [0-9](2)
  protected $weight;       // float
  protected $weightUnit;   // string KGM
  protected $service;      // string
  protected $objectType;   // string [DOC|MAR]
  protected $height;       // float
  protected $length;       // float
  protected $width;        // float

  public function __construct(Array $params=array()){
    $this->evtCode      = isset($params['evtCode']) ? $params['evtCode'] : 'DC';
    $this->productCode  = isset($params['productCode']) ? $params['productCode'] : '';
    $this->shipDate     = isset($params['shipDate']) ? $params['shipDate'] : '';
    $this->shipHour     = isset($params['shipHour']) ? $params['shipHour'] : '';
    $this->weight       = isset($params['weight']) ? $params['weight'] : '';
    $this->weightUnit   = isset($params['weightUnit']) ? $params['weightUnit'] : 'KGM';
    $this->service      = isset($params['service']) ? $params['service'] : '0';
    $this->objectType   = isset($params['objectType']) ? $params['objectType'] : 'MAR';
    $this->height       = isset($params['height']) ? $params['height'] : '';
    $this->length       = isset($params['length']) ? $params['length'] : '';
    $this->width        = isset($params['width']) ? $params['width'] : '';
  }
}
