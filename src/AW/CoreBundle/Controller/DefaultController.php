<?php

namespace AW\CoreBundle\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
  public function indexAction(Request $request)
  {
    if($this->getUser()->getSociete()){
      return $this->redirectToRoute('aw_shop_homepage');
    }elseif($this->isGranted('webappli.cmdplan.see')){
      return $this->redirectToRoute('aw_plans_list');
    }elseif($this->isGranted('webappli.cmdplan.seeprod')){
      return $this->redirectToRoute('aw_plans_production_list');
    }elseif($this->isGranted('webappli.cmdplan.see_expedition')){
      return $this->redirectToRoute('aw_plans_shipping_receipt');
    }else{
      throw new \Exception('Accès non defini');
    }
  }
}
