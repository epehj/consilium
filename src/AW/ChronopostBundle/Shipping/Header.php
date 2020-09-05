<?php

namespace AW\ChronopostBundle\Shipping;

class Header
{
  protected $idEmit;         // string = CHRFR
  protected $accountNumber;  // int [0-9]{8}
  protected $subAccount;     // int [0-9]{3}

  public function __construct(Array $params=array()){
    $this->idEmit         = isset($params['idEmit']) ? $params['idEmit'] : 'CHRFR';
    $this->accountNumber  = isset($params['accountNumber']) ? $params['accountNumber'] : '';
    $this->subAccount     = isset($params['subAccount']) ? $params['subAccount'] : '';
  }
}
