<?php

namespace AW\ChronopostBundle\Shipping;

class SkybillParams
{
  protected $mode;

  public function __construct(Array $params=array()){
    $this->mode = isset($params['mode']) ? $params['mode'] : 'PDF';
  }
}
