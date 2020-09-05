<?php

namespace AW\TNTBundle\Service;

class WebService extends \SoapClient
{
  public function __construct($username, $password)
  {
    $options = array(
      'trace' => 1,
      'stream_context' => stream_context_create(array(
        'http' => array(
          'user_agent' => 'PHP/SOAP',
          'accept' => 'application/xml'
        )
      ))
    );
    parent::__construct('https://www.tnt.fr/service/?wsdl', $options);

    $authheader = sprintf('
<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
  <wsse:UsernameToken>
    <wsse:Username>%s</wsse:Username>
    <wsse:Password>%s</wsse:Password>
 </wsse:UsernameToken>
</wsse:Security>', htmlspecialchars($username), htmlspecialchars($password));
    $authvars = new \SoapVar($authheader, XSD_ANYXML);
    $header = new \SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $authvars);

    $this->__setSoapHeaders(array($header));
  }

  public function expeditionCreation($parameters)
  {
    return parent::expeditionCreation(array('parameters' => $parameters));
  }
}
