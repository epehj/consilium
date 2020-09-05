<?php

namespace AW\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AW\DoliBundle\Entity\Contact;
use AW\DoliBundle\Entity\ContactExtrafields;
use AW\DoliBundle\Entity\Country;
use AW\DoliBundle\Form\ContactType;

class AddressController extends Controller
{
  public function listAction()
  {
    if(!$this->getUser()->getSociete()){
      throw new \Exception('Uniquement les clients peuvent ajouter une adresse');
    }

    $contacts = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:Contact')
      ->getSocieteShippingContacts($this->getUser()->getSociete())
    ;

    return $this->render('AWShopBundle:Address:list.html.twig', array(
      'contacts' => $contacts
    ));
  }

  public function addAction(Request $request)
  {
    if(!$this->getUser()->getSociete()){
      throw new \Exception('Uniquement les clients peuvent ajouter une adresse');
    }

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $defaultCountry = $em
      ->getRepository('AWDoliBundle:Country')
      ->findOneByCode('FR')
    ;

    $contact = new Contact();
    $contact
      ->setSociete($this->getUser()->getSociete())
      ->setUserCreation($this->getUser())
      ->setCountry($defaultCountry)
    ;

    $form = $this->get('form.factory')->create(ContactType::class, $contact);

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $extrafields = new ContactExtrafields();
      $extrafields
        ->setTypeContact('2')
        ->setContact($contact)
      ;

      $em->persist($contact);
      $em->persist($extrafields);
      $em->flush();

      return $this->redirectToRoute('aw_shop_address_list');
    }

    return $this->render('AWShopBundle:Address:add.html.twig', array(
      'form' => $form->createView()
    ));
  }
}
