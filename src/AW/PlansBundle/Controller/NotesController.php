<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Note;
use AW\PlansBundle\Form\DeadLineNoteType;

class NotesController extends Controller
{
  /**
   * @ParamConverter("commande", options={"repository_method" = "findWithNotes"})
   */
  public function listAction(Request $request, Commande $commande, $type)
  {
    $private = $type == 'private' ? true : false;
    if($private){
      $this->denyAccessUnlessGranted('VIEW_NOTE_PRIVATE', $commande);

      if($this->isGranted('EDIT_INTERNAL_USER', $commande)){
        $note = new Note();
        $note->setDeadline(new \DateTime());
        $form = $this->get('form.factory')->create(DeadLineNoteType::class, $note);
        $form->handleRequest($request);

        if($request->isMethod('POST') and $form->isValid()){
          $em = $this
            ->getDoctrine()
            ->getManager()
          ;

          $note
            ->setPrivate(true)
            ->setCommande($commande)
            ->setUser($this->getUser())
          ;

          $em->persist($note);
          $em->flush();

          return $this->redirectToRoute('aw_plans_notes', array('id' => $commande->getId(), 'type' => $type));
        }
      }
    }else{
      $this->denyAccessUnlessGranted('VIEW_NOTE_PUBLIC', $commande);
    }

    return $this->render('AWPlansBundle:Notes:view.html.twig', array(
      'commande' => $commande,
      'type' => $type,
      'form' => isset($form) ? $form->createView() : null
    ));
  }

  public function addAction(Commande $commande, Request $request, $type, $_format)
  {
    $private = $type == 'private' ? true : false;
    if($private){
      $this->denyAccessUnlessGranted('VIEW_NOTE_PRIVATE', $commande);
    }else{
      $this->denyAccessUnlessGranted('VIEW_NOTE_PUBLIC', $commande);
    }

    if($request->isMethod('POST')){
      $note = new Note();
      $note
        ->setMessage($request->get('message'))
        ->setPrivate($private)
        ->setCommande($commande)
        ->setUser($this->getUser())
      ;

      if(!$this->getUser()->getSociete()){
        $note->setSeen(true);
      }

      $em = $this
        ->getDoctrine()
        ->getManager()
      ;
      $em->persist($note);
      $em->flush();

      $message = (new \Swift_Message())
        ->setSubject('CONSILIUM // '.$commande->getSociete()->getName().' - '.$commande->getSite().' - '.$commande->getRef().' // nouvelle note de '.$note->getUser()->getFullName())
        ->setFrom($this->getParameter('email_plans'))
        ->setTo($this->getParameter('email_plans'))
        ->setBody($note->getMessage())
      ;

      if($this->getUser()->getSociete()){
        $message->addTo(
          $this->getUser()->getEmail(),
          $this->getUser()->getFullName()
        );
      }elseif($note->isPublic() and $commande->getUserContact()){
        $message->addTo(
          $commande->getUserContact()->getEmail(),
          $commande->getUserContact()->getFullName()
        );
      }

      try{
        $this->get('mailer')->send($message);
      }catch(\Exception $e){}

      $return = array(
        'id' => $note->getId(),
        'message' => $note->getMessage(),
        'date' => $note->getDate()->format('d/m/Y H:i:s'),
        'userid' => $note->getUser()->getId(),
        'username' => $note->getUser()->getFullName()
      );

      return new JsonResponse($return);
    }

    return new JsonResponse();
  }

  public function lastAction(Commande $commande, Request $request, $type, $_format)
  {
    $private = $type == 'private' ? true : false;
    if($private){
      $this->denyAccessUnlessGranted('VIEW_NOTE_PRIVATE', $commande);
    }else{
      $this->denyAccessUnlessGranted('VIEW_NOTE_PUBLIC', $commande);
    }

    if($request->isMethod('POST')){
      $lastId = $request->get('lastid');
      $notes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Note')
        ->getLast($commande, $lastId, $private)
      ;

      $return = array();
      foreach($notes as $note){
        $return[] = array(
          'id' => $note->getId(),
          'message' => $note->getMessage(),
          'date' => $note->getDate()->format('d/m/Y H:i:s'),
          'userid' => $note->getUser()->getId(),
          'username' => $note->getUser()->getFullName(),
          'deadline' => $note->getDeadline() ? $note->getDeadline()->format('d/m/Y H:i:s') : null
        );
      }

      return new JsonResponse($return);
    }

    return new JsonResponse();
  }

  public function markedSeenAction(Note $note)
  {
    if($this->getUser()->getSociete() or !$this->isGranted('webappli.cmdplan.modify')){
      throw new \Exception('Action non autorisée');
    }

    $note->setSeen(true);
    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_view', array('id' => $note->getCommande()->getId()));
  }
}
