<?php

namespace AW\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class DefaultController extends Controller
{
  public function profileAction()
  {
    return $this->render('AWUserBundle:Default:profile.html.twig');
  }

  public function resetPasswordAction(Request $request)
  {
    $data = array(
      'plainPassword' => ''
    );
    $builder = $this->createFormBuilder($data, array('translation_domain' => false));
    $form = $builder
      ->add('plainPassword', RepeatedType::class, array(
        'type' => PasswordType::class,
        'invalid_message' => 'Les deux mot de passe doivent être identiques',
        'first_options'  => array('label' => 'Nouveau mot de passe'),
        'second_options' => array('label' => 'Resaissir votre nouveau mot de passe'),
      ))
      ->getForm()
    ;

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $data = $form->getData();

      $passCrypted = $this
        ->get('security.password_encoder')
        ->encodePassword($this->getUser(), $data['plainPassword'])
      ;

      $this
        ->getUser()
        ->setPassCrypted($passCrypted)
      ;

      $this
        ->getDoctrine()
        ->getManager()
        ->flush()
      ;

      $this->addFlash('success', 'Votre mot de passe a été modifié');

      return $this->redirectToRoute('aw_user_profile');
    }

    return $this->render('AWUserBundle:Default:resetPassword.html.twig', array(
      'form' => $form->createView()
    ));
  }

  public function switchAction()
  {
    $this->denyAccessUnlessGranted('ROLE_ALLOWED_TO_SWITCH');

    $users = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:User')
      ->getActive()
    ;

    return $this->render('AWUserBundle:Default:switch.html.twig', array(
      'users' => $users
    ));
  }
}
