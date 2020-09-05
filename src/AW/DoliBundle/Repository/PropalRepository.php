<?php

namespace AW\DoliBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AW\DoliBundle\Entity\User;

class PropalRepository extends \Doctrine\ORM\EntityRepository
{
  public function getList($start, $length, $columns, $orders, User $user)
  {
    $qb = $this->createQueryBuilder('p')
      ->leftJoin('p.societe', 's')
        ->addSelect('s')
      ->where('p.status IN (:status)')
        ->setParameter('status', array(1, 2))
    ;

    if($user->getSociete()){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->eq('p.societe', ':societe'),
          $qb->expr()->in('p.societe', ':children')
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
            ->andWhere('p.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'refClient':
          $qb
            ->andWhere('p.refClient LIKE :refClient')
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
          $qb
            ->andWhere('p.status = :status')
            ->setParameter('status', $column['search']['value'])
          ;
          break;
      }
    }

    if(empty($orders)){
      $qb
        ->orderBy('p.finValidite', 'DESC')
      ;
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'p.ref';
            break;
          case 'refClient':
            $sort = 'p.refClient';
            break;
          case 'societe':
            $sort = 's.name';
            break;
          case 'finValidite':
            $sort = 'p.finValidite';
            break;
          case 'status':
            $sort = 'p.status';
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
