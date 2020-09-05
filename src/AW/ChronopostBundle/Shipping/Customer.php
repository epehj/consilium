<?php

namespace AW\ChronopostBundle\Shipping;

class Customer
{
  protected $customerCivility;    // string [E|L|M] E: Madame, L: Mademoiselle, M: Monsieur
  protected $customerName;        // string [a-zA-Z0-9](0,100)
  protected $customerName2;       // string [a-zA-Z0-9](0,100)
  protected $customerAdress1;     // string [a-zA-Z0-9](0,38)
  protected $customerAdress2;     // string [a-zA-Z0-9](0,38)
  protected $customerZipCode;     // string [a-zA-Z0-9](0,9)
  protected $customerCity;        // string [a-zA-Z0-9](0,50)
  protected $customerCountry;     // string [A-Z](2)
  protected $customerContactName; // string [a-zA-Z0-9](0,100)
  protected $customerEmail;       // string [a-zA-Z0-9](0,80)
  protected $customerPhone;       // string [a-zA-Z0-9](0,17)
  protected $customerMobilePhone; // string [a-zA-Z0-9](0,17)
  protected $customerPreAlert;    // int [0|1] - Non utilisé
  protected $printAsSender;       // string [Y|N]

  public function __construct(Array $params=array()){
    $this->customerCivility    = isset($params['customerCivility']) ? $params['customerCivility'] : '1';
    $this->customerName        = isset($params['customerName']) ? $params['customerName'] : '';
    $this->customerName2       = isset($params['customerName2']) ? $params['customerName2'] : '';
    $this->customerAdress1     = isset($params['customerAdress1']) ? $params['customerAdress1'] : '';
    $this->customerAdress2     = isset($params['customerAdress2']) ? $params['customerAdress2'] : '';
    $this->customerZipCode     = isset($params['customerZipCode']) ? $params['customerZipCode'] : '';
    $this->customerCity        = isset($params['customerCity']) ? $params['customerCity'] : '';
    $this->customerCountry     = isset($params['customerCountry']) ? $params['customerCountry'] : '';
    $this->customerContactName = isset($params['customerContactName']) ? $params['customerContactName'] : '';
    $this->customerEmail       = isset($params['customerEmail']) ? $params['customerEmail'] : '';
    $this->customerPhone       = isset($params['customerPhone']) ? $params['customerPhone'] : '';
    $this->customerMobilePhone = isset($params['customerMobilePhone']) ? $params['customerMobilePhone'] : '';
    $this->customerPreAlert    = isset($params['customerPreAlert']) ? $params['customerPreAlert'] : '0';
    $this->printAsSender       = isset($params['printAsSender']) ? $params['printAsSender'] : 'N';
  }
}
