<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AW\PlansBundle\PDF\OfpPDF;

class OfpController extends Controller
{
  private function getDir()
  {
    $dir = $this->getParameter('documents_dir').'/ofp';
    if(!file_exists($dir)){
      $fs = new Filesystem();
      $fs->mkdir($dir);
    }

    return $dir;
  }

  public function listAction()
  {
    $dir = $this->getDir();
    $finder = new Finder();
    $finder->sort(function(\SplFileInfo $a, \SplFileInfo $b){
      return strcmp($b->getFilename(), $a->getFilename());
    });
    $finder->files()->in($dir);

    return $this->render('AWPlansBundle:Ofp:list.html.twig', array(
      'finder' => $finder
    ));
  }

  public function newAction()
  {
    $ref = 'OFP'.date('ym-dHi');
    $output = $this->getDir().'/'.$ref.'.pdf';
    if(file_exists($output)){
      $this->addFlash('error', 'La référence '.$ref.' existe déjà ! Réessayez !!!');
      return $this->redirectToRoute('aw_plans_ofs');
    }

    $commandes = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWPlansBundle:Commande')
      ->getListFabrication()
    ;

    $this->get('aw_plans.pdf.generator')->generateOfpPDF($commandes, $ref, $this->getUser());

    return $this->redirectToRoute('aw_plans_ofs');
  }

  public function viewAction($file)
  {
    $filepath = $this->getDir().'/'.$file;
    if(!file_exists($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  public function deleteAction($file)
  {
    $filepath = $this->getDir().'/'.$file;
    if(!file_exists($filepath)){
      throw $this->createNotFoundException('Fichier '.$file.' est introuvable.');
    }

    $fs = new Filesystem();
    $fs->remove($filepath);

    $this->addFlash('success', 'Le bon '.basename($file).' supprimé !!!');

    return $this->redirectToRoute('aw_plans_ofs');
  }
}
