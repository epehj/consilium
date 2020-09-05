<?php

namespace AW\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Filesystem\Filesystem;

use AW\DoliBundle\Entity\Societe;
use AW\DoliBundle\Entity\Commande;
use AW\DoliBundle\Entity\CommandeExtrafields;
use AW\DoliBundle\Entity\CommandeDet;
use AW\DoliBundle\Entity\ContactLink;
use AW\DoliBundle\Entity\Product;
use AW\ShopBundle\Entity\Cart;

use AW\DoliBundle\Form\CommandeShopType;

class CartController extends Controller
{
  public function addAction(Request $request, Product $product, $_format)
  {
    if($product->getExtrafields()->getCustomized()){
      throw new \Exception('Les articles personnalisés ne peuvent être commandés en ligne. Merci de nous contacter.');
    }

    $qty = $request->request->get('qty');
    $update = $request->request->get('update') ? true : false;

    if($qty <= 0){
      throw new \Exception('Quantité doit être supérieur à zéro');
    }

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $list = $em
      ->getRepository('AWShopBundle:Cart')
      ->getUserCart($this->getUser())
    ;

    $alreadAdded = false;
    foreach($list as $cart){
      if($product == $cart->getProduct()){
        if($update){
          $cart->setQty($qty);
        }else{
          $cart->setQty($cart->getQty() + $qty);
        }
        $alreadAdded = true;
        break;
      }
    }

    if(!$alreadAdded){
      $cart = new Cart();
      $cart
        ->setUser($this->getUser())
        ->setQty($qty)
        ->setProduct($product)
      ;

      $em->persist($cart);
    }

    $em->flush();

    $qty = $cart->getQty();
    $subprice = $this->get('aw.doli.service.pricelist_utils')->getCustomerPrice($qty, $product, $this->getUser()->getSociete());
    $price = $subprice  * $qty;

    $totalQty = 0;
    $totalPrice = 0;
    $list = $em
      ->getRepository('AWShopBundle:Cart')
      ->getUserCart($this->getUser())
    ;
    foreach($list as $cart){
      $totalQty += $cart->getQty();
      $totalPrice += $this->get('aw.doli.service.pricelist_utils')->getCustomerPrice($cart->getQty(), $cart->getProduct(), $this->getUser()->getSociete()) * $cart->getQty();
    }

    $response = array(
      'qty' => $qty,
      'subprice' => number_format($subprice, 2, ',', ' '),
      'price' => number_format($price, 2, ',', ' '),
      'totalQty' => $totalQty,
      'totalPrice' => number_format($totalPrice, 2, ',', ' '),
    );
    return new JsonResponse($response);
  }

  public function deleteAction(Cart $cart)
  {
    if($cart->getUser() != $this->getUser()){
      throw $this->createNotFoundException('Article non trouvée dans votre panier');
    }

    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $em->remove($cart);
    $em->flush();

    return $this->redirectToRoute('aw_shop_cart_list');
  }

  public function listAction()
  {
    $carts = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWShopBundle:Cart')
      ->getUserCart($this->getUser())
    ;

    return $this->render('AWShopBundle:Cart:list.html.twig', array(
      'carts' => $carts
    ));
  }

  public function confirmOrderAction(Request $request)
  {
    $em = $this
      ->getDoctrine()
      ->getManager()
    ;

    $carts = $em
      ->getRepository('AWShopBundle:Cart')
      ->getUserCart($this->getUser())
    ;

    if(empty($carts)){
      throw new \Exception('Panier vide');
    }

    $commande = new Commande();
    $commande
      ->setSociete($this->getUser()->getSociete())
    ;
    $form = $this->get('form.factory')->create(CommandeShopType::class, $commande);

    $form->handleRequest($request);
    if($request->isMethod('POST') and $request->request->get('action') == 'confirm' and $form->isValid()){
      $em
        ->getConnection()
        ->beginTransaction()
      ;

      $commande
        ->setUserCreation($this->getUser())
        ->setInputReason(14)
      ;

      $extrafields = new CommandeExtrafields();
      $extrafields
        ->setAffectedWh(CommandeExtrafields::WH_GENAS)
        ->setCommande($commande)
      ;

      $em->persist($commande);
      $em->persist($extrafields);
      $em->flush();

      // ajout contact livraison
      $contact = $form->get('contactShipping')->getData();
      if($contact !== null){
        $link = new ContactLink();
        $link
          ->setElementId($commande->getId())
          ->setTypeContact(ContactLink::TYPE_COMMANDE_SHIPPING)
          ->setContact($contact)
        ;
        $em->persist($link);
      }

      $globalTotalHt = 0;
      $globalTotalTva = 0;
      foreach($carts as $cart){
        $price = $this->get('aw.doli.service.pricelist_utils')->getCustomerPrice($cart->getQty(), $cart->getProduct(), $commande->getSociete());

        $det = new CommandeDet();
        $det
          ->setProduct($cart->getProduct())
          ->setQty($cart->getQty())
          ->setPrice($price)
          ->setTvaTx($cart->getProduct()->getTvaTx())
          ->setCommande($commande)
        ;

        $em->persist($det);

        $globalTotalHt += $det->getTotalHt();
        $globalTotalTva += $det->getTotalTva();
      }

      // utile pour le franco de port
      $totalHtWithoutRemise = $globalTotalHt;

      // Appliquer une remise pour une 1ère commande en ligne
      $nbCommandes = $em
        ->getRepository('AWDoliBundle:Commande')
        ->countShopList($commande->getSociete())
      ;
      if($nbCommandes == 1){
        $remise = $em
          ->getRepository('AWDoliBundle:Product')
          ->findOneByRef('REM')
        ;
        $price = -round($globalTotalHt * 10 / 100, 2); // remise de 10%

        $det = new CommandeDet();
        $det
          ->setProduct($remise)
          ->setQty(1)
          ->setPrice($price)
          ->setTvaTx($remise->getTvaTx())
          ->setDescription('Remise de 10% pour une première commande en ligne')
          ->setCommande($commande)
        ;
        $em->persist($det);

        $globalTotalHt += $det->getTotalHt();
        $globalTotalTva += $det->getTotalTva();
        $globalTotalHt = round($globalTotalHt, 2);
        $globalTotalTva = round($globalTotalTva, 2);
      }

      // frais de port
      if($commande->getShippingMethod() == Commande::SHIPMENT_MODE_CHRONO){
        $ch13 = $em
          ->getRepository('AWDoliBundle:Product')
          ->findOneByRef('CH13')
        ;
        $price = $this->get('aw.doli.service.pricelist_utils')->getCustomerPrice(1, $ch13, $commande->getSociete());

        if($totalHtWithoutRemise < 300){
          $remise = 0;
        }else{
          $remise = $price;
        }

        $det = new CommandeDet();
        $det
          ->setProduct($ch13)
          ->setQty(1)
          ->setPrice($price)
          ->setRemise($remise)
          ->setTvaTx($ch13->getTvaTx())
          ->setCommande($commande)
        ;
        $em->persist($det);

        $globalTotalHt += $det->getTotalHt();
        $globalTotalTva += $det->getTotalTva();
        $globalTotalHt = round($globalTotalHt, 2);
        $globalTotalTva = round($globalTotalTva, 2);
      }

      $commande
        ->setTotalHt($globalTotalHt)
        ->setTotalTtc($globalTotalHt + $globalTotalTva)
        ->setTva($globalTotalTva)
      ;

      if(in_array($this->getUser()->getSociete()->getCustomerBad(), array(Societe::CUSTOMER_BAD_RED, Societe::CUSTOMER_BAD_BLACK))){
        $this->addFlash('error', "Votre commande n'a pas pu être validée. Merci de nous contacter pour la finaliser.");
      }else{
        $commande->setStatus(1);
      }

      // clean panier
      foreach($carts as $cart){
        $em->remove($cart);
      }

      $em->flush();

      $em
        ->getConnection()
        ->commit()
      ;

      // ajout des éventuels fichiers joints
      $dir = $this->getParameter('doli_documents_dir').'/commande/'.$commande->getRef();
      if(!file_exists($dir)){
        $fs = new Filesystem();
        $fs->mkdir($dir);
      }
      foreach($form->get('files')->getData() as $file){
        $filename = $commande->getRef().'-'.$file->getClientOriginalName();
        $file->move($dir, $filename);
      }

      $model = $em
        ->getRepository('AWCoreBundle:ModelMail')
        ->findOneByType('SHOP_NEW_COMMANDE');
      ;

      if($model !== null){
        try{
          $r = $this->get('aw.doli.service.api')->get('/webappli/generate/commande/pdf/'.$commande->getId());
          $pathPDF = $this->getParameter('doli_documents_dir').'/commande/'.$commande->getRef().'/'.$commande->getRef().'.pdf';

          $model->render($this->get('twig'), array('commande' => $commande));

          $message = (new \Swift_Message())
            ->setFrom($this->getParameter('email_plans'))
            ->setTo(array($this->getUser()->getEmail() => $this->getUser()->getFullName()))
            ->setBcc($this->getParameter('email_plans'))
            ->setSubject($model->getSubject())
            ->setBody($model->getContent(), 'text/html')
          ;

          if(file_exists($pathPDF)){
            $message
              ->attach(\Swift_Attachment::fromPath($pathPDF))
            ;
          }

          $this->get('mailer')->send($message);
        }catch(\Exception $e){
          $this->addFlash('error', "Échec d'envoi de l'accusé de réception de votre commande.");
        }
      }

      return $this->redirectToRoute('aw_shop_commande_view', array('id' => $commande->getId()));
    }

    return $this->render('AWShopBundle:Cart:confirm_order.html.twig', array(
      'carts' => $carts,
      'form' => $form->createView()
    ));
  }
}
