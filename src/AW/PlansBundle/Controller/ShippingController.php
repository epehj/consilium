<?php

namespace AW\PlansBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\Expedition;
use AW\DoliBundle\Entity\Societe;

use AW\PlansBundle\Form\ExpeditionChronopostType;
use AW\PlansBundle\Form\ExpeditionTNTType;

use AW\ChronopostBundle\Shipping\Header;
use AW\ChronopostBundle\Shipping\Shipper;
use AW\ChronopostBundle\Shipping\Customer;
use AW\ChronopostBundle\Shipping\Recipient;
use AW\ChronopostBundle\Shipping\Skybill;
use AW\ChronopostBundle\Shipping\SkybillParams;
use AW\ChronopostBundle\Shipping\MultiParcelWithReservation;

class ShippingController extends Controller
{
  public function receiptListAction(Request $request, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see_expedition');

    if($_format == 'json'){
      $draw = $request->query->get('draw') ? $request->query->get('draw') : 1;
      $start = $request->query->get('start') ? $request->query->get('start') : 0;
      $length = $request->query->get('length') ? $request->query->get('length') : 20;
      $columns = $request->query->get('columns') ? $request->query->get('columns') : array();

      $commandes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWPlansBundle:Commande')
        ->getListReceipt($start, $length, $columns)
      ;

      $now = new \DateTime();

      $data = array();
      foreach($commandes as $commande){
        $intervalFab = $commande->getDateFabrication()->diff($now);

        $data[] = array(
          'id' => $commande->getId(),
          'url' => $this->generateUrl('aw_plans_view', array('id' => $commande->getId())),
          'ref' => $commande->getRef(),
          'societe' => $commande->getSociete()->getName(),
          'site' => $commande->getSite(),
          'datefabrication' => $commande->getDateFabrication() ? $commande->getDateFabrication()->format('d/m/Y') : null,
          'alert' => ($intervalFab !== false and ($intervalFab->y > 0 or $intervalFab->m > 0 or $intervalFab->d > 3)) ? true : false
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

    return $this->render('AWPlansBundle:Shipping:receiptList.html.twig');
  }

  public function listAction()
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see_expedition');

    $sql = "SELECT COUNT(DISTINCT `aw_commandeplan`.`rowid`) AS `qty`, GROUP_CONCAT(DISTINCT `aw_commandeplan`.`rowid`) AS `ids`, SUM(`aw_commandeplandet`.`qty`) AS `planqty`,";
    $sql.= " `aw_commandeplan`.`shipping_recipient`, `aw_commandeplan`.`shipping_address1`, `aw_commandeplan`.`shipping_address2`,";
    $sql.= " `aw_commandeplan`.`shipping_zip`, `aw_commandeplan`.`shipping_town`, `llx_c_country`.`label` AS `country_label`,";
    $sql.= " `llx_societe`.`nom` AS `soc_nom`";
    $sql.= " FROM `aw_commandeplan`";
    $sql.= " LEFT JOIN `aw_commandeplandet` ON `aw_commandeplandet`.`fk_commande` = `aw_commandeplan`.`rowid`";
    $sql.= " LEFT JOIN `llx_c_country` ON `llx_c_country`.`rowid` = `aw_commandeplan`.`shipping_fk_country`";
    $sql.= " LEFT JOIN `llx_societe` ON `llx_societe`.`rowid` = `aw_commandeplan`.`fk_societe`";
    $sql.= " WHERE `aw_commandeplan`.`status` = :status";
    $sql.= " GROUP BY `aw_commandeplan`.`fk_societe`, `aw_commandeplan`.`shipping_recipient`, `aw_commandeplan`.`shipping_address1`, `aw_commandeplan`.`shipping_address2`,";
    $sql.= " `aw_commandeplan`.`shipping_zip`, `aw_commandeplan`.`shipping_town`, `aw_commandeplan`.`shipping_fk_country`";

    $stmt = $this
      ->getDoctrine()
      ->getManager()
      ->getConnection()
      ->prepare($sql)
    ;
    $stmt->execute(array(
      'status' => Commande::STATUS_RECEIVED
    ));

    $list = $stmt->fetchAll();

    return $this->render('AWPlansBundle:Shipping:list.html.twig', array(
      'list' => $list
    ));
  }

  public function newAction(Request $request, $ids, $method)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see_expedition');

    $listIds = explode(',', $ids);

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $er = $em
      ->getRepository('AWPlansBundle:Commande')
    ;

    $expedition = new Expedition();
    foreach($listIds as $id){
      $commande = $er->findWithDetail($id);
      if($commande === null){
        throw new \Exception("Commande ID ".$id." introuvable");
      }

      $expedition->addCommande($commande);
    }

    if($method == 'none'){
      $expedition
        ->setDate(new \DateTime())
        ->setMethod(Expedition::METHOD_NONE)
      ;

      $em->persist($expedition);

      foreach($expedition->getCommandes() as $commande){
        $commande
          ->setExpedition($expedition)
          ->updateStatus($this->getUser(), Commande::STATUS_EN_EXPEDITION)
        ;
      }
      $em->flush();

      $this->get('aw_plans.pdf.generator')->generateShippingPDF($expedition);

      return $this->redirectToRoute('aw_plans_shipping_view', array('id' => $expedition->getId()));
    }elseif($method == 'chronopost'){
      $expedition->setMethod(Expedition::METHOD_CHRONOPOST);
      $form = $this->get('form.factory')->create(ExpeditionChronopostType::class, $expedition);
      $form->handleRequest($request);
    }elseif($method == 'tnt'){
      $expedition->setMethod(Expedition::METHOD_TNT);
      $form = $this->get('form.factory')->create(ExpeditionTNTType::class, $expedition);
      $form->handleRequest($request);
    }

    if($request->isMethod('POST') and $form->isValid()){
      $customerBad = $expedition->getCommandes()[0]->getSociete()->getCustomerBad();
      if($customerBad == Societe::CUSTOMER_BAD_BLUE or $customerBad == Societe::CUSTOMER_BAD_PURPLE){
        throw new \Exception("Impossible d'expédier les commandes: client bloqué");
      }

      $expedition->setDate(new \DateTime());

      if($expedition->getMethod() == Expedition::METHOD_CHRONOPOST){
        $header = new Header(array(
          'accountNumber' => $this->getParameter('chronopost.account')
        ));

        $shipper = new Shipper(array(
          'shipperName'     => 'CONSILIUM',
          'shipperAdress1'  => '',
          'shipperZipCode'  => '',
          'shipperCity'     => '',
          'shipperCountry'  => 'FR',
          'shipperEmail'    => '',
          'shipperPhone'    => ''
        ));

        $customer = new Customer(array(
          'customerName'     => 'CONSILIUM',
          'customerAdress1'  => '',
          'customerZipCode'  => '',
          'customerCity'     => '',
          'customerCountry'  => 'FR',
          'customerEmail'    => '',
          'customerPhone'    => ''
        ));

        $commande = $expedition->getCommandes()[0];
        $recipient = new Recipient(array(
          'recipientName'     => $commande->getShippingRecipient(),
          'recipientAdress1'  => $commande->getShippingAddress1(),
          'recipientAdress2'  => $commande->getShippingAddress2(),
          'recipientZipCode'  => $commande->getShippingZip(),
          'recipientCity'     => $commande->getShippingTown(),
          'recipientCountry'  => $commande->getShippingCountry()->getCode()
        ));

        $skybill = new Skybill(array(
          'productCode' => $form->get('productCode')->getData(),
          'shipDate'    => new \DateTime(),
          'shipHour'    => date('m'),
          'weight'      => $form->get('weight')->getData()
        ));

        $params = new MultiParcelWithReservation(array(
          'headerValue'         => $header,
          'shipperValue'        => $shipper,
          'customerValue'       => $customer,
          'recipientValue'      => $recipient,
          'skybillValue'        => $skybill,
          'skybillParamsValue'  => new SkybillParams(),
          'password'            => $this->getParameter('chronopost.password'),
          'numberOfParcel'      => $form->get('numberOfParcel')->getData()
        ));

        try{
          $query = $this->get('aw_chronopost.webservice')->shippingMultiParcelWithReservation($params);
          if($query->return->errorCode == 0){
            $methodInfo = serialize($query);
          }else{
            $this->addFlash('error', $query->return->errorMessage);
          }
        }catch(\Exception $e){
          $this->addFlash('error', $e->getMessage());
        }
      }

      if($expedition->getMethod() == Expedition::METHOD_TNT){
        $params = array();
        $params['shippingDate'] = $expedition->getDate()->format('Y-m-d');
        $params['accountNumber'] = $this->getParameter('tnt.account');
        $params['sender'] = array(
          'name'          => 'CONSILIUM',
          'address1'      => '',
          'zipCode'       => '',
          'city'          => '',
          'emailAddress'  => ''
        );
        $params['receiver'] = array(
          'name'      => $commande->getShippingRecipient(),
          'address1'  => $commande->getShippingAddress1(),
          'address2'  => $commande->getShippingAddress2(),
          'zipCode'   => $commande->getShippingZip(),
          'city'      => $commande->getShippingTown()
        );
        $params['serviceCode'] = $form->get('serviceCode')->getData();
        $params['quantity'] = $form->get('quantity')->getData();
        $params['labelFormat'] = 'STDA4';
        $params['parcelsRequest'] = array();
        for($i=0;$i<$form->get('quantity')->getData();$i++){
          $params['parcelsRequest'][] = array(
            'sequenceNumber' => $i+1,
            'customerReference' => '',
            'weight' => $form->get('weight')->getData()
          );
        }

        try{
          $query = $this->get('aw_tnt.webservice')->expeditionCreation($params);
          $query->Expedition->PDFLabels = base64_encode($query->Expedition->PDFLabels); //encode le binaire pour être sérialiser
          $methodInfo = serialize($query);
        }catch(\SoapFault $e){
          $this->addFlash('error', $e->getMessage());
        }catch(\Exception $e){
          $this->addFlash('error', $e->getMessage());
        }
      }

      if(isset($methodInfo)){
        $expedition->setMethodInfo($methodInfo);
        $em->persist($expedition);

        foreach($expedition->getCommandes() as $commande){
          $commande
            ->setExpedition($expedition)
            ->updateStatus($this->getUser(), Commande::STATUS_EN_EXPEDITION)
          ;
        }
        $em->flush();

        return $this->redirectToRoute('aw_plans_shipping_view', array('id' => $expedition->getId()));
      }
    }

    return $this->render('AWPlansBundle:Shipping:new.html.twig', array(
      'expedition' => $expedition,
      'ids' => $ids,
      'form' => isset($form) ? $form->createView() : null
    ));
  }

  public function viewAction(Expedition $expedition, $_format)
  {
    $this->denyAccessUnlessGranted('webappli.cmdplan.see_expedition');

    if($_format == 'pdf'){
      $filepath = $this->getParameter('documents_dir').'/expplan/'.$expedition->getId().'/BL.pdf';
      if(!file_exists($filepath) or !is_file($filepath)){
        throw $this->createNotFoundException('Fichier '.$filepath.' est introuvable.');
      }

      $response = new BinaryFileResponse($filepath);
      $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
      return $response;
    }

    return $this->render('AWPlansBundle:Shipping:view.html.twig', array(
      'expedition' => $expedition
    ));
  }
}
