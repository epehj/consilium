<?php

namespace AW\DoliBundle\Repository;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

use AW\DoliBundle\Entity\User;
use AW\DoliBundle\Entity\Societe;

class CommandeRepository extends \Doctrine\ORM\EntityRepository
{
  /**
   * @deprecated getLastRef is deprecated. Use getNextRef method instead.
   */
  public function getLastRef($mask)
  {
    @trigger_error('getLastRef method is deprecated. Use getNextRef method instead.', E_USER_DEPRECATED);

    $query = $this->_em
      ->createQuery("SELECT SUBSTRING(MAX(c.ref), 7) AS ref FROM AWDoliBundle:Commande c WHERE REGEXP(c.ref, '^AW[0-9]{8}$') = true");

    return $query->getSingleScalarResult();
  }

  public function getNextRef($base='CO', $razYearFiscal=true)
  {
    // default mask Dolibarr CO{yy}{mm}{0000@0}
    // code https://github.com/Dolibarr/dolibarr/blob/5.0/htdocs/core/lib/functions2.lib.php#L712
    $dql = "SELECT MAX(SUBSTRING(c.ref, 7, 4)) AS value FROM AWDoliBundle:Commande c";
    $dql.= " WHERE c.ref LIKE '".$base."________'";
    $dql.= " AND c.ref NOT LIKE '(PROV%)'";

    if($razYearFiscal){
      $dql.= " AND ((SUBSTRING(c.ref, 3, 2) = '".date('y')."' AND SUBSTRING(c.ref, 5, 2) >= '07')";
      $dql.= " OR (SUBSTRING(c.ref, 3, 2) = '".(date('y')+1)."' AND SUBSTRING(c.ref, 5, 2) < '07'))";
    }

    $query = $this->_em
      ->createQuery($dql);

    try{
      $value = $query->getSingleScalarResult();
    }catch(NoResultException $e){
      $value = 0;
    }

    return $base.date('ym').str_pad(strval($value+1), 4, '0', STR_PAD_LEFT);
  }

  public function getShopList($start, $length, $columns, $orders, User $user)
  {
    $qb = $this->createQueryBuilder('c')
      ->andWhere('c.inputReason = 14')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
    ;

    if($user->getSociete()){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->eq('c.societe', ':societe'),
          $qb->expr()->in('c.societe', ':children')
        ))
        ->setParameter('societe', $user->getSociete())
        ->setParameter('children', $user->getSociete()->getChildren())
      ;
    }

    if(empty($orders)){
      $qb->orderBy('c.dateCommande', 'DESC');
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'c.ref';
            break;
          case 'date':
            $sort = 'c.dateCreation';
            break;
          case 'totalht':
            $sort = 'c.totalHt';
            break;
          case 'status':
            $sort = 'c.status';
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

    $query = $qb
      ->getQuery()
			->setFirstResult($start)
			->setMaxResults($length)
		;

    return new Paginator($query, true);
  }

  public function countShopList(Societe $societe)
  {
    return $this->createQueryBuilder('c')
      ->select('COUNT(c)')
      ->andWhere('c.inputReason = 14')
      ->andWhere('c.societe = :societe')
        ->setParameter('societe', $societe)
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }
}
