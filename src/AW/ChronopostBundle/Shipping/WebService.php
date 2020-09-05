<?php

namespace AW\ChronopostBundle\Shipping;

class WebService extends \SoapClient
{
  protected $wsdl = "https://ws.chronopost.fr/shipping-cxf/ShippingServiceWS?wsdl";

  public function __construct()
  {
    parent::__construct($this->wsdl);
  }

  public function shippingMultiParcelWithReservation(MultiParcelWithReservation $params)
  {
    return parent::shippingMultiParcelWithReservation($params);
  }

  public function getReservedSkybillWithTypeAndMode(ReservedSkybillWithTypeAndMode $params){
    return parent::getReservedSkybillWithTypeAndMode($params);
  }
}
