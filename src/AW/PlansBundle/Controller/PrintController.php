<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Finder\Finder;

use AW\PlansBundle\PDF\PrintPDF;

class PrintController extends Controller
{
  public function newAction($format)
  {
    return $this->render('AWPlansBundle:Print:new.html.twig', array(
      'format' => $format,
      'dirName' => date('YmdHis')
    ));
  }

  public function uploadAction(Request $request, $format, $dirName, $file)
  {
    switch($request->getMethod()){
      case 'POST':
        return $this->postFiles($request, $format, $dirName);
      case 'GET':
        return $this->getFile($dirName, $file);
      case 'DELETE':
        return $this->deleteFile($dirName, $file);
      default:
        throw new \Exception('Action inconnue !');
    }
  }

  private function postFiles($request, $format, $dirName)
  {
    $response = array();

    $builder = $this->createFormBuilder(array(), array('csrf_protection' => false));
    $form = $builder
      ->add('files', FileType::class, array('multiple' => true))
      ->getForm()
    ;

    $form->handleRequest($request);

    $dir = $this->getParameter('documents_dir').'/impression/'.$dirName;
    $data = $form->getData();
    foreach($data['files'] as $file){
      $filename = $file->getClientOriginalName();
      while(file_exists($dir.'/'.$filename)){
        $filename = $this->get('aw.core.service.utils')->filenameCounter($filename);
      }

      $file->move($dir, $filename);

      $this->rotatePDF($dir.'/'.$filename, $format);

      $response[] = array(
        'name' => $filename,
        'url' => $this->generateUrl('aw_plans_print_upload', array('format' => $format, 'dirName' => $dirName, 'file' => $filename))
      );
    }
    return new JsonResponse($response);
  }

  private function rotatePDF($filepath, $format)
  {
    $pdf = new \FPDI();

    $nbPage = $pdf->setSourceFile($filepath);
    $tplidx = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($tplidx);

    if(in_array($format, array('A0', 'A2', 'A4', 'A5'))){
      if($size['w'] > $size['h']){
        return;
      }
      $orientation = 'P';
    }

    if(in_array($format, array('A1', 'A3'))){
      if($size['w'] < $size['h']){
        return;
      }
      $orientation = 'L';
    }

    $pdf->addPage($orientation, array($size['w'], $size['h']), 90);
    $pdf->useTemplate($tplidx);

    $pdf->Output($filepath, 'F');
  }

  private function getFile($dirName, $file)
  {
    $filepath = $this->getParameter('documents_dir').'/impression/'.$dirName.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  private function deleteFile($dirName, $file)
  {
    $filepath = $this->getParameter('documents_dir').'/impression/'.$dirName.'/'.$file;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    if(@unlink($filepath)){
      return new JsonResponse(array());
    }

    throw new \Exception('Échec de suppression du fichier !');
  }

  public function generateAction($format, $dirName)
  {
    $dir = $this->getParameter('documents_dir').'/impression/'.$dirName;

    $finder = new Finder();
    $finder
      ->files()
      ->name('*.pdf')
      ->notName('OUTPUT*')
      ->in($dir)
    ;

    switch($format){
      case 'A0':
        $nbPlansPerPage = 1;
        break;
      case 'A1':
        $nbPlansPerPage = 3;
        break;
      case 'A2':
        $nbPlansPerPage = 6;
        break;
      case 'A3':
        $nbPlansPerPage = 12;
        break;
      case 'A4':
        $nbPlansPerPage = 24;
        break;
      case 'A5':
        $nbPlansPerPage = 54;
        break;
      default:
        throw new \Exception('Format inconnu');
        break;
    }

    $groupedFiles = array();
    $i = 0;
    $key = 1;
    $groupedFiles[$i] = array();
    foreach($finder as $file){
      $groupedFiles[$i][] = $file;

      if(
        ($i == 0 and $key > $nbPlansPerPage-1) or
        ($i > 0 and $key%$nbPlansPerPage == 0)
      ){
        $i++;
      }

      $key++;
    }

    foreach($groupedFiles as $key => $files){
      $output = $dir.'/OUTPUT-'.($key+1).'.pdf';

      $printPDF = new PrintPDF($format);
      $printPDF->generate($files, $output);
    }

    return new JsonResponse(array());
  }

  public function outputAction($dirName)
  {
    $dir = $this->getParameter('documents_dir').'/impression/'.$dirName;

    $finder = new Finder();
    $finder
      ->files()
      ->name('OUTPUT*.pdf')
      ->in($dir)
    ;

    if(count($finder) == 0){
      throw $this->createNotFoundException('Aucun fichier de sortie trouvé');
    }

    if(count($finder) == 1){
      $filepath = $this->getParameter('documents_dir').'/impression/'.$dirName.'/OUTPUT-1.pdf';
    }else{
      $zipFile = new \ZipArchive();
      $filepath = $this->getParameter('documents_dir').'/impression/'.$dirName.'/OUTPUT.zip';
      if($zipFile->open($filepath, \ZipArchive::CREATE|\ZipArchive::OVERWRITE) !== true){
        throw new \Exception('Impossible de créer le fichier ZIP');
      }

      foreach($finder as $file){
        $zipFile->addFile($file->getRealPath(), $file->getFilename());
      }

      $zipFile->close();

      foreach($finder as $file){
        @unlink($file->getRealPath());
      }
    }

    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.$filepath.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response
      ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT)
      ->deleteFileAfterSend(true)
    ;
    return $response;
  }
}
