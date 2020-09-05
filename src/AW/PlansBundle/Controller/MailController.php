<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Mail;
use AW\PlansBundle\Entity\Bat;
use AW\PlansBundle\Form\MailType;

class MailController extends Controller
{
  private function getLastProduction(Commande $commande)
  {
    $dir = $this->getParameter('documents_dir').'/prodplan/'.$commande->getDir();
    if(file_exists($dir) and is_dir($dir)){
      $lastProdDir = null;
      $lastProd = 0;
      $finder = new Finder();
      foreach($finder->directories()->in($dir) as $file){ // recherche de la dernière production
        $dirName = explode('_', $file->getFilename(), 3);
        if(count($dirName) == 3 and $dirName[1] > $lastProd){
          $lastProdDir = $file;
          $lastProd = $dirName[1];
        }
      }

      if($lastProdDir){ // dernière production trouvée
        return $lastProdDir->getRealPath();
      }
    }

    return null;
  }

  /**
   * @ParamConverter("commande")
   */
  public function sendAction(Request $request, Commande $commande, $action)
  {
    switch($action){
      case 'validation':
        $this->denyAccessUnlessGranted('VALIDATE', $commande);
        $modelType = 'VALIDATION';
        $requiredAttachments = false;
        $subDir = 'creation';
        $newStatus = Commande::STATUS_VALIDATED;
        $flashMessage = 'Commande validée!';
        break;

      case 'bat':
        $this->denyAccessUnlessGranted('SEND_BAT', $commande);
        $requiredAttachments = false;
        $attachmentsSelectable = true;
        $subDir = 'modification';
        $newStatus = Commande::STATUS_BAT;
        $flashMessage = 'Commande en BAT client!';

        $lastProdDir = $this->getLastProduction($commande);

        if($commande->getContactBATEmail()){
          $modelType = 'BAT_ANONYMOUS';
          $to = $commande->getContactBATName().' <'.$commande->getContactBATEmail().'>';
        }else{
          $modelType = 'BAT';
          $to = $commande->getUserContact() ? $commande->getUserContact()->getFullName().' <'.$commande->getUserContact()->getEmail().'>' : '';
        }
        break;

      case 'modificationbat':
      case 'validationbat':
        $this->denyAccessUnlessGranted('MODIFY_BAT', $commande);
        $modelType = $action == 'modificationbat' ? 'BAT_MODIF' : 'BAT_VALIDE';
        $requiredAttachments = true;
        $subDir = $action == 'modificationbat' ? 'modification' : 'validation';
        $newStatus = $action == 'modificationbat' ? Commande::STATUS_BAT_MODIF : Commande::STATUS_BAT_VALIDATED;
        $flashMessage = $action == 'modificationbat' ? 'Commande en modification BAT!' : 'Commande en BAT validé!';

        if($commande->getUserContact()){
          $to = $commande->getUserContact()->getFullName().' <'.$commande->getUserContact()->getEmail().'>';
          if($this->getUser()->getSociete() and $this->getUser() != $commande->getUserContact()){
            $to .= ','.$this->getUser()->getFullName().' <'.$this->getUser()->getEmail().'>';
          }
        }elseif($this->getUser()->getSociete()){
          $to = $this->getUser()->getFullName().' <'.$this->getUser()->getEmail().'>';
        }

        if(isset($to)){
          $to = $to.', '.$this->getParameter('email_plans');
        }else{
          $to = $this->getParameter('email_plans');
        }
        break;

      case 'fabrication':
        $this->denyAccessUnlessGranted('SEND_PRINTER', $commande);
        $modelType = 'FABRICATION';
        $requiredAttachments = false;
        $attachmentsSelectable = true;
        $subDir = 'versionfinale';
        $newStatus = Commande::STATUS_EN_FABRICATION;
        $flashMessage = 'Commande en fabrication!';
        $lastProdDir = $this->getLastProduction($commande);
        break;

      default:
        throw $this->createNotFoundException('Action inconnue');
        break;
    }

    if(empty($to)){
      $to = $commande->getUserContact() ? $commande->getUserContact()->getFullName().' <'.$commande->getUserContact()->getEmail().'>' : '';
    }

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $model = $em
      ->getRepository('AWCoreBundle:ModelMail')
      ->findOneByType($modelType)
    ;
    $em->detach($model);
    $model->render($this->get('twig'), array('commande' => $commande));

    if(($action == 'modificationbat' or $action == 'validationbat') and $this->getUser()->getSociete()){
      $model->setContent('Merci de saisir ici un message si besoin et de joindre les fichiers nécessaires aux traitements de votre commande.');
    }

    $mail = new Mail();
    $mail
      ->setCommande($commande)
      ->setAddressTo($to)
      ->setSubject($model->getSubject())
      ->setMessage($model->getContent())
    ;

    $form = $this->get('form.factory')->create(MailType::class, $mail, array(
      'required_attachments' => $requiredAttachments,
      'attachments_selectable' => isset($attachmentsSelectable) ? $attachmentsSelectable : false
    ));

    if(isset($lastProdDir) and $lastProdDir !== null){
      $finder = new Finder();
      $finder->files()->name('*.pdf')->in($lastProdDir);
      $key = 0;
      foreach($finder as $file){
        $form
          ->get('attachments2')
          ->add($key, CheckboxType::class, array(
            'label' => $file->getFilename(),
            'data' => true,
            'required' => false
          ))
        ;
        $key++;
      }
    }

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      if($action == 'bat'){
        $filesUploaded = false;
        foreach($form->get('attachments2')->getData() as $checked){
          if($checked){
            $filesUploaded = true;
          }
        }

        if(!$filesUploaded and empty($form->get('attachments')->getData())){
          $this->addFlash('error', 'Merci de joindre un ou des fichiers');
          return $this->redirect($request->getUri());
        }

        $numbat = count($commande->getBats()) + 1;
        $bat = new Bat();
        $bat
          ->setNumero($numbat)
          ->setCommande($commande)
          ->setUserCreation($this->getUser())
        ;
        $em->persist($bat);
      }elseif($action == 'modificationbat' or $action == 'validationbat'){
        $bat = $em
          ->getRepository('AWPlansBundle:Bat')
          ->getCurrent($commande)
        ;

        if($action == 'modificationbat'){
          $bat
            ->setDateModification(new \DateTime())
            ->setUserModification($this->getUser())
          ;
        }else{
          $bat
            ->setDateValidation(new \DateTime())
            ->setUserValidation($this->getUser())
          ;
        }
      }elseif($action == 'fabrication'){
        $filesUploaded = false;
        foreach($form->get('attachments2')->getData() as $checked){
          if($checked){
            $filesUploaded = true;
          }
        }

        if(!$filesUploaded and empty($form->get('attachments')->getData())){
          $this->addFlash('error', 'Merci de joindre un ou des fichiers');
          return $this->redirect($request->getUri());
        }
      }

      if($action == 'bat' and $commande->getContactBATEmail()){
        $from = $this->getParameter('email_service_plans');
        $body = $mail->getMessage().$commande->getSociete()->getInfosPlans()->getSignatureBat();
      }else{
        $from = $this->getParameter('email_plans');

        if(!$this->getUser()->getSociete() and $this->getUser()->getSignature()){
          $body = $mail->getMessage().$this->getUser()->getSignature();
        }else{
          $body = $mail->getMessage().'<p>E-Mail automatique</p>';
        }
      }

      $message = (new \Swift_Message())
        ->setFrom($from)
        ->setTo($mail->getAddressToFormated())
        ->setSubject($mail->getSubject())
        ->setBody($body, 'text/html')
      ;

      /*if(!$this->get('kernel')->isDebug()){
        $message->setBcc($this->getParameter('email_bcc'));

        foreach($commande->getSociete()->getCommercials() as $commercial){
          if($commercial->getEmail()){
            $message->addBcc($commercial->getEmail());
          }
        }
      }*/

      $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/'.$subDir;
      foreach($form->get('attachments')->getData() as $attachment){
        $filename = $attachment->getClientOriginalName();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch($action){
          case 'bat':
            $filename = 'BAT'.(count($commande->getBats())+1).'-'.date('YmdHi').'.'.$ext;
            break;
          case 'modificationbat':
            $filename = 'BAT'.(count($commande->getBats())).'-MODIF-'.date('YmdHi').'.'.$ext;
            break;
          case 'validationbat':
            $filename = 'BAT'.(count($commande->getBats())).'-VALIDE-'.date('YmdHi').'.'.$ext;
            break;
        }

        while(file_exists($dir.'/'.$filename)){
          $filename = $this->get('aw.core.service.utils')->filenameCounter($filename);
        }

        $attachment->move($dir, $filename);
        $message->attach(\Swift_Attachment::fromPath($dir.'/'.$filename));
      }

      if($action == 'bat' or $action == 'fabrication'){
        $fs = new Filesystem();

        foreach($form->get('attachments2') as $child){
          if($child->getData()){
            $filename = $child->getConfig()->getOption('label');
            $newfilename = $filename;
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if($action == 'bat'){
              $newfilename = 'BAT'.(count($commande->getBats())+1).'-'.date('YmdHi').'.'.$ext;
            }

            while(file_exists($dir.'/'.$newfilename)){
              $newfilename = $this->get('aw.core.service.utils')->filenameCounter($newfilename);
            }

            $fs->copy($lastProdDir.'/'.$filename, $dir.'/'.$newfilename);
            $message->attach(\Swift_Attachment::fromPath($dir.'/'.$newfilename));
          }
        }
      }

      $this->get('mailer')->send($message);

      $commande->updateStatus($this->getUser(), $newStatus);
      $em->persist($mail);
      $em->flush();

      $this->addFlash('success', $flashMessage);

      return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
    }

    return $this->render('AWPlansBundle:Mail:mail.html.twig', array(
      'commande' => $commande,
      'form' => $form->createView()
    ));
  }
}
