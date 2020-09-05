<?php

namespace AW\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AW\DoliBundle\Entity\Category;

class DefaultController extends Controller
{
  public function indexAction(Request $request, $page=1, Category $category=null)
  {
    if($category and ($category->getExtrafields() === null or $category->getExtrafields()->getAvailableOnline() !== true)){
      return $this->redirectToRoute('aw_shop_homepage');
    }

    $search = $request->get('search');

    $products = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('AWDoliBundle:Product')
      ->getAvailableOnline($page, $category, $search)
    ;

    $nbPages = ceil(count($products)/8);

    return $this->render('AWShopBundle:Default:index.html.twig', array(
      'page' => $page,
      'nbPages' => $nbPages,
      'category' => $category,
      'products' => $products,
      'search' => $search
    ));
  }

  public function sidebarAction(Category $category=null, $search='')
  {
    if($category == null){
      $categories = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('AWDoliBundle:Category')
        ->getRootAvailableOnline()
      ;
    }else{
      $categories = $category->getChildren();
    }

    return $this->render('AWShopBundle::sidebar.html.twig', array(
      'currentCategory' => $category,
      'categories' => $categories,
      'search' => $search
    ));
  }
}
