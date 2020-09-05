<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;

use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttendeesType;

use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\CalendarItemCreateOrDeleteOperationType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Enumeration\RoutingType;

use \jamesiarmes\PhpEws\Type\AttendeeType;
use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\CalendarItemType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Form\CommandeReleveNoteType;
use AW\PlansBundle\Entity\Mail;

class RelevesController extends Controller
{
  public function listAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.releves');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getListReleves($start, $length, $columns, $orders)
      ;

      $data = array();
      foreach($commandes as $commande){
        $data[] = array(
          'id' => $commande->getId(),
          'ref' => $commande->getRef(),
          'societe' => $commande->getSociete()->getName(),
          'site' => $commande->getSite(),
          'address1' => $commande->getAddress1(),
          'address2' => $commande->getAddress2(),
          'zip' => $commande->getZip(),
          'town' => $commande->getTown(),
          'date' => $commande->getDateCreation()->format('d/m/Y'),
          'urgent' => $commande->getUrgent(),
          'statusLabel' => $commande->getReleveStatusLabel(),
          'url' => $this->generateUrl('aw_plans_releves_view', array('id' => $commande->getId()))
        );
      }

      $response = array(
        'draw' => $draw,
        'recordsTotal' => count($commandes),
        'recordsFiltered' => count($commandes),
        'data' => $data
      );
      return new JsonResponse($response);
    }

    return $this->render('AWPlansBundle:Releves:list.html.twig');
  }

  public function mapsAction($markers)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.releves');

    if($markers == 'r'){
      $markersReleve = true;
      $markersPose = false;
    }elseif($markers == 'p'){
      $markersReleve = false;
      $markersPose = true;
    }else{
      $markersReleve = true;
      $markersPose = true;
    }

    $commandes = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Commande')
      ->findWaitingRelevePose($markersReleve, $markersPose)
    ;

    return $this->render('AWPlansBundle:Releves:maps.html.twig', array(
      'markers' => $markers,
      'commandes' => $commandes,
      'google_api_key' => $this->getParameter('google_public_api_key')
    ));
  }

  /**
   * @ParamConverter("commande", options={"repository_method" = "findWithDetail"})
   */
  public function viewAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW_RELEVE', $commande);

    $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation';
    if(!file_exists($dir)){
      mkdir($dir, 0777, true);
    }

    $finder = new Finder();
    $finder
      ->files()
      ->notName('logo.*')
      ->notName('commande.pdf')
      ->notName('logo_client.*')
      ->in($dir)
    ;

    return $this->render('AWPlansBundle:Releves:view.html.twig', array(
      'commande' => $commande,
      'finder'   => $finder
    ));
  }

  public function uploadAction(Request $request, Commande $commande)
  {
    $this->denyAccessUnlessGranted('ADD_RELEVE', $commande);

    if($request->isMethod('POST')){
      $response = array();
      $data = array('files' => null);
      $builder = $this->createFormBuilder($data, array('csrf_protection' => false));
      $form = $builder
        ->add('files', FileType::class, array('multiple' => true))
        ->getForm();

      if($form->handleRequest($request)->isValid()){
        $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation';
        $data = $form->getData();
        foreach($data['files'] as $file){
          $filename = $file->getClientOriginalName();
          while(file_exists($dir.'/'.$filename)){
            $filename = $this->get('aw.core.service.utils')->filenameCounter($filename);
          }

          $mimetype = $file->getMimeType();

          $file
            ->move($dir, $filename);

          $response[] = array(
            'url' => $this->generateUrl('aw_plans_releves_download', array('id' => $commande->getId(), 'file' => $filename)),
            'name' => $filename,
            'type' => $mimetype,
            'size' => filesize($dir.'/'.$filename),
            'delete_url' => $this->generateUrl('aw_plans_releves_delete', array('id' => $commande->getId(), 'file' => $filename)),
          );
        }
        return new JsonResponse($response);
      }else{
        $message = $form->getErrors() ? $form->getErrors() : 'Échec de téléchargement.';
        throw new \Exception($message);
      }
    }

    return new JsonResponse();
  }

  public function downloadAction(Commande $commande, $file)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.releves');

    $filepath = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  public function deleteAction(Commande $commande, $file)
  {
    $this->denyAccessUnlessGranted('ADD_RELEVE', $commande);

    $filepath = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    if(@unlink($filepath)){
      $this->addFlash('success', 'Fichier '.$file.' supprimé !');
    }else{
      $this->addFlash('error', 'Impossible de supprimer le fichier '.$file.' !');
    }

    return $this->redirectToRoute('aw_plans_releves_view', array('id' => $commande->getId()));
  }

  public function updateAction(Commande $commande, $status)
  {
    switch($status){
      case Commande::RELEVE_STATUS_TERMINE:
        $this->denyAccessUnlessGranted('ADD_RELEVE', $commande);
        $commande
          ->setProduction(1)
          ->setDateProduction(
            new \DateTime()
          )
        ;
        break;

      case Commande::RELEVE_STATUS_EN_ATTENTE:
        $this->denyAccessUnlessGranted('STACK_RELEVE', $commande);
        $commande->setProduction(0);
        break;

      case Commande::RELEVE_ANOMALIE:
        $this->denyAccessUnlessGranted('RELEVE_ANOMALIE', $commande);
        break;

      case Commande::POSE_STATUS_EN_ATTENTE:
        $this->denyAccessUnlessGranted('STACK_POSE', $commande);
        break;

      default:
        throw new \Exception('Statut relevé inconnu');
    }

    $commande->setReleveStatus($status);

    $this
      ->getDoctrine()
      ->getManager()
      ->flush()
    ;

    return $this->redirectToRoute('aw_plans_releves_view', array('id' => $commande->getId()));
  }

  /**
   * @ParamConverter("commande", options={"repository_method" = "findWithDetail"})
   */
  public function poseEndAction(Request $request, Commande $commande, $_format)
  {
    $this->denyAccessUnlessGranted('POSE_DONE', $commande);

    $fs = new Filesystem();
    $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/validation';
    if(!file_exists($dir)){
      $fs->mkdir($dir);
    }

    if($request->isMethod('POST')){
      $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
      $builder
        ->add('signature_technicien', TextType::class)
        ->add('signature_client', TextType::class)
        ->add('name_client', TextType::class)
      ;

      $form = $builder->getForm();
      if($form->handleRequest($request)->isValid()){
        $data = $form->getData();

        foreach($data as $name => $imgText){
          if($name == 'name_client' or empty($imgText)){
            continue;
          }

          $imgData = base64_decode($imgText);
          if(file_put_contents($dir.'/'.$name.'.png', $imgData) === false){
            throw new \Exception('Échec de sauvegarde de la signature');
          }
        }

        $this->get('aw_plans.pdf.generator')->generatePosePDF($commande, $data['name_client']);

        $commande
          ->setReleveStatus(Commande::POSE_STATUS_TERMINE)
          ->updateStatus($this->getUser(), Commande::STATUS_CLOSED)
        ;

        $em = $this
          ->getDoctrine()
          ->getManager()
        ;

        $em
          ->flush()
        ;

        $model = $em
          ->getRepository('AWCoreBundle:ModelMail')
          ->findOneByType('POSE_CONFIRM')
        ;

        if($model !== null){
          $em->detach($model);

          try{
            $model->render($this->get('twig'), array('commande' => $commande));

            if($commande->getUserContact() and $commande->getUserContact()->getEmail()){
              $to = $commande->getUserContact()->getFullName().' <'.$commande->getUserContact()->getEmail().'>, '.$this->getParameter('email_plans');
            }else{
              $to = $this->getParameter('email_plans');
            }

            if($commande->getSociete()->getInfosPlans() and $commande->getSociete()->getInfosPlans()->getEmailBLPose()){
              $to .= ', '.$commande->getSociete()->getInfosPlans()->getEmailBLPose();
            }

            $mail = new Mail();
            $mail
              ->setCommande($commande)
              ->setAddressTo($to)
              ->setSubject($model->getSubject())
              ->setMessage($model->getContent())
            ;

            $message = (new \Swift_Message())
              ->setFrom($this->getParameter('email_plans'))
              ->setTo($mail->getAddressToFormated())
              ->setSubject($mail->getSubject())
              ->setBody($mail->getMessage(), 'text/html')
            ;

            if(!$this->get('kernel')->isDebug()){
              $message->setBcc($this->getParameter('email_bcc'));
            }

            $bl = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/validation/BL-POSE.pdf';
            $message->attach(\Swift_Attachment::fromPath($bl));

            $this->get('mailer')->send($message);

            $em->persist($mail);
            $em->flush();
          }catch(\Exception $e){}
        }

        return new JsonResponse(array('success' => true));
      }

      $message = $form->getErrors() ? $form->getErrors() : 'Échec de téléchargement.';
      throw new \Exception($message);
    }

    $finder = new Finder();
    $finder
      ->files()
      ->in($dir)
    ;

    return $this->render('AWPlansBundle:Releves:poseEnd.html.twig', array(
      'commande' => $commande,
      'finder' => $finder
    ));
  }

  public function uploadPoseAction(Request $request, Commande $commande, $file)
  {
    $response = array();

    $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/validation';

    switch($request->getMethod()){
      case 'POST':
        $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
        $form = $builder
          ->add('files', FileType::class, array('multiple' => true))
          ->getForm()
        ;

        $form->handleRequest($request);

        $fs = new Filesystem();
        if(!is_dir($dir)){
          $fs->remove($dir);
        }
        if(!file_exists($dir)){
          $fs->mkdir($dir);
        }

        $data = $form->getData();
        foreach($data['files'] as $file){
          $filename = 'PHOTO_POSE'.($file->guessExtension() ? '.'.$file->guessExtension() : '');
          while(file_exists($dir.'/'.$filename)){
            $filename = $this->get('aw.core.service.utils')->filenameCounter($filename);
          }

          $file->move($dir, $filename);

          $response[] = array(
            'name' => $filename,
            'url' => $this->generateUrl('aw_plans_pose_upload', array('id' => $commande->getId(), 'file' => $filename))
          );
        }
        return new JsonResponse($response);

      case 'GET':
        $filepath = $dir.'/'.$file;
        if(!file_exists($filepath) or !is_file($filepath)){
          throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
        }

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;

      case 'DELETE':
        $filepath = $dir.'/'.$file;
        if(!file_exists($filepath) or !is_file($filepath)){
          throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
        }

        if(!@unlink($filepath)){
          throw new \Exception('Échec de suppression du fichier !');
        }

        return new JsonResponse(array());
      default:
        throw new \Exception('Action inconnue !');
    }
  }

  public function addRDVAction(Request $request, Commande $commande)
  {
    $form = $this->get('form.factory')->create(CommandeReleveNoteType::class, $commande);

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $startEvent = $form->get('date')->getData();
      $endEvent = clone $startEvent;
      $endEvent->modify('+ 1 hour');

      /*
       * Ajout d'événement dans l'agenda Exchange
       * Plus d'infos https://github.com/jamesiarmes/php-ews/blob/master/examples/event/create.php
       */
      $host = $this->getParameter('exchange_host');
      $username = $this->getParameter('exchange_plans_username');
      $password = $this->getParameter('exchange_plans_password');
      $client = new Client($host, $username, $password, Client::VERSION_2007);

      // Build the request
      $request = new CreateItemType();
      $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
      $request->Items = new NonEmptyArrayOfAllItemsType();

      // Build the event to be added
      $event = new CalendarItemType();
      $event->RequiredAttendees = new NonEmptyArrayOfAttendeesType();
      $event->Start = $startEvent->format('c');
      $event->End = $endEvent->format('c');
      $event->Subject = $commande->getRef().' : '.$commande->getReleveNote();
      $event->Location = $commande->getAddress1().' '.$commande->getZip().' '.$commande->getTown();

      // Set the event body
      $event->Body = new BodyType();
      $event->Body->_ = '';
      $event->Body->BodyType = BodyTypeType::TEXT;

      // add attendee
      $attendee = new AttendeeType();
      $attendee->Mailbox = new EmailAddressType();
      $attendee->Mailbox->EmailAddress = $commande->getReleveUser()->getEmail();
      $attendee->Mailbox->Name = $commande->getReleveUser()->getFullName();
      $attendee->Mailbox->RoutingType = RoutingType::SMTP;
      $event->RequiredAttendees->Attendee[] = $attendee;

      $request->Items->CalendarItem[] = $event;

      $response = $client->CreateItem($request);

      $hasError = false;
      $responseMessages = $response->ResponseMessages->CreateItemResponseMessage;
      foreach($responseMessages as $responseMessage){
        if($responseMessage->ResponseClass != ResponseClassType::SUCCESS){
          $hasError = true;
          $this->addFlash('error', "Échec d'ajout du rendez-vous dans l'agenda : ".$responseMessage->MessageText." (code: ".$responseMessage->ResponseCode.")");
        }
      }

      if(!$hasError){
        $em = $this
          ->getDoctrine()
          ->getManager()
        ;

        $em->flush();

        $this->addFlash('success', "Rendez-vous ajouté à l'agenda du releveur/poseur!");

        return $this->redirectToRoute('aw_plans_releves_view', array('id' => $commande->getId()));
      }
    }

    return $this->render('AWPlansBundle:Releves:addRDV.html.twig', array(
      'commande' => $commande,
      'form' => $form->createView()
    ));
  }
}
