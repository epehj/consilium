<?php

namespace AW\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\CoreBundle\Entity\ModelMail;
use AW\CoreBundle\Form\ModelMailType;

class AdminController extends Controller
{
  public function mailsAction(Request $request)
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $mails = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWCoreBundle:ModelMail')
      ->findAll()
    ;

    return $this->render('AWCoreBundle:Admin:mails.html.twig', array(
      'mails' => $mails
    ));
  }

  public function mailAction(Request $request, ModelMail $mail)
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $form = $this->get('form.factory')->create(ModelMailType::class, $mail);
    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $this
        ->getDoctrine()
        ->getManager()
        ->flush()
      ;

      return $this->redirectToRoute('aw_core_config_mail', array('id' => $mail->getId()));
    }

    return $this->render('AWCoreBundle:Admin:mail.html.twig', array(
      'form' => $form->createView()
    ));
  }
}
