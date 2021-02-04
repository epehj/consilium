<?php

namespace AW\PlansBundle\EventListener;

use AW\PlansBundle\Entity\CommandeST;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use AW\PlansBundle\Entity\Commande;
use AW\DoliBundle\Entity\Commande as DoliCommande;
use AW\CoreBundle\Service\Utils;
use AW\PlansBundle\PDF\Generator as PDFGenerator;

class CommandeListener
{
  private $tokenStorage;
  private $doliCommandeListener;
  private $utils;
  private $pdfGenerator;
  private $documentsDir;
  private $doliDocumentsDir;

  private $needReFlush;
  private $doliNeedUpdate;
  private $pdfNeedUpdate;

  public function __construct(TokenStorageInterface $tokenStorage, DoliCommandeListener $doliCommandeListener, Utils $utils, PDFGenerator $pdfGenerator, $documentsDir, $doliDocumentsDir)
  {
    $this->tokenStorage = $tokenStorage;
    $this->doliCommandeListener = $doliCommandeListener;
    $this->utils = $utils;
    $this->pdfGenerator = $pdfGenerator;
    $this->documentsDir = $documentsDir;
    $this->doliDocumentsDir = $doliDocumentsDir;

    $this->needReFlush = false;
    $this->doliNeedUpdate = false;
    $this->pdfNeedUpdate = false;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    //pas sur d'avoir besoin de capter l'évent quand on persiste une commandeST
    if(!$entity instanceof Commande){
      return;
    }

    try{
      $lastRefNumber = $args
        ->getObjectManager()
        ->getRepository('AWDoliBundle:Commande')
        ->getLastRef('AW{yy}{mm}{0000}')
      ;
    }catch(\Doctrine\ORM\NoResultException $e){
      $lastRefNumber = 0;
    }

    $nextRef = 'AW'.date('ym').str_pad(strval($lastRefNumber+1), 4, '0', STR_PAD_LEFT); // nouvelle référence

    if($entity instanceof Commande)
        $dir = trim($entity->getSociete()->getName().' - '.$entity->getSite().' - '.$nextRef); // dossier de la commande
    else
        $dir = trim($entity->getCommande()->getSociete()->getName().' - '.$entity->getCommande->getSite().' - '.$nextRef); // dossier de la commande
      // remplacer les caractères accentués
    $search = [
      'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a',
      'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a', 'È' => 'e',
      'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e',
      'ê' => 'e', 'ë' => 'e', '€' => 'e', 'Ì' => 'i', 'Í' => 'i',
      'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
      'ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o',
      'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'Ù' => 'u',
      'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u',
      'û' => 'u', 'ü' => 'u', 'µ' => 'u', 'Œ' => 'oe', 'œ' => 'oe',
      '$' => 's'
    ];
    $dir = strtr($dir, $search);
    $dir = preg_replace('#[^A-Za-z0-9 .-]+#', '_', $dir); // remplacer tous autres caractères non alpha-numérique par un tiret bas (sauf espace et tiret)
    $dir = trim($dir, '_'); // supprimer les éventuels tiret bas en debut et fin
    $dir = strtoupper($dir); // mettre toute la chaine en majuscule

    if($entity->getSociete()->getInfosPlans()){
      if($entity->getSociete()->getInfosPlans()->getAutoUrgent()){
        $entity->setUrgent(true);
      }

      if($entity->getSociete()->getInfosPlans()->getBatOnlyByFr()){
        $entity->setBatOnlyByFr(true);
      }
    }

    $entity
      ->setRef($nextRef)
      ->setDir($dir)
    ;
//      $entity->getCommandeST()->setR

    $entity->setDoliCommande(new DoliCommande());
    $entity->getDoliCommande()
      ->setRef($nextRef)
      ->setRefClient($entity->getRefClient())
      ->setDateCommande($entity->getDateCreation())
      ->setDateCreation($entity->getDateCreation())
      ->setSociete($entity->getSociete())
      ->setUserCreation($entity->getUserCreation())
    ;
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    $dir = $this->documentsDir.'/cmdplan/'.$entity->getDir().'/creation';
    $fs = new Filesystem();
    $fs->mkdir($dir);

    // ajout du logo du client
    if($entity->getSociete()->getLogo()){
      $logoClient = $this->doliDocumentsDir.'/societe/'.$entity->getSociete()->getId().'/logos/'.$entity->getSociete()->getLogo();
      if(file_exists($logoClient)){
        $info = pathinfo($logoClient);
        $targetFile = $dir.'/logo_client.'.$info['extension'];
        while(file_exists($targetFile)){
          $targetFile = $this->utils->filenameCounter($targetFile);
        }

        $fs->copy($logoClient, $targetFile);
      }
    }
  }

  public function preUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    if($this->tokenStorage->getToken()){ // ne pas changement de date de modifier si pas de token (cas via command)
      $user = $this->tokenStorage->getToken()->getUser();
      $entity
        ->setDateModification(new \DateTime())
        ->setUserModification($user)
      ;
    }

    if($args->hasChangedField('status')){
      $entity
        ->setDateUpdate(new \DateTime())
      ;
    }

    if($args->hasChangedField('refClient') and $entity->getDoliCommande() !== null){
      $entity
        ->getDoliCommande()
        ->setRefClient($entity->getRefClient())
      ;

      $this->needReFlush = true;
    }

    if($args->hasChangedField('address1') or
      $args->hasChangedField('address2') or
      $args->hasChangedField('zip') or
      $args->hasChangedField('town')
    ){
      $entity->setGeoCode(null);
    }

    if(
      $args->hasChangedField('pose') and
      !$entity->getPose() and
      $entity->getReleveStatus() == Commande::POSE_STATUS_EN_ATTENTE
    ){
      if($entity->getReleve()){
        $entity->setReleveStatus(Commande::RELEVE_STATUS_TERMINE);
      }else{
        $entity->setReleveStatus(null);
      }
    }

    if($args->hasChangedField('site') or
      $args->hasChangedField('address1') or
      $args->hasChangedField('address2') or
      $args->hasChangedField('zip') or
      $args->hasChangedField('town') or
      $args->hasChangedField('remarques') or
      $args->hasChangedField('refClient')
    ){
      $this->pdfNeedUpdate = true;
    }

    if($args->hasChangedField('site') or
      $args->hasChangedField('address1') or
      $args->hasChangedField('address2') or
      $args->hasChangedField('zip') or
      $args->hasChangedField('town') or
      $args->hasChangedField('releve') or
      $args->hasChangedField('pose') or
      $args->hasChangedField('qtyDeclination')
    ){
      $this->doliNeedUpdate = true;
    }
  }

  public function postUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    if($this->pdfNeedUpdate){
      $this->pdfGenerator->generateCommandePDF($entity);
    }

    if($this->doliNeedUpdate){
      $this->doliCommandeListener->updateDoliCommandeDet($entity);
    }

    if($this->needReFlush){
      $args->getEntityManager()->flush();
    }
  }

  public function postRemove(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Commande){
      return;
    }

    if(empty($entity->getDir())){
      return;
    }

    $fs = new Filesystem();

    $dir = $this->documentsDir.'/cmdplan/'.$entity->getDir();
    if(file_exists($dir)){
      try{
        $fs->remove($dir);
      }catch(IOExceptionInterface $e){}
    }

    $dir = $this->documentsDir.'/prodplan/'.$entity->getDir();
    if(file_exists($dir)){
      try{
        $fs->remove($dir);
      }catch(IOExceptionInterface $e){}
    }
  }
}
