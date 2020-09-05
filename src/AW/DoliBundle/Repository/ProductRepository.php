<?php

namespace AW\DoliBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AW\DoliBundle\Entity\Category;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{
  public function getAvailableOnline($page=1, Category $category=null, $search)
  {
    $qb = $this
      ->createQueryBuilder('p')
      ->leftJoin('p.extrafields', 'e')
        ->addSelect('e')
      ->where('e.availableOnline = true')
    ;

    if($category){
      $qb
        ->leftJoin('p.categories', 'c')
          ->addSelect('c')
        ->andWhere('c = :category')
          ->setParameter('category', $category)
      ;
    }

    if(!empty($search)){
      $words = explode(' ', $search);
      foreach($words as $key => $word){
        $word = trim($word);
        if(empty($word)){
          continue;
        }

        $word = '%'.$word.'%';

        $qb
          ->andWhere($qb->expr()->orX(
            $qb->expr()->like('p.ref', ':search_ref_'.$key),
            $qb->expr()->like('p.name', ':search_name_'.$key)
          ))
          ->setParameter('search_ref_'.$key, $word)
          ->setParameter('search_name_'.$key, $word)
        ;
      }
    }

    $query = $qb
      ->getQuery()
      ->setFirstResult(($page-1)*8)
      ->setMaxResults(8)
    ;

    return new Paginator($query, true);
  }
}
