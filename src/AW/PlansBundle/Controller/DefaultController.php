<?php

namespace AW\PlansBundle\Controller;

use AW\PlansBundle\Entity\CommandeST;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\Commande;

class DefaultController extends Controller
{
  public function listAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getList($start, $length, $columns, $orders, $this->getUser())
      ;

      $data = array();
      foreach($commandes as $commande){
        $factures = array();

        if(!$this->getUser()->getSociete() and $commande->getDoliCommande()){
          foreach($commande->getDoliCommande()->getCoFactures() as $coFacture){
            $factures[] = $coFacture->getFacture()->getRef();
          }
        }

        $data[] = array(
          'id' => $commande->getId(),
          'url' => $this->generateUrl('aw_plans_view', array('id' => $commande->getId())),
          'ref' => $commande->getRef(),
          'listDet' => $this
              ->getDoctrine()
              ->getManager()
              ->getRepository('AWPlansBundle:Commande')->getSumPlans($commande->getRef()),
          'refClient' => $commande->getRefClient(),
          'societe' => $commande->getSociete()->getName(),
          'userContact' => $commande->getUserContact() ? $commande->getUserContact()->getFullname() : '',
          'site' => $commande->getSite(),
          'address1' => $commande->getAddress1(),
          'address2' => $commande->getAddress2(),
          'zip' => $commande->getZip(),
          'town' => $commande->getTown(),
          'releveNote' => $commande->getReleveNote(),
          'date' => $commande->getDateCreation()->format('d/m/Y'),
          'dateUpdate' => $commande->getDateUpdate() ? $commande->getDateUpdate()->format('d/m/Y') : '',
          'status' => $commande->getStatus(),
          'statusLabel' => $commande->getStatusLabel(),
          'releveStatusLabel' => $commande->getReleveStatusLabel(),
          'production' => $commande->getProduction(),
          'urgent' => $commande->getUrgent(),
          'alert' => $commande->getAlert(),
          'userCreation' => $commande->getUserCreation()->getFullname(),
          'releveUser' => null,
          'factures' => implode(' ', $factures),
          'billed' => ($commande->getDoliCommande() and $commande->getDoliCommande()->getBilled()) ? 'Oui' : 'Non'
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

    if($_format == 'csv'){
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();
      $orders = $request->query->get('order') ? $request->query->get('order') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getAllList($columns, $orders, $this->getUser())
      ;

      $tmpfile = tempnam(sys_get_temp_dir(), 'AW');

      $handle = fopen($tmpfile, 'w+');

      $header = array();
      foreach($this->getUser()->getParam()->getPlansDataTablesState()['columns'] as $index => $column){
        if($column['visible'] == 'false'){
          continue;
        }

        switch($columns[$index]['name']){
          case 'ref':
            $header[] = 'N° commande';
            break;
          case 'refClient':
            $header[] = 'Ref. Client';
            break;
          case 'societe':
            $header[] = 'Societe';
            $header[] = 'Code-barres';
            break;
          case 'site':
            $header[] = 'Site';
            break;
          case 'address':
            $header[] = 'Adresse';
            $header[] = 'Adresse complémentaire';
            $header[] = 'Code postal';
            $header[] = 'Ville';
            break;
          case 'date':
            $header[] = 'Date';
            break;
          case 'dateUpdate':
            $header[] = 'Dernière mise à jour';
            break;
          case 'userContact':
            $header[] = 'Contact';
            if(!$this->getUser()->getSociete() or
              ($this->getUser()->getSociete()->getInfosPlans() and $this->getUser()->getSociete()->getInfosPlans()->getAllowContactBat())
            ){
              $header[] = 'Contact BAT';
            }
            break;
          case 'status':
            $header[] = 'Statut';
            break;
          case 'releveStatus':
            $header[] = 'Statut Relevé/Pose';
            break;
          case 'releveNote':
            $header[] = 'Note Relevé/Pose';
            break;
        }
      }
      fputcsv($handle, $header);

      foreach($commandes as $commande){
        $line = array();
        foreach($this->getUser()->getParam()->getPlansDataTablesState()['columns'] as $index => $column){
          if($column['visible'] == 'false'){
            continue;
          }

          switch($columns[$index]['name']){
            case 'ref':
              $line[] = $commande->getRef();
              break;
            case 'refClient':
              $line[] = $commande->getRefClient();
              break;
            case 'societe':
              $line[] = $commande->getSociete()->getName();
              $line[] = $commande->getSociete()->getBarcode();
              break;
            case 'site':
              $line[] = $commande->getSite();
              break;
            case 'address':
              $line[] = $commande->getAddress1();
              $line[] = $commande->getAddress2();
              $line[] = $commande->getZip();
              $line[] = $commande->getTown();
              break;
            case 'date':
              $line[] = $commande->getDateCreation()->format('d/m/Y');
              break;
            case 'dateUpdate':
              $line[] = $commande->getDateUpdate() ? $commande->getDateUpdate()->format('d/m/Y') : '';
              break;
            case 'userContact':
              $line[] = $commande->getUserContact() ? $commande->getUserContact()->getFullname() : '';
              if(!$this->getUser()->getSociete() or
                ($this->getUser()->getSociete()->getInfosPlans() and $this->getUser()->getSociete()->getInfosPlans()->getAllowContactBat())
              ){
                $line[] = $commande->getContactBATName() ? $commande->getContactBATName().' '.$commande->getContactBATPhone().' '.$commande->getContactBATEmail() : '';
              }
              break;
            case 'status':
              $line[] = $commande->getStatusLabel();
              break;
            case 'releveStatus':
              $line[] = $commande->getReleveStatusLabel();
              break;
            case 'releveNote':
              $line[] = $commande->getReleveNote();
              break;
          }
        }
        fputcsv($handle, $line);
      }

      fclose($handle);

      $response = new BinaryFileResponse($tmpfile, 200, array('Content-Type' => 'text/csv; charset=utf-8'));
      $response
        ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.csv')
        ->deleteFileAfterSend(true)
      ;

      return $response;
    }

    if($_format == 'js'){
      return $this->render('AWPlansBundle:Default:list.js.twig');
    }

    $contacts = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:User')
      ->getSocieteUsers($this->getUser()->getSociete())
    ;

    if($this->getUser()->getSociete()){
      $users = array();
    }else{
      $users = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWDoliBundle:User')
        ->findBy(
          array('status' => 1),
          array('firstname' => 'asc')
        )
      ;
    }

    return $this->render('AWPlansBundle:Default:list.html.twig', array(
      'listStatus' => Commande::$statusName,
      'listReleveStatus' => Commande::$releveStatusName,
      'contacts' => $contacts,
      'users' => $users
    ));
  }

  /**
   * @ParamConverter("commande", options={"repository_method" = "findWithDetail"})
   */
  public function viewAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW', $commande);

    // FIXME : hack degueu pour afficher les anciennes commandes (qui n'ont pas de commandes ST associées)
      if($commande->getCommandeST() == null)
          $commande->setCommandeST(new CommandeST());
    return $this->render('AWPlansBundle:Default:view.html.twig', array(
      'commande' => $commande
    ));
  }

  /**
   * @ParamConverter("commande")
   */
  public function mailAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW_MAIL', $commande);

    return $this->render('AWPlansBundle:Default:mail.html.twig', array(
      'commande' => $commande
    ));
  }

  /**
   * @ParamConverter("commande")
   */
  public function suiviAction(Commande $commande)
  {
    $this->denyAccessUnlessGranted('VIEW_SUIVI', $commande);

    return $this->render('AWPlansBundle:Default:suivi.html.twig', array(
      'commande' => $commande
    ));
  }

  public function billingAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see_facturation');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getListBilling($start, $length, $columns)
      ;

      $data = array();
      foreach($commandes as $commande){
        $data[] = array(
          'id' => $commande->getId(),
          'url' => $this->generateUrl('aw_plans_view', array('id' => $commande->getId())),
          'ref' => $commande->getRef(),
          'societe' => $commande->getSociete()->getName(),
          'dateUpdate' => $commande->getDateUpdate() ? $commande->getDateUpdate()->format('d/m/Y') : '',
          'status' => $commande->getStatus(),
          'statusLabel' => $commande->getStatusLabel()
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

    return $this->render('AWPlansBundle:Default:billing.html.twig');
  }
}
