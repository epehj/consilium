<?php

namespace AW\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AW\UserBundle\Entity\UserParam;

class ParamController extends Controller
{
  public function plansDataTablesStateAction(Request $request)
  {
    if($request->isMethod('POST')){
      $data = array(
        'time' => $request->get('time'),
        'start' => $request->get('start'),
        'length' => $request->get('length'),
        'order' => $request->get('order'),
        'search' => $request->get('search'),
        'columns' => $request->get('columns')
      );

      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      if($this->getUser()->getParam()){
        $userParam = $this->getUser()->getParam();
      }else{
        $userParam = new UserParam();
        $userParam->setUser($this->getUser());

        $em->persist($userParam);
      }

      $userParam->setPlansDataTablesState($data);
      $em->flush();

      return new JsonResponse(array('save' => true));
    }else{
      if($this->getUser()->getParam()){
        $data = $this
          ->getUser()
          ->getParam()
          ->getPlansDataTablesState()
        ;
      }else{
        $data = array();
      }

      return new JsonResponse($data);
    }
  }

  public function infosNewsUpdateAction(Request $request, $key)
  {
    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    if($this->getUser()->getParam()){
      $userParam = $this->getUser()->getParam();
    }else{
      $userParam = new UserParam();
      $userParam->setUser($this->getUser());

      $em->persist($userParam);
    }

    if($userParam->getInfosNews()){
      $data = $userParam->getInfosNews();
      $data[$key] = true;
    }else{
      $data = array($key => true);
    }

    $this
      ->getUser()
      ->getParam()
      ->setInfosNews($data)
    ;

    $em->flush();

    return new JsonResponse($data);
  }
}
