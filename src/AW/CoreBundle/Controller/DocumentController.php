<?php

namespace AW\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DocumentController extends Controller
{
  private function humanFileSize($bytes)
  {
    $units = array('o', 'ko', 'Mo', 'Go', 'To', 'Po');
    $factor = floor((strlen($bytes) -1)/3);
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . @$units[$factor];
  }

  public function manageAction()
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $treeview = array();
    $dir = $this->getParameter('documents_dir').'/docs';
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
            'subdir' => $dir->getFilename(),
            'file' => $file->getFilename()
          );

          $dirTree['children'][] = array(
            'name' => $file->getFilename(),
            'size' => $this->humanFileSize($file->getSize()),
            'link' => $this->generateUrl('aw_core_documents_view', $linkparams),
            'deletelink' => $this->generateUrl('aw_core_documents_delete', $linkparams),
            'extension' => $file->getExtension()
          );
        }

        $treeview[] = $dirTree;
      }
    }

    return $this->render('AWCoreBundle:Document:manage.html.twig', array(
      'treeview' => $treeview
    ));
  }

  public function uploadAction(Request $request, $dirName, $_format, $file)
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $dir = $this->getParameter('documents_dir').'/docs/'.$dirName;

    if($request->getMethod() == 'GET'){
      return $this->render('AWCoreBundle:Document:upload.html.twig', array(
        'dirName' => $dirName
      ));
    }elseif($request->getMethod() == 'POST'){
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
          'url' => $this->generateUrl('aw_core_documents_view', array(
            'subdir' => $dirName,
            'file' => $filename
          )),
          'delete_url' => $this->generateUrl('aw_core_documents_upload', array(
            'dirName' => $dirName,
            'file' => $filename,
            '_format' => 'json'
          )),
        );
      }
      return new JsonResponse($response);
    }elseif($request->getMethod() == 'DELETE'){
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

  public function deleteAction($subdir, $file)
  {
    $this->denyAccessUnlessGranted('webappli.admin');

    $filepath = $this->getParameter('documents_dir').'/docs/'.$subdir.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    if(@unlink($filepath)){
      $this->addFlash('success', 'Fichier '.$file.' supprimé!');
      return $this->redirectToRoute('aw_core_documents_manage');
    }

    throw new \Exception('Échec de suppression du fichier !');
  }

  public function popupAction()
  {
    if($this->get('session')->get('popupSeen')){
      throw $this->createNotFoundException();
    }

    $file = $this->getParameter('documents_dir').'/docs/popup/popup.pdf';
    if(file_exists($file)){
      $this->get('session')->set('popupSeen', 1);
      return $this->render('AWCoreBundle:Document:popup.html.twig');
    }

    throw $this->createNotFoundException();
  }

  public function listAction()
  {
    $this->denyAccessUnlessGranted('webappli.docs');

    $tree = array();
    $dir = $this->getParameter('documents_dir').'/docs';
    if(file_exists($dir)){
      $dirFinder = new Finder();
      $dirFinder->sortByName()->directories()->in($dir)->exclude('popup');

      foreach($dirFinder as $dir){
        $dirTree = array(
          'text' => $dir->getFilename(),
          'icon' => 'fa fa-folder',
          'nodes' => array()
        );

        $fileFinder = new Finder();
        $fileFinder->sortByName()->files()->in($dir->getRealPath());
        foreach($fileFinder as $file){
          $linkparams = array(
            'subdir' => $dir->getFilename(),
            'file' => $file->getFilename()
          );

          $dirTree['nodes'][] = array(
            'text' => $file->getFilename(),
            'icon' => 'fa fa-file-o',
            'dataAttr' => array(
              'link' => $this->generateUrl('aw_core_documents_view', $linkparams),
              'ext' => $file->getExtension()
            )
          );
        }

        $tree[] = $dirTree;
      }
    }

    return $this->render('AWCoreBundle:Document:list.html.twig', array(
      'tree' => $tree
    ));
  }

  public function viewAction($subdir, $file)
  {
    $this->denyAccessUnlessGranted('webappli.docs');

    $filepath = $this->getParameter('documents_dir').'/docs/'.$subdir.'/'.$file;

    if(!file_exists($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
    return $response;
  }
}
