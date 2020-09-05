<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Production;

class ProductionController extends Controller
{
  public function listAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.seeprod');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $onlyUserProduction = $this->isGranted('webappli.cmdplan.sendbat') ? false : true;

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getListProduction($start, $length, $columns, $orders, $this->getUser(), $onlyUserProduction)
      ;

      $data = array();
      foreach($commandes as $commande){
        $data[] = array(
          'id' => $commande->getId(),
          'url' => $this->generateUrl('aw_plans_production_view', array('id' => $commande->getId())),
          'ref' => $commande->getRef(),
          'refClient' => $commande->getRefClient(),
          'societe' => $commande->getSociete()->getName(),
          'site' => $commande->getSite(),
          'date' => $commande->getDateCreation()->format('d/m/Y'),
          'dateUpdate' => $commande->getDateUpdate() ? $commande->getDateUpdate()->format('d/m/Y') : '',
          'status' => $commande->getStatus(),
          'statusLabel' => $commande->getStatusLabel(),
          'production' => $commande->getProduction(),
          'urgent' => $commande->getUrgent(),
          'alert' => $commande->getAlert(),
          'operateur' => $commande->getUserProduction() ? $commande->getUserProduction()->getFullname() : ''
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

    if($_format == 'js'){
      return $this->render('AWPlansBundle:Production:list.js.twig');
    }

    return $this->render('AWPlansBundle:Production:list.html.twig', array(
      'listStatus' => Commande::$statusName
    ));
  }

  /**
   * @ParamConverter("commande", options={"repository_method" = "findWithProductions"})
   */
  public function viewAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW_PRODUCTION', $commande);

    return $this->render('AWPlansBundle:Production:view.html.twig', array(
      'commande' => $commande
    ));
  }

  public function startAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('START_PRODUCTION', $commande);

    $nbProdOngoing = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Production')
      ->getUserOngoing($this->getUser())
    ;

    if($nbProdOngoing >= 2){
      $this->addFlash('error', 'Vous ne pouvez pas lancer plus de deux productions en même temps.');
      return $this->redirectToRoute('aw_plans_production_view', array('id' => $commande->getId()));
    }

    $commande
      ->setUserProduction($this->getUser())
      ->setProduction(2)
    ;

    $production = new Production();
    $production
      ->setCommande($commande)
      ->setStatus($commande->getStatus())
      ->setUser($this->getUser())
      ->setDateStart(new \DateTime())
    ;

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $em->persist($production);
    $em->flush();

    return $this->render('AWPlansBundle:Production:start.html.twig', array(
      'commande' => $commande
    ));
  }

  public function downloadAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('END_PRODUCTION', $commande);

    $tmpfile = tempnam(sys_get_temp_dir(), 'AW');

    $zipFile = new \ZipArchive();
    if($zipFile->open($tmpfile, \ZipArchive::CREATE|\ZipArchive::OVERWRITE) !== true){
      throw new \Exception('Impossible de créer le fichier ZIP');
    }

    $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir();
    if(file_exists($dir)){
      $dirFinder = new Finder();
      $dirFinder->directories()->in($dir);

      foreach($dirFinder as $dir){
        $zipFile->addEmptyDir($dir->getFilename());

        $fileFinder = new Finder();
        $fileFinder->files()->in($dir->getRealPath());
        foreach($fileFinder as $file){
          $localPath = $dir->getFilename().'/'.$file->getFilename();
          $zipFile->addFile($file->getRealPath(), $localPath);
        }
      }
    }

    $dir = $this->getParameter('documents_dir').'/prodplan/'.$commande->getDir();
    if(file_exists($dir)){
      // recherche de la dernière PROD
      $dirFinder = new Finder();
      $dirFinder->directories()->in($dir);
      $lastDir = null;
      $lastProd = 0;

      foreach($dirFinder as $dir){
        $aDir = explode('_', $dir->getFilename(), 3);
        if($aDir[1] > $lastProd){
          $lastDir = $dir;
          $lastProd = intval($aDir[1]);
        }
      }

      if($lastDir){
        $fileFinder = new Finder();
        $fileFinder->files()->in($lastDir->getRealPath());
        foreach($fileFinder as $file){
          $zipFile->addFile($file->getRealPath(), 'PROD/'.$file->getFilename());
        }
      }
    }

    $zipFile->close();

    if(!file_exists($tmpfile)){ // Zip not created if no file added
      $this->addFlash('error', "Impossible de créer l'archive des fichiers.");
      return $this->redirectToRoute('aw_plans_production_view', array('id' => $commande->getId()));
    }

    $response = new BinaryFileResponse($tmpfile);
    $response
      ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $commande->getRef().'.zip')
      ->deleteFileAfterSend(true)
    ;

    return $response;
  }

  public function endAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('END_PRODUCTION', $commande);

    $dir = tempnam(sys_get_temp_dir(), 'AW');
    return $this->render('AWPlansBundle:Production:end.html.twig', array(
      'commande' => $commande,
      'dirName' => basename($dir)
    ));
  }

  public function uploadAction(Request $request, $dirName, $file)
  {
    switch($request->getMethod()){
      case 'POST':
        return $this->postFiles($request, $dirName);
      case 'GET':
        return $this->getFile($dirName, $file);
      case 'DELETE':
        return $this->deleteFile($dirName, $file);
      default:
        throw new \Exception('Action inconnue !');
    }
  }

  private function postFiles($request, $dirName)
  {
    $response = array();

    $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
    $form = $builder
      ->add('files', FileType::class, array('multiple' => true))
      ->getForm()
    ;

    $form->handleRequest($request);

    $dir = sys_get_temp_dir().'/'.$dirName;
    $fs = new Filesystem();
    if(!is_dir($dir)){
      $fs->remove($dir);
    }
    if(!file_exists($dir)){
      $fs->mkdir($dir);
    }

    $data = $form->getData();
    foreach($data['files'] as $file){
      $filename = $file->getClientOriginalName();
      while(file_exists($dir.'/'.$filename)){
        $filename = $this->get('aw.core.service.utils')->filenameCounter($filename);
      }

      $file->move($dir, $filename);

      $response[] = array(
        'name' => $filename,
        'url' => $this->generateUrl('aw_plans_production_upload', array('dirName' => $dirName, 'file' => $filename))
      );
    }
    return new JsonResponse($response);
  }

  private function getFile($dirName, $file)
  {
    $filepath = sys_get_temp_dir().'/'.$dirName.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  private function deleteFile($dirName, $file)
  {
    $filepath = sys_get_temp_dir().'/'.$dirName.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    if(@unlink($filepath)){
      return new JsonResponse(array());
    }

    throw new \Exception('Échec de suppression du fichier !');
  }

  public function finishAction(Commande $commande, $dirName)
  {
    $tmpDir = sys_get_temp_dir().'/'.$dirName;

    // vérifier s'il y a bien un AI et un PDF
    $missingAI = true;
    $missingPDF = true;
    $finder = new Finder();
    $finder->files()->in($tmpDir);
    foreach($finder as $file){
      $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

      if($ext == 'ai'){
        $missingAI = false;
      }elseif($ext == 'pdf'){
        $missingPDF = false;
      }
    }

    if($missingAI or $missingPDF){
      throw new \Exception('Fichiers AI et/ou PDF manquants');
    }

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $production = $em
      ->getRepository('AWPlansBundle:Production')
      ->getCurrent($commande)
    ;

    $production
      ->setDateEnd(new \DateTime())
    ;

    $commande
      ->setProduction(3)
    ;

    $dir = $this->getParameter('documents_dir').'/prodplan/'.$commande->getDir().'/prod_'.count($commande->getProductions());
    if($commande->getStatus() == Commande::STATUS_VALIDATED){
      $dir .= '_creation';
    }else{
      $dir .= '_BAT_'.count($commande->getBats());
    }

    $fs = new Filesystem();
    if(file_exists($dir)){
      $fs->remove($dir);
    }
    $fs->rename($tmpDir, $dir);

    $em->flush();

    return new JsonResponse(array());
  }

  public function returnAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('RETURN_PRODUCTION', $commande);

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $production = $em
      ->getRepository('AWPlansBundle:Production')
      ->getLast($commande)
    ;

    if($production !== null){
      $em->remove($production);
    }

    $commande
      ->setProduction(1)
    ;

    $em->flush();

    return $this->redirectToRoute('aw_plans_production_view', array('id' => $commande->getId()));
  }

  public function cancelAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('CANCEL_PRODUCTION', $commande);

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $commande
      ->setProduction(0)
    ;

    $em->flush();

    return $this->redirectToRoute('aw_plans_production_view', array('id' => $commande->getId()));
  }
}
