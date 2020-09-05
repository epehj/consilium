<?php

namespace AW\DoliBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AW\DoliBundle\Entity\User;

class FactureRepository extends \Doctrine\ORM\EntityRepository
{
  public function getList($start, $length, $columns, $orders, User $user)
  {
    $qb = $this->createQueryBuilder('f')
      ->leftJoin('f.societe', 's')
        ->addSelect('s')
      ->leftJoin('f.paiements', 'p')
        ->addSelect('p')
      ->leftJoin('f.extrafields', 'e')
        ->addSelect('e')
      ->where('f.status IN (:status)')
        ->setParameter('status', array(1, 2))
      ->andWhere('e.noRelanceAuto IS NULL')
    ;

    if($user->getSociete()){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->eq('f.societe', ':societe'),
          $qb->expr()->in('f.societe', ':children')
        ))
        ->setParameter('societe', $user->getSociete())
        ->setParameter('children', $user->getSociete()->getChildren())
      ;
    }

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('f.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'refClient':
          $qb
            ->andWhere('f.refClient LIKE :refClient')
            ->setParameter('refClient', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'status':
          if($column['search']['value'] == 0){
            $qb
              ->andWhere('f.status = 1')
              ->groupBy('f.id')
              ->having('COUNT(p) = 0')
            ;
          }elseif($column['search']['value'] == 1){
            $qb
              ->andWhere('f.status = 1')
              ->groupBy('f.id')
              ->having('COUNT(p) > 0')
            ;
          }else{
            $qb
              ->andWhere('f.status = :status')
              ->setParameter('status', $column['search']['value'])
            ;
          }
          break;
      }
    }

    if(empty($orders)){
      $qb
        ->orderBy('f.dateFacture', 'DESC')
      ;
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'f.ref';
            break;
          case 'refClient':
            $sort = 'f.refClient';
            break;
          case 'societe':
            $sort = 's.name';
            break;
          case 'dateFacture':
            $sort = 'f.dateFacture';
            break;
          case 'dateLimReglement':
            $sort = 'f.dateLimReglement';
            break;
          case 'status':
            $sort = 'f.status';
            break;
          default:
            $sort = null;
            break;
        }

        if($sort !== null){
          $dir = strtoupper($order['dir']);
          $qb->addOrderBy($sort, $dir);
        }
      }
    }

    $query = $qb->getQuery();

    $query
			->setFirstResult($start)
			->setMaxResults($length)
		;

    return new Paginator($query, true);
  }
}
