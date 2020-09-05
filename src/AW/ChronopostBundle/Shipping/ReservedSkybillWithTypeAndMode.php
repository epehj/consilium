<?php

namespace AW\ChronopostBundle\Shipping;

class ReservedSkybillWithTypeAndMode
{
  protected $reservationNumber;  // string
  protected $mode;

  public function __construct(Array $params=array()){
    $this->reservationNumber  = isset($params['reservationNumber']) ? $params['reservationNumber'] : '';
    $this->mode               = isset($params['mode']) ? $params['mode'] : 'PDF';
  }
}
