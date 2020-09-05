<?php

namespace AW\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AW\DoliBundle\Entity\Commande;

class CommandeController extends Controller
{
  public function listAction(Request $request, $_format)
  {
    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWDoliBundle:Commande')
        ->getShopList($start, $length, $columns, $orders, $this->getUser())
      ;

      $data = array();
      foreach($commandes as $commande){
        $data[] = array(
          'id' => $commande->getId(),
          'url' => $this->generateUrl('aw_shop_commande_view', array('id' => $commande->getId())),
          'societe' => $commande->getSociete()->getName(),
          'ref' => $commande->getRef(),
          'date' => $commande->getDateCreation()->format('d/m/Y H:i'),
          'totalht' => number_format($commande->getTotalHt(), 2, ',', ' '),
          'status' => $commande->getStatus()
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

    return $this->render('AWShopBundle:Commande:list.html.twig');
  }

  public function viewAction(Commande $commande)
  {
    if($this->getUser()->getSociete() and $commande->getSociete() != $this->getUser()->getSociete()){
      $foundChild = false;
      foreach($this->getUser()->getSociete()->getChildren() as $child){
        if($commande->getSociete() == $child){
          $foundChild = true;
          break;
        }
      }

      if(!$foundChild){
        throw $this->createNotFoundException('Commande introuvable');
      }
    }

    $contact = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:Contact')
      ->getCommandeContactShipping($commande)
    ;

    return $this->render('AWShopBundle:Commande:view.html.twig', array(
      'commande' => $commande,
      'contact' => $contact
    ));
  }
}
