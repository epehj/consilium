<?php

namespace AW\DoliBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Api
{
  private $baseUrl;
  private $apiKey;

  public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, $baseUrl)
  {
    $user = $tokenStorage->getToken()->getUser();
    if(empty($user->getApiKey())){
      $apiKey = md5(mt_rand());
      $user->setApiKey($apiKey);
      $em->flush();
    }

    $this->apiKey = $user->getApiKey();
    $this->baseUrl = $baseUrl;
  }

  # see https://wiki.dolibarr.org/index.php/Module_Web_Services_REST_(developer)#Use
  private function call($path, $method = 'GET', $data = null)
  {
    $curl = curl_init();
    $httpheader = array(
      'DOLAPIKEY: '.$this->apiKey,
      'Content-Type: application/json'
    );
    $url = $this->baseUrl.$path;

    switch($method){
      case 'POST':
        curl_setopt($curl, CURLOPT_POST, 1);
        break;
      case 'PUT':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        break;
    }

    if($data){
      if(in_array($method, array('GET', 'DELETE'))){
        $url .= '?'.http_build_query($data);
      }else{
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      }
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);

    $result = curl_exec($curl);
    curl_close($curl);

    return json_decode($result);
  }

  public function get($path, $data = null)
  {
    return $this->call($path, 'GET', $data);
  }

  public function post($path, $data = null)
  {
    return $this->call($path, 'POST', $data);
  }

  public function put($path, $data = null)
  {
    return $this->call($path, 'PUT', $data);
  }

  public function delete($path, $data = null)
  {
    return $this->call($path, 'DELETE', $data);
  }
}
