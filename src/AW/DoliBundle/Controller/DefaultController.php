<?php

namespace AW\DoliBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Entity\Propal;
use AW\DoliBundle\Entity\Facture;

class DefaultController extends Controller
{
  public function societeLogoAction(Request $request, Societe $societe)
  {
    if($societe->getLogo()){
      $logoClient = $this->getParameter('doli_documents_dir').'/societe/'.$societe->getId().'/logos/'.$societe->getLogo();
    }

    if(isset($logoClient) and !file_exists($logoClient)){
      $logoClient = $this->get('kernel')->getRootDir().'/../web/img/nophoto.png';
    }

    $md5 = md5_file($logoClient);
    $response = new Response();
    $response->setEtag($md5);
    $response->setPublic();
    if($response->isNotModified($request)){
      return $response;
    }

    $response = new BinaryFileResponse($logoClient);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
    $response->setEtag($md5);
    $response->setPublic();
    return $response;
  }

  public function propalAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.devis');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $propals = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWDoliBundle:Propal')
        ->getList($start, $length, $columns, $orders, $this->getUser())
      ;

      $data = array();
      foreach($propals as $propal){
        $data[] = array(
          'ref' => $propal->getRef(),
          'refClient' => $propal->getRefClient(),
          'societe' => $propal->getSociete()->getName(),
          'finValidite' => $propal->getFinValidite() ? $propal->getFinValidite()->format('d/m/Y') : '',
          'status' => $propal->getStatus(),
          'url' => $this->generateUrl('aw_doli_propal_view', array('ref' => $propal->getRef(), '_format' => 'pdf'))
        );
      }

      $response = array(
        'draw' => $draw,
        'recordsTotal' => count($propals),
        'recordsFiltered' => count($propals),
        'data' => $data
      );
      return new JsonResponse($response);
    }

    return $this->render('AWDoliBundle:Default:propal.html.twig');
  }

  public function propalViewAction(Propal $propal, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.devis');

    if($this->getUser()->getSociete() and $this->getUser()->getSociete()->getId() != $propal->getSociete()->getId()){
      throw $this->createNotFoundException('Devis introuvable');
    }

    $filepath = $this->getParameter('doli_documents_dir').'/propale/'.$propal->getRef().'/'.$propal->getRef().'.'.$_format;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.basename($filepath).' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }

  public function facturesAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.facture');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $factures = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWDoliBundle:Facture')
        ->getList($start, $length, $columns, $orders, $this->getUser())
      ;

      $data = array();
      foreach($factures as $facture){
        $data[] = array(
          'ref' => $facture->getRef(),
          'refClient' => $facture->getRefClient(),
          'societe' => $facture->getSociete()->getName(),
          'dateFacture' => $facture->getDateFacture() ? $facture->getDateFacture()->format('d/m/Y') : '',
          'dateLimReglement' => $facture->getDateLimReglement() ? $facture->getDateLimReglement()->format('d/m/Y') : '',
          'status' => ($facture->getStatus() == 1 and count($facture->getPaiements()) == 0) ? 0 : $facture->getStatus(),
          'url' => $this->generateUrl('aw_doli_facture_view', array('ref' => $facture->getRef(), '_format' => 'pdf'))
        );
      }

      $response = array(
        'draw' => $draw,
        'recordsTotal' => count($factures),
        'recordsFiltered' => count($factures),
        'data' => $data
      );
      return new JsonResponse($response);
    }

    return $this->render('AWDoliBundle:Default:factures.html.twig');
  }

  public function factureViewAction(Facture $facture, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.devis');

    if($this->getUser()->getSociete() and $this->getUser()->getSociete()->getId() != $facture->getSociete()->getId()){
      throw $this->createNotFoundException('Devis introuvable');
    }

    $filepath = $this->getParameter('doli_documents_dir').'/facture/'.$facture->getRef().'/'.$facture->getRef().'.'.$_format;
    if(!file_exists($filepath) or !is_file($filepath)){
      throw $this->createNotFoundException('Fichier '.basename($filepath).' est introuvable.');
    }

    $response = new BinaryFileResponse($filepath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    return $response;
  }
}
