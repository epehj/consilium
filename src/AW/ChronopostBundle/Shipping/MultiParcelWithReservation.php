<?php

namespace AW\ChronopostBundle\Shipping;

class MultiParcelWithReservation
{
  protected $esdValue;           // esdValue
  protected $headerValue;        // headerValue
  protected $shipperValue;       // shipperValue
  protected $customerValue;      // customerValue
  protected $recipientValue;     // recipientValue
  protected $refValue;           // refValue
  protected $skybillValue;       // skybillValue
  protected $skybillParamsValue; // skybillParamsValue
  protected $password;           // string [0-9](6)
  protected $modeRetour;         // string 1|2
  protected $numberOfParcel;     // int

  public function __construct(Array $params=array()){
    $this->esdValue           = isset($params['esdValue']) ? $params['esdValue'] : '';
    $this->headerValue        = isset($params['headerValue']) ? $params['headerValue'] : '';
    $this->shipperValue       = isset($params['shipperValue']) ? $params['shipperValue'] : '';
    $this->customerValue      = isset($params['customerValue']) ? $params['customerValue'] : '';
    $this->recipientValue     = isset($params['recipientValue']) ? $params['recipientValue'] : '';
    $this->refValue           = isset($params['refValue']) ? $params['refValue'] : '';
    $this->skybillValue       = isset($params['skybillValue']) ? $params['skybillValue'] : '';
    $this->skybillParamsValue = isset($params['skybillParamsValue']) ? $params['skybillParamsValue'] : '';
    $this->password           = isset($params['password']) ? $params['password'] : '';
    $this->modeRetour         = isset($params['modeRetour']) ? $params['modeRetour'] : '2';
    $this->numberOfParcel     = isset($params['numberOfParcel']) ? $params['numberOfParcel'] : '';
  }
}
