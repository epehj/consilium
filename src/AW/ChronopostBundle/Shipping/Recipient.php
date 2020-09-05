<?php

namespace AW\ChronopostBundle\Shipping;

class Recipient
{
  protected $recipientName;        // string [a-zA-Z0-9](0,100)
  protected $recipientName2;       // string [a-zA-Z0-9](0,100)
  protected $recipientAdress1;     // string [a-zA-Z0-9](0,38)
  protected $recipientAdress2;     // string [a-zA-Z0-9](0,38)
  protected $recipientZipCode;     // string [a-zA-Z0-9](0,9)
  protected $recipientCity;        // string [a-zA-Z0-9](0,50)
  protected $recipientCountry;     // string [A-Z](2)
  protected $recipientContactName; // string [a-zA-Z0-9](0,100)
  protected $recipientEmail;       // string [a-zA-Z0-9](0,80)
  protected $recipientPhone;       // string [a-zA-Z0-9](0,17)
  protected $recipientMobilePhone; // string [a-zA-Z0-9](0,17)
  protected $recipientPreAlert;    // int [0|22] 0: pas de préalerte 11: préalerte mail destinataire

  public function __construct(Array $params=array()){
    $this->recipientName        = isset($params['recipientName']) ? $params['recipientName'] : '';
    $this->recipientName2       = isset($params['recipientName2']) ? $params['recipientName2'] : '';
    $this->recipientAdress1     = isset($params['recipientAdress1']) ? $params['recipientAdress1'] : '';
    $this->recipientAdress2     = isset($params['recipientAdress2']) ? $params['recipientAdress2'] : '';
    $this->recipientZipCode     = isset($params['recipientZipCode']) ? $params['recipientZipCode'] : '';
    $this->recipientCity        = isset($params['recipientCity']) ? $params['recipientCity'] : '';
    $this->recipientCountry     = isset($params['recipientCountry']) ? $params['recipientCountry'] : '';
    $this->recipientContactName = isset($params['recipientContactName']) ? $params['recipientContactName'] : '';
    $this->recipientEmail       = isset($params['recipientEmail']) ? $params['recipientEmail'] : '';
    $this->recipientPhone       = isset($params['recipientPhone']) ? $params['recipientPhone'] : '';
    $this->recipientMobilePhone = isset($params['recipientMobilePhone']) ? $params['recipientMobilePhone'] : '';
    $this->recipientPreAlert    = isset($params['recipientPreAlert']) ? $params['recipientPreAlert'] : '0';
  }
}
