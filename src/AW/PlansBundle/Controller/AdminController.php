<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AW\PlansBundle\Form\ConsigneType;

class AdminController extends Controller
{
  public function consigneAction(Request $request)
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $consigne = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Consigne')
      ->find(1)
    ;

    $form = $this->get('form.factory')->create(ConsigneType::class, $consigne);
    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $this
        ->getDoctrine()
        ->getManager()
        ->flush()
      ;

      return $this->redirectToRoute('aw_plans_config_consigne');
    }

    return $this->render('AWPlansBundle:Admin:consigne.html.twig', array(
      'form' => $form->createView()
    ));
  }
}
