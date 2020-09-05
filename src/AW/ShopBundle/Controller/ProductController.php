<?php

namespace AW\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Finder\Finder;

use AW\DoliBundle\Entity\Product;

class ProductController extends Controller
{
  public function viewAction(Product $product)
  {
    if($product->getExtrafields() === null or $product->getExtrafields()->getAvailableOnline() !== true){
      throw $this->createNotFoundException('Produit introuvable');
    }

    $prices = $this->getUser()->getSociete() ? $this->get('aw.doli.service.pricelist_utils')->getListCustomerPrice($product, $this->getUser()->getSociete()) : array();

    return $this->render('AWShopBundle:Product:view.html.twig', array(
      'product' => $product,
      'prices' => $prices
    ));
  }

  public function imageAction(Request $request, Product $product, $version)
  {
    if($version == 'thumbnail'){
      $dir = $this->getParameter('doli_documents_dir').'/produit/'.$product->getRef().'/thumbs';
    }else{
      $dir = $this->getParameter('doli_documents_dir').'/produit/'.$product->getRef();
    }

    if(file_exists($dir)){
      $finder = new Finder();
      foreach($finder->files()->depth('== 0')->in($dir) as $file){
        if(in_array($file->getExtension(), array('jpg', 'png'))){
          $thumbnailPath = $file->getRealPath();
          break;
        }
      }
    }

    if(!isset($thumbnailPath)){
      $thumbnailPath = $this->get('kernel')->getRootDir().'/../web/img/nophoto.png';
    }

    $md5 = md5_file($thumbnailPath);
    $response = new Response();
    $response->setEtag($md5);
    $response->setPublic();
    if($response->isNotModified($request)){
      return $response;
    }

    $response = new BinaryFileResponse($thumbnailPath);
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
    $response->setEtag($md5);
    $response->setPublic();
    return $response;
  }
}
