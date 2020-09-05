<?php

namespace AW\ChronopostBundle\Shipping;

class Shipper
{
  protected $shipperCivility;    // string [E|L|M] E: Madame, L: Mademoiselle, M: Monsieur
  protected $shipperName;        // string [a-zA-Z0-9](0,100)
  protected $shipperName2;       // string [a-zA-Z0-9](0,100)
  protected $shipperAdress1;     // string [a-zA-Z0-9](0,38)
  protected $shipperAdress2;     // string [a-zA-Z0-9](0,38)
  protected $shipperZipCode;     // string [a-zA-Z0-9](0,9)
  protected $shipperCity;        // string [a-zA-Z0-9](0,50)
  protected $shipperCountry;     // string [A-Z](2)
  protected $shipperContactName; // string [a-zA-Z0-9](0,100)
  protected $shipperEmail;       // string [a-zA-Z0-9](0,80)
  protected $shipperPhone;       // string [a-zA-Z0-9](0,17)
  protected $shipperMobilePhone; // string [a-zA-Z0-9](0,17)
  protected $shipperPreAlert;    // int [0|11] 0: pas de préalerte 11: abonnement tracking expéditeur

  public function __construct(Array $params=array()){
    $this->shipperCivility    = isset($params['shipperCivility']) ? $params['shipperCivility'] : '1';
    $this->shipperName        = isset($params['shipperName']) ? $params['shipperName'] : '';
    $this->shipperName2       = isset($params['shipperName2']) ? $params['shipperName2'] : '';
    $this->shipperAdress1     = isset($params['shipperAdress1']) ? $params['shipperAdress1'] : '';
    $this->shipperAdress2     = isset($params['shipperAdress2']) ? $params['shipperAdress2'] : '';
    $this->shipperZipCode     = isset($params['shipperZipCode']) ? $params['shipperZipCode'] : '';
    $this->shipperCity        = isset($params['shipperCity']) ? $params['shipperCity'] : '';
    $this->shipperCountry     = isset($params['shipperCountry']) ? $params['shipperCountry'] : '';
    $this->shipperContactName = isset($params['shipperContactName']) ? $params['shipperContactName'] : '';
    $this->shipperEmail       = isset($params['shipperEmail']) ? $params['shipperEmail'] : '';
    $this->shipperPhone       = isset($params['shipperPhone']) ? $params['shipperPhone'] : '';
    $this->shipperMobilePhone = isset($params['shipperMobilePhone']) ? $params['shipperMobilePhone'] : '';
    $this->shipperPreAlert    = isset($params['shipperPreAlert']) ? $params['shipperPreAlert'] : '0';
  }
}
