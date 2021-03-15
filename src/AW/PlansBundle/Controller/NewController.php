<?php

namespace AW\PlansBundle\Controller;

use AW\PlansBundle\Form\CommandeSousTraitantType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\CommandeST;
use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Mail;
use AW\PlansBundle\Form\CommandeType;
use AW\PlansBundle\Form\CommandeSTType;
use AW\DoliBundle\Entity\Societe;

class NewController extends Controller
{
  /**
   * @ParamConverter("societe", options={"mapping": {"socid": "id"}})
   */
  public function newAction(Request $request, Societe $societe=null)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.new');

    $commande = new Commande();

    if($societe){
      $commande->setSociete($societe);
    }elseif($this->getUser()->getSociete()){
      $commande->setSociete($this->getUser()->getSociete());
    }

    // bien vérifier pour les utilisateurs clients qu'ils font bien partie de la societe
    if($societe and $this->getUser()->getSociete() and $societe != $this->getUser()->getSociete()){
      $ok = false;
      foreach($this->getUser()->getSociete()->getChildren() as $child){
        if($societe == $child){
          $ok = true;
          break;
        }
      }

      if(!$ok){
        return $this->redirectToRoute('aw_plans_new');
      }
    }

    $form = $this->get('form.factory')->create(CommandeType::class, $commande);

    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      $commande->setUserCreation($this->getUser());

      /*
       * Insertion des données en base en 2 étapes
       * 1 - Enregistrement de la commande
       * 2 - Enregistrement des plans en ajoutant le lien vers la commande ayant un ID maintenant
       *
       * Source : https://github.com/winzou/SdzBlog/blob/master/src/Sdz/BlogBundle/Controller/BlogController.php
       */

      $commande->getListDet()->clear(); // vider les details

      $tmpdir = sys_get_temp_dir().'/'.$commande->getDir(); // sauvegarder le dossier temporaire
      $em->persist($commande);
      $em->flush();

      $newdir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation';
      $fs = new Filesystem();
      $fs->mirror($tmpdir, $newdir);

      /*
       * Désactiver les événements Doctrines
       * Afin d'éviter des doublons dans les lignes de commande Dolibarr
       */
      $em
        ->getEventManager()
        ->removeEventListener(
          array('postPersist'),
          $this->get('aw_plans.eventlistener.commandedet')
        )
      ;

      foreach($form->get('listDet')->getData() as $det){
        $det->setCommande($commande);
        $commande->addListDet($det);
        $em->persist($det);
      }
      $em->flush();

      $this->get('aw_plans.eventlistener.doli_commande')->updateDoliCommandeDet($commande);

      $model = $em
        ->getRepository('AWCoreBundle:ModelMail')
        ->findOneByType('NEW_COMMANDE');
      ;
      if($model !== null){
        $em->detach($model);

        try{
          $model->render($this->get('twig'), array('commande' => $commande));

          if($this->getUser()->getSociete() and $this->getUser()->getEmail()){
            $to = $this->getUser()->getFullName().' <'.$this->getUser()->getEmail().'>, '.$this->getParameter('email_plans');
          }else{
            $to = $this->getParameter('email_plans');
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

            foreach($commande->getSociete()->getCommercials() as $commercial){
              if($commercial->getEmail()){
                $message->addBcc($commercial->getEmail());
              }
            }
          }

          $finder = new Finder();
          foreach($finder->files()->in($newdir) as $file){
            $message->attach(\Swift_Attachment::fromPath($file->getRealPath()));
          }

          $this->get('mailer')->send($message);

          $em->persist($mail);
          $em->flush();
        }catch(\Exception $e){
          $this->addFlash('error', "Échec d'envoi de mail (Error :".$e->getMessage().")");
        }
      }else{
        $this->addFlash('error', "Échec d'envoi de mail");
      }

      return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
    }

    if($commande->getDir()){
      $dir = sys_get_temp_dir().'/'.$commande->getDir();

      $finder = new Finder();
      foreach($finder->files()->name('logo.*')->in($dir) as $file){
        $logo = $file;
        break;
      }

      $finder = new Finder();
      $finder->files()->notName('logo.*')->in($dir);
    }

    $consigne = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Consigne')
      ->find(1)
    ;

    return $this->render('AWPlansBundle:New:new.html.twig', array(
      'form' => $form->createView(),
      'logo' => isset($logo) ? $logo : null,
      'finder' => isset($finder) ? $finder : null,
      'consigne' => $consigne
    ));
  }
  /**
   * @ParamConverter("societe", options={"mapping": {"socid": "id"}})
   */
  public function newSTAction(Request $request, Societe $societe=null)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.new');

    // FIXME ajouter les valeurs par defaut pour les champs qui devrait etre nullable=false (validationObligatoireReleveur et cancel au moins)
    $commande = new Commande();
    // valeurs par défaut pour gérer les champs obligatoires
    $commandeST = new CommandeST();
    $commandeST->setCommande($commande);
    $commande->setCommandeST($commandeST);
    $commandeST->setInfosComplementaires(CommandeST::NONE);
    $commandeST->setCancel(false);

    if($societe){
      $commande->setSociete($societe);
    }elseif($this->getUser()->getSociete()){
      $commande->setSociete($this->getUser()->getSociete());
    }

    // bien vérifier pour les utilisateurs clients qu'ils font bien partie de la societe
    if($societe and $this->getUser()->getSociete() and $societe != $this->getUser()->getSociete()){
      $ok = false;
      foreach($this->getUser()->getSociete()->getChildren() as $child){
        if($societe == $child){
          $ok = true;
          break;
        }
      }

      if(!$ok){
        return $this->redirectToRoute('aw_plans_new_st');
      }
    }

    $form = $this->get('form.factory')->create(CommandeSousTraitantType::class, $commandeST);
    $form->handleRequest($request);
    if($request->isMethod('POST') and $form->isValid()){
      $em = $this
        ->getDoctrine()
        ->getManager()
      ;

      $commande->setUserCreation($this->getUser());

      /*
       * Insertion des données en base en 2 étapes
       * 1 - Enregistrement de la commande
       * 2 - Enregistrement des plans en ajoutant le lien vers la commande ayant un ID maintenant
       *
       * Source : https://github.com/winzou/SdzBlog/blob/master/src/Sdz/BlogBundle/Controller/BlogController.php
       */

      $commande->getListDet()->clear(); // vider les details
      switch ($commandeST->getPrestation()) {
          case CommandeST::PRESTA_TOTAL:
              $commande->setReleve(true);
              $commande->setPose(true);
              break;
          case CommandeST::PRESTA_POSE:
              $commande->setPose(true);
              break;
          case CommandeST::PRESTA_RELEVE:
              $commande->setReleve(true);
              break;

      }

      $tmpdir = sys_get_temp_dir().'/'.$commande->getDir(); // sauvegarder le dossier temporaire
      $em->persist($commande);
      $em->flush();

      $newdir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/creation';
      $fs = new Filesystem();
      $fs->mirror($tmpdir, $newdir);

      /*
       * Désactiver les événements Doctrines
       * Afin d'éviter des doublons dans les lignes de commande Dolibarr
       */
      $em
        ->getEventManager()
        ->removeEventListener(
          array('postPersist'),
          $this->get('aw_plans.eventlistener.commandedet')
        )
      ;

      foreach($form->get('commande')->get('listDet')->getData() as $det){
        $det->setCommande($commande);
        $commande->addListDet($det);
        $em->persist($det);
      }
      $em->flush();

      $this->get('aw_plans.eventlistener.doli_commande')->updateDoliCommandeDet($commande);

      $model = $em
        ->getRepository('AWCoreBundle:ModelMail')
        ->findOneByType('NEW_COMMANDE');
      ;
      if($model !== null){
        $em->detach($model);

        try{
          $model->render($this->get('twig'), array('commande' => $commande));

          if($this->getUser()->getSociete() and $this->getUser()->getEmail()){
            $to = $this->getUser()->getFullName().' <'.$this->getUser()->getEmail().'>, '.$this->getParameter('email_plans');
          }else{
            $to = $this->getParameter('email_plans');
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

            foreach($commande->getSociete()->getCommercials() as $commercial){
              if($commercial->getEmail()){
                $message->addBcc($commercial->getEmail());
              }
            }
          }

          $finder = new Finder();
          foreach($finder->files()->in($newdir) as $file){
            $message->attach(\Swift_Attachment::fromPath($file->getRealPath()));
          }

          $this->get('mailer')->send($message);

          $em->persist($mail);
          $em->flush();
        }catch(\Exception $e){
          $this->addFlash('error', "Échec d'envoi de mail (Error :".$e->getMessage().")");
        }
      }else{
        $this->addFlash('error', "Échec d'envoi de mail");
      }

      return $this->redirectToRoute('aw_plans_view', array('id' => $commande->getId()));
    }

    if($commande->getDir()){
      $dir = sys_get_temp_dir().'/'.$commande->getDir();

      $finder = new Finder();
      foreach($finder->files()->name('logo.*')->in($dir) as $file){
        $logo = $file;
        break;
      }

      $finder = new Finder();
      $finder->files()->notName('logo.*')->in($dir);
    }

    $consigne = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Consigne')
      ->find(1)
    ;

    return $this->render('AWPlansBundle:New:new_st.html.twig', array(
      'form' => $form->createView(),
      'logo' => isset($logo) ? $logo : null,
      'finder' => isset($finder) ? $finder : null,
      'consigne' => $consigne
    ));
  }

  //fixme changer le dossir d'upload des bons dans "bon"
  public function uploadAction(Request $request, $type)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.new');

    $response = array();
    $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
    $builder->add('dir', TextType::class);

    if($type == 'logo'){
      $builder->add('logo', FileType::class);
    }else{
      $builder->add('files', FileType::class, array('multiple' => true));
    }

    $form = $builder->getForm();
    if($form->handleRequest($request)->isValid()){
      $data = $form->getData();

      $fs = new Filesystem();
      $dir = sys_get_temp_dir().'/'.$data['dir'];

      if(!file_exists($dir)){
        throw new \Exception('Dossier temporaire introuvable.');
      }

      if(!is_dir($dir)){
        throw new \Exception('Dossier temporaire incorrect.');
      }

      // logo
      if($type == 'logo'){
        $finder = new Finder();
        foreach($finder->files()->name('logo.*')->in($dir) as $file){
          $filepath = $dir.'/'.$file-> getRelativePathname();
          $fs->remove($filepath);
        }

        $mimetype = $data['logo']->getMimeType();
        switch($mimetype){
          case 'image/jpeg':
          case 'image/pjpeg':
            $extension = '.jpeg';
            break;

          case 'image/png':
          case 'image/x-png':
            $extension = '.png';
            break;

          default:
            throw new \Exception('Extension image inconnu : '.$mimetype);
            break;
        }

        $filename = 'logo'.$extension;
        $data['logo']->move($dir, $filename);
        $response[] = array(
          'url' => $this->generateUrl('aw_plans_new_upload_view', array('dir' => $data['dir'], 'file' => $filename)),
          'name' => $filename,
          'type' => $mimetype,
          'size' => filesize($dir.'/'.$filename),
          'delete_url' => $this->generateUrl('aw_plans_new_upload_delete', array('dir' => $data['dir'], 'file' => $filename))
        );
      }else{
        foreach($data['files'] as $file){
          $filename = $file->getClientOriginalName();
          $mimetype = $file->getMimeType();
          $file->move($dir, $filename);
          $response[] = array(
            'url' => $filename,
            'name' => $filename,
            'type' => $mimetype,
            'size' => filesize($dir.'/'.$filename),
            'delete_url' => $this->generateUrl('aw_plans_new_upload_delete', array('dir' => $data['dir'], 'file' => $filename))
          );
        }
      }

      return new JsonResponse($response);
    }

    $message = $form->getErrors() ? $form->getErrors() : 'Échec de téléchargement.';
    throw new \Exception($message);
  }

  public function uploadViewAction($dir, $file)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.new');

    $filepath = sys_get_temp_dir().'/'.$dir.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  public function uploadDeleteAction($dir, $file)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.new');

    $filepath = sys_get_temp_dir().'/'.$dir.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $fs = new Filesystem();
    $fs->remove($filepath);

    return new JsonResponse();
  }
}
