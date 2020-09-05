<?php

namespace AW\PlansBundle\EventListener;

use Doctrine\ORM\EntityManager;
use AW\DoliBundle\Service\PricelistUtils;

use AW\PlansBundle\Entity\Commande;
use AW\PlansBundle\Entity\CommandeDet;
use AW\DoliBundle\Entity\Commande as DoliCommande;
use AW\DoliBundle\Entity\CommandeDet as DoliCommandeDet;
use AW\PlansBundle\PDF\Generator as PDFGenerator;

class DoliCommandeListener
{
  private $em;
  private $pricelistUtils;
  private $pdfGenerator;

  public function __construct(EntityManager $em, PricelistUtils $pricelistUtils, PDFGenerator $pdfGenerator)
  {
    $this->em = $em;
    $this->pricelistUtils = $pricelistUtils;
    $this->pdfGenerator = $pdfGenerator;
  }

  public function updateDoliCommandeDet(Commande $commande)
  {
    if($commande->getDoliCommande() === null){
      return;
    }

    // supprimer tous les lignes existantes sur la commande Dolibarr
    foreach($commande->getDoliCommande()->getListDet() as $det){
      $this->em->remove($det);
    }

    // liste des produits dans la clé est la référence de l'article et la valeur la quantité
    $products = array();
    $remises = array();
    $nbPlans = 0;
    foreach($commande->getListDet() as $det){
      $nbPlans += $det->getQty();

      $ref = $det->getMainProductRef();
      if(isset($products[$ref])){
        $products[$ref] += $det->getQty();
        $remises[$ref] += $det->getRemise();
      }else{
        $products[$ref] = $det->getQty();
        $remises[$ref] = $det->getRemise();
      }

      $ref = $det->getFinitionProductRef();
      if($ref){
        if(isset($products[$ref])){
          $products[$ref] += $det->getQty();
        }else{
          $products[$ref] = $det->getQty();
        }
      }
    }

    if($commande->getReleve()){
      $products['P23'] = $nbPlans;
    }

    if($commande->getPose()){
      $products['P25'] = $nbPlans;
    }

    // ajout des products
    $description = $commande->getSite().' '.$commande->getAddress1().' '.$commande->getAddress2().' '.$commande->getZip().' '.$commande->getTown();
    $globalTotalHt = 0;
    $globalTotalTva = 0;
    $erProduct = $this->em->getRepository('AWDoliBundle:Product');
    foreach($products as $ref => $qty){
      $product = $erProduct->findOneByRef($ref);
      if($product === null){
        throw new \Symfony\Component\HttpKernel\Exception\HttpException(500, "Ref ".$ref." introuvable.");
      }

      $price = $this->pricelistUtils->getCustomerPrice($qty, $product, $commande->getSociete());
      if(isset($remises[$ref]) and $remises[$ref] > 0){
        $remiseFixe = round($remises[$ref], 2);
      }else{
        $remiseFixe = 0;
      }
      $tvaTx = ($commande->getSociete()->getCountry() and $commande->getSociete()->getCountry()->getCode() == 'FR') ? $product->getTvaTx() : 0;

      $doliCommandeDet = new DoliCommandeDet();
      $doliCommandeDet
        ->setProduct($product)
        ->setDescription($description)
        ->setPrice($price)
        ->setRemise($remiseFixe)
        ->setQty($qty)
        ->setTvaTx($tvaTx)
        ->setCommande($commande->getDoliCommande())
      ;

      $this->em->persist($doliCommandeDet);

      $globalTotalHt += $doliCommandeDet->getTotalHt();
      $globalTotalTva += $doliCommandeDet->getTotalTva();
    }

    // Déclinaison
    if($commande->getQtyDeclination() > 0){
      $product = $erProduct->findOneByRef('P19');
      if($product === null){
        throw new \Symfony\Component\HttpKernel\Exception\HttpException(500, "Ref ".$ref." introuvable.");
      }

      $price = $this->pricelistUtils->getCustomerPrice($commande->getQtyDeclination(), $product, $commande->getSociete());
      $tvaTx = ($commande->getSociete()->getCountry() and $commande->getSociete()->getCountry()->getCode() == 'FR') ? $product->getTvaTx() : 0;

      $doliCommandeDet = new DoliCommandeDet();
      $doliCommandeDet
        ->setProduct($product)
        ->setDescription($description)
        ->setPrice($price)
        //->setRemise($remiseFixe)
        ->setQty(
          $commande->getQtyDeclination()
        )
        ->setTvaTx($tvaTx)
        ->setCommande($commande->getDoliCommande())
      ;

      $this->em->persist($doliCommandeDet);

      $globalTotalHt += $doliCommandeDet->getTotalHt();
      $globalTotalTva += $doliCommandeDet->getTotalTva();
    }

    // MaJ des totaux
    $globalTotalHt = round($globalTotalHt, 2);
    $globalTotalTva = round($globalTotalTva, 2);
    $commande->getDoliCommande()
      ->setTotalHt($globalTotalHt)
      ->setTotalTtc($globalTotalHt + $globalTotalTva)
      ->setTva($globalTotalTva)
    ;

    $this->em->flush();

    $this->pdfGenerator->generateCommandePDF($commande);
  }
}
