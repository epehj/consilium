<?php

namespace AW\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
  public function listAction($_format)
  {
    if($_format == 'js'){
      return $this->render('AWCoreBundle:Notification:list.js.twig');
    }

    if($_format == 'json'){
      $data = array(
        'count' => 0,
        'list' => array()
      );

      if(!$this->getUser()->getSociete() and $this->isGranted('webappli.cmdplan.modify')){
        // Rappels
        $notes = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('AWPlansBundle:Note')
          ->getListDeadlineNotSeen()
        ;

        if(!empty($notes)){
          $deadlines = array(
            'title' => 'Rappels échus',
            'countLabel' => count($notes).' rappel(s)',
            'list' => array()
          );
          foreach($notes as $note){
            $deadlines['list'][] = array(
              'message' => $note->getCommande()->getRef().' - '.$note->getMessage(),
              'date' => $note->getDeadline()->format('d/m/Y'),
              'time' => $note->getDeadline()->format('H:i'),
              'linkto' => $this->generateUrl('aw_plans_view', array('id' => $note->getCommande()->getId())),
              'deletelink' => $this->generateUrl('aw_plans_notes_marked_seen', array('id' => $note->getId()))
            );
          }

          $data['count'] += count($notes);
          $data['list'][] = $deadlines;
        }

        // Notes publiques
        $notes = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('AWPlansBundle:Note')
          ->getPublicListNotSeen()
        ;

        if(!empty($notes)){
          $deadlines = array(
            'title' => 'Notes publiques',
            'countLabel' => count($notes).' note(s)',
            'list' => array()
          );
          foreach($notes as $note){
            $deadlines['list'][] = array(
              'message' => $note->getCommande()->getRef().' - '.$note->getMessage(),
              'date' => $note->getDate()->format('d/m/Y'),
              'time' => $note->getDate()->format('H:i'),
              'linkto' => $this->generateUrl('aw_plans_notes', array('type' => 'public', 'id' => $note->getCommande()->getId())),
              'deletelink' => $this->generateUrl('aw_plans_notes_marked_seen', array('id' => $note->getId()))
            );
          }

          $data['count'] += count($notes);
          $data['list'][] = $deadlines;
        }
      }

      return new JsonResponse($data);
    }
  }
}
