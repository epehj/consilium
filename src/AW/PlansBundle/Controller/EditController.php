<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\CommandeDet;
use AW\PlansBundle\Form\CommandeSiteType;
use AW\PlansBundle\Form\CommandeShippingType;
use AW\PlansBundle\Form\CommandeEditType;
use AW\PlansBundle\Form\CommandeDetType;

class EditController extends Controller
{
  /**
   * @ParamConverter("commande")
   */
  public function editAction(Request $request, Commande $commande, $element)
  {
    if($element == 'commande'){
      $this->denyAccessUnlessGranted('EDIT', $commande);
      $oldStatus = $commande->getStatus();
      $form = $this->get('form.factory')->create(CommandeEditType::class, $commande);
    }elseif($element == 'site'){
      $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $commande);
      $form = $this->get('form.factory')->create(CommandeSiteType::class, $commande);
    }elseif($element == 'shipping'){
      $this->denyAccessUnlessGranted('EDIT', $commande);
      $form = $this->get('form.factory')->create(CommandeShippingType::class, $commande);
    }else{
      throw new \Exception('Element à éditer inconnu');
    }

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      // si changement de status de la commande
      if($element == 'commande' and $oldStatus != $commande->getStatus()){
        $newStatus = $commande->getStatus();

        // Ne pas autoriser de passer directement en BAT
        if($newStatus == Commande::STATUS_BAT or
          $newStatus == Commande::STATUS_BAT_MODIF or
          $newStatus == Commande::STATUS_BAT_VALIDATED
        ){
          throw new \Exception('Changement de statut de BATs non autorisé');
        }

        // sinon passer par la fonction de changement de status
        $commande->updateStatus($this->getUser(), $newStatus);

        // supprimer le BAT en cours
        if($oldStatus == Commande::STATUS_BAT){
          $bat = $em
            ->getRepository('AWPlansBundle:Bat')
            ->getCurrent($commande)
          ;

          if($bat !== null){
            $em
              ->remove($bat)
            ;
          }
        }

        // modifier le dernier BAT validé et le mettre en modification
        if($oldStatus >= Commande::STATUS_BAT_VALIDATED and $newStatus < Commande::STATUS_BAT_VALIDATED){
          $bat = $em
            ->getRepository('AWPlansBundle:Bat')
            ->getValidated($commande)
          ;

          if($bat !== null){
            $userBAT = $bat->getUserValidation();

            $bat
              ->setDateModification(new \DateTime())
              ->setUserModification($userBAT)
              ->setDateValidation(null)
              ->setUserValidation(null)
            ;
          }
        }

        // supprimer la production en cours
        $production = $em
          ->getRepository('AWPlansBundle:Production')
          ->getCurrent($commande)
        ;

        if($production !== null){
          $em
            ->remove($production)
          ;
        }
      }

      $em->flush();

      return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
    }

    return $this->render('AWPlansBundle:Edit:'.$element.'.html.twig', array(
      'form' => $form->createView(),
      'commande' => $commande
    ));
  }

  /**
   * @ParamConverter("commande")
   */
  public function receiveAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('RECEIVE', $commande);

    $commande->updateStatus($this->getUser(), Commande::STATUS_RECEIVED);

    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
  }

  /**
   * @ParamConverter("commande")
   */
  public function cancelAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $commande);

    $commande->updateStatus($this->getUser(), Commande::STATUS_CANCELED);

    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
  }

  /**
   * @ParamConverter("commande")
   */
  public function reopenAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('REOPEN', $commande);

    $commande->updateStatus($this->getUser(), Commande::STATUS_VALIDATED);

    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
  }

  /**
   * @ParamConverter("commande")
   */
  public function closedAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('CLOSED', $commande);

    $commande->updateStatus($this->getUser(), Commande::STATUS_CLOSED);

    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
  }

  /**
   * @ParamConverter("commande")
   */
  public function cloneAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('CLONE', $commande);

    $new = clone $commande;
    $new->setUserCreation($this->getUser());

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $em->persist($new);
    $em->flush();

    foreach($commande->getListDet() as $det){
      $det = clone $det;
      $det->setCommande($new);
      $em->persist($det);
    }
    $em->flush();

    return $this->redirectToRoute('aw_plans_view', array('id' => $new->getId()));
  }

  /**
   * @ParamConverter("commande")
   */
  public function deleteAction(Request $request, Commande $commande)
  {
    $this->denyAccessUnlessGranted('DELETE', $commande);

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $em->remove($commande);
    $em->flush();

    $this->addFlash('success', 'La commande '.$commande->getRef().' a été supprimée!');

    return $this->redirectToRoute('aw_plans_list');
  }

  /**
   * @ParamConverter("commandeDet", options={"repository_method" = "findWithCommande"})
   */
  public function editDetAction(Request $request, CommandeDet $det)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $det->getCommande());

    $form = $this->get('form.factory')->create(CommandeDetType::class, $det);

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      $em->flush();

      return $this->redirectToRoute('aw_plans_view', array('id' => $det->getCommande()->getId()));
    }

    return $this->render('AWPlansBundle:Edit:det.html.twig', array(
      'form' => $form->createView(),
      'commande' => $det->getCommande()
    ));
  }

  /**
   * @ParamConverter("commande")
   */
  public function addDetAction(Request $request, Commande $commande, $type)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $commande);

    $det = new CommandeDet();
    $det->setType(strtoupper($type));

    $form = $this->get('form.factory')->create(CommandeDetType::class, $det);
    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $det->setCommande($commande);

      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      $em->persist($det);
      $em->flush();

      return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
    }

    return $this->render('AWPlansBundle:Edit:det.html.twig', array(
      'form' => $form->createView(),
      'commande' => $commande
    ));
  }

  /**
   * @ParamConverter("commandeDet", options={"repository_method" = "findWithCommande"})
   */
  public function deleteDetAction(CommandeDet $det)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $det->getCommande());

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;
    $em->remove($det);
    $em->flush();

    return $this->redirectToRoute('aw_plans_view', array('id' => $det->getCommande()->getId()));
  }
}
