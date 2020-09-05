<?php

namespace AW\PlansBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use AW\PlansBundle\Entity\Expedition;
use AW\PlansBundle\PDF\Generator as PDFGenerator;

use AW\DoliBundle\Entity\CommandeDet as DoliCommandeDet;
use AW\DoliBundle\Service\PricelistUtils;

use AW\ChronopostBundle\Shipping\WebService as ChronopostWS;
use AW\ChronopostBundle\Shipping\ReservedSkybillWithTypeAndMode;

class ExpeditionListener
{
  private $pdfGenerator;
  private $documentsDir;
  private $mailer;
  private $pricelistUtils;
  private $chronopostWS;
  private $emailPlans;
  private $emailExpedition;

  public function __construct(PDFGenerator $pdfGenerator, \Swift_Mailer $mailer, PricelistUtils $pricelistUtils, ChronopostWS $chronopostWS, $documentsDir, $emailPlans, $emailExpedition)
  {
    $this->pdfGenerator = $pdfGenerator;
    $this->mailer = $mailer;
    $this->pricelistUtils = $pricelistUtils;
    $this->documentsDir = $documentsDir;
    $this->chronopostWS = $chronopostWS;
    $this->emailPlans = $emailPlans;
    $this->emailExpedition = $emailExpedition;
  }

  public function prePersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Expedition){
      return;
    }

    if($entity->getMethod() == Expedition::METHOD_NONE){
      return;
    }

    $refs = '';
    $qty = 0;
    foreach($entity->getCommandes() as $commande){
      $refs .= $commande->getRef();
      if($commande->getSociete()->getInfosPlans() and $commande->getSociete()->getInfosPlans()->getShippingCostsByQtyPlans()){
        foreach($commande->getListDet() as $det){
          $qty += $det->getQty();
        }
      }
    }

    $trackingNumbers = $entity->getTrackingNumbers();
    $qty = $qty ? $qty*count($trackingNumbers) : count($trackingNumbers);

    $em = $args->getEntityManager();

    $product = $em
      ->getRepository('AWDoliBundle:Product')
      ->findOneByRef('P24')
    ;
    if($product === null){
      throw new \Exception("Article P24 introuvable");
    }

    if($commande->getSociete()->getInfosPlans() and $commande->getSociete()->getInfosPlans()->getNoShippingCosts()){
      $price = 0;
    }else{
      $price = $this->pricelistUtils->getCustomerPrice($qty, $product, $commande->getSociete());
    }
    $tvaTx = $product->getTvaTx();
    $totalHt = $price * $qty;
    $totalTva = $totalHt * ($tvaTx / 100);

    $paHt = $product->getCostPrice() > 0 ? $product->getCostPrice() : $product->getPmp();

    $doliCommande = $entity->getCommandes()[0]->getDoliCommande();
    $doliCommande
      ->setTotalHt($doliCommande->getTotalHt()+$totalHt)
      ->setTotalTtc($doliCommande->getTotalTtc() + $totalHt + $totalTva)
      ->setTva($doliCommande->getTva() + $totalTva)
    ;

    $doliCommandeDet = new DoliCommandeDet();
    $doliCommandeDet
      ->setProduct($product)
      ->setName($product->getName())
      ->setDescription("Date: ".$entity->getDate()->format('d/m/Y')." - N° suivi: ".implode(',', $trackingNumbers)." - Commande(s): ".$refs)
      ->setPrice($price)
      ->setSubprice($price)
      ->setQty($qty)
      ->setTotalHt($totalHt)
      ->setTvaTx($tvaTx)
      ->setTotalTva($totalTva)
      ->setBuyPriceHt($paHt)
      ->setCommande($doliCommande)
    ;

    $em->persist($doliCommandeDet);
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    if(!$entity instanceof Expedition){
      return;
    }

    if($entity->getMethod() == Expedition::METHOD_CHRONOPOST){
      $methodInfo = unserialize($entity->getMethodInfo());
      $params = new ReservedSkybillWithTypeAndMode(array('reservationNumber' => $methodInfo->return->reservationNumber));

      $query = $this->chronopostWS->getReservedSkybillWithTypeAndMode($params);

      if($query->return->errorCode == 0){
        $content = base64_decode($query->return->skybill);
        $shippingPDF = tempnam(sys_get_temp_dir(), 'AW');
        file_put_contents($shippingPDF, $content);
      }else{
        throw new \Exception($query->return->errorMessage);
      }
    }elseif($entity->getMethod() == Expedition::METHOD_TNT){
      $methodInfo = unserialize($entity->getMethodInfo());
      $content = base64_decode($methodInfo->Expedition->PDFLabels);
      $shippingPDF = tempnam(sys_get_temp_dir(), 'AW');
      file_put_contents($shippingPDF, $content);
    }else{
      $shippingPDF = null;
    }

    $this->pdfGenerator->generateShippingPDF($entity, $shippingPDF);

    $message = (new \Swift_Message())
      ->setFrom($this->emailPlans)
      ->setTo($this->emailExpedition)
      ->setSubject("Expédition Plan // ".$entity->getCommandes()[0]->getSociete()->getName())
      ->setBody("Ci-joints le bon de livraison et eventuellement le bon de chronopost ou TNT.")
      ->attach(\Swift_Attachment::fromPath($this->documentsDir.'/expplan/'.$entity->getId().'/BL.pdf'))
    ;

    $this->mailer->send($message);
  }
}
