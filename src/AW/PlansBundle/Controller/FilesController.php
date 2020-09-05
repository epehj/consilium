<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\Commande;

class FilesController extends Controller
{
  private function humanFileSize($bytes)
  {
    $units = array('o', 'ko', 'Mo', 'Go', 'To', 'Po');
    $factor = floor((strlen($bytes) -1)/3);
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . @$units[$factor];
  }

  /**
   * @ParamConverter("commande")
   */
  public function listAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW_FILE', $commande);

    $treeview = array();
    $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir();
    if(file_exists($dir)){
      $dirFinder = new Finder();
      $dirFinder->sortByName()->directories()->in($dir);

      foreach($dirFinder as $dir){
        $dirTree = array(
          'name' => $dir->getFilename(),
          'key' => md5($dir->getRealPath()),
          'children' => array()
        );

        $fileFinder = new Finder();
        $fileFinder->sort(function(\SplFileInfo $a, \SplFileInfo $b){
          return $a->getCTime() < $b->getCTime();
        })->files()->in($dir->getRealPath());
        foreach($fileFinder as $file){
          $linkparams = array(
            'id' => $commande->getId(),
            'dir' => 'cmdplan',
            'subdir' => $dir->getFilename(),
            'file' => $file->getFilename()
          );

          $dirTree['children'][] = array(
            'name' => $file->getFilename(),
            'date' => date('d/m/Y H:i', $file->getCTime()),
            'size' => $this->humanFileSize($file->getSize()),
            'link' => $this->generateUrl('aw_plans_files_view', $linkparams),
            'extension' => $file->getExtension()
          );
        }

        $treeview[] = $dirTree;
      }
    }

    if(!$this->getUser()->getSociete()){
      $dirProdTree = array(
        'name' => 'productions',
        'key' => md5('productions'),
        'children' => array()
      );

      $dir = $this->getParameter('documents_dir').'/prodplan/'.$commande->getDir();
      if(file_exists($dir)){
        $dirFinder = new Finder();
        $dirFinder->sortByName()->directories()->in($dir);

        foreach($dirFinder as $dir){
          $dirTree = array(
            'name' => $dir->getFilename(),
            'key' => md5($dir->getRealPath()),
            'children' => array()
          );

          $fileFinder = new Finder();
          $fileFinder->sortByName()->files()->in($dir->getRealPath());
          foreach($fileFinder as $file){
            $linkparams = array(
              'id' => $commande->getId(),
              'dir' => 'prodplan',
              'subdir' => $dir->getFilename(),
              'file' => $file->getFilename()
            );

            $dirTree['children'][] = array(
              'name' => $file->getFilename(),
              'size' => $this->humanFileSize($file->getSize()),
              'link' => $this->generateUrl('aw_plans_files_view', $linkparams),
              'extension' => $file->getExtension()
            );
          }

          $dirProdTree['children'][] = $dirTree;
        }

        $treeview[] = $dirProdTree;
      }
    }

    return $this->render('AWPlansBundle:Files:list.html.twig', array(
      'commande' => $commande,
      'treeview' => $treeview
    ));
  }

  /**
   * @ParamConverter("commande")
   */
  public function viewAction(Request $request, Commande $commande, $dir, $subdir, $file)
  {
    $this->denyAccessUnlessGranted('VIEW_FILE', $commande);

    $filepath = $this->getParameter('documents_dir').'/'.$dir.'/'.$commande->getDir().'/'.$subdir.'/'.$file;

    if(!file_exists($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    if($request->query->get('download')){
      $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }else{
      $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
    }

    return $response;
  }

  /**
   * @ParamConverter("commande")
   */
  public function generateAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $commande);

    $this->get('aw_plans.pdf.generator')->generateCommandePDF($commande);

    return $this->redirectToRoute('aw_plans_files', array('id' => $commande->getId()));
  }

  public function uploadAction(Request $request, Commande $commande, $dirName, $_format, $file)
  {
    $this->denyAccessUnlessGranted('EDIT_INTERNAL_USER', $commande);

    if($_format == 'json'){
      $dir = $this->getParameter('documents_dir').'/cmdplan/'.$commande->getDir().'/'.$dirName;

      switch($request->getMethod()){
        case 'POST':
          return $this->postFiles($request, $commande, $dir);
        case 'GET':
          return $this->getFile($dir, $file);
        case 'DELETE':
          return $this->deleteFile($dir, $file);
        default:
          throw new \Exception('Action inconnue !');
      }
    }

    return $this->render('AWPlansBundle:Files:upload.html.twig', array(
      'commande' => $commande,
      'dirName' => $dirName
    ));
  }

  private function postFiles(Request $request, Commande $commande, $dir)
  {
    $response = array();

    $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
    $form = $builder
      ->add('files', FileType::class, array('multiple' => true))
      ->getForm()
    ;

    $form->handleRequest($request);

    $fs = new Filesystem();
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
        'url' => $this->generateUrl('aw_plans_files_upload', array(
          'id' => $commande->getId(),
          'dirName' => basename($dir),
          'file' => $filename,
          '_format' => 'json'
        ))
      );
    }
    return new JsonResponse($response);
  }

  private function getFile($dir, $file)
  {
    $filepath = $dir.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  private function deleteFile($dir, $file)
  {
    $filepath = $dir.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    if(@unlink($filepath)){
      return new JsonResponse(array());
    }

    throw new \Exception('Échec de suppression du fichier !');
  }
}
