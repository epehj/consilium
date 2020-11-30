<?php

namespace AW\PlansBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

use AW\PlansBundle\Entity\Commande;
use AW\DoliBundle\Entity\User;
use function Doctrine\ORM\QueryBuilder;

class CommandeRepository extends \Doctrine\ORM\EntityRepository
{
  public function getListQueryBuilder($columns, $orders, User $user)
  {
    $qb = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('c.userContact', 'u')
        ->addSelect('u')
      ->leftJoin('c.userCreation', 'uC')
        ->addSelect('uC')
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
    }else{
      $qb
        ->leftJoin('c.doliCommande', 'dC')
          ->addSelect('dC')
        ->leftJoin('dC.coFactures', 'cf', 'WITH', $qb->expr()->andX( // joindre la table llx_element en se limitant aux liens commande <=> facture
          $qb->expr()->eq('cf.sourcetype', ':sourcetype'),
          $qb->expr()->eq('cf.targettype', ':targettype')
        ))
          ->setParameter('sourcetype', 'commande')
          ->setParameter('targettype', 'facture')
          ->addSelect('cf')
        ->leftJoin('cf.facture', 'f')
          ->addSelect('f')
      ;
    }

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('c.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'refClient':
          $qb
            ->andWhere('c.refClient LIKE :refClient')
            ->setParameter('refClient', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'site':
          $qb
            ->andWhere('c.site LIKE :site')
            ->setParameter('site', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'address':
          $qb
            ->andWhere($qb->expr()->orX(
              $qb->expr()->like('c.address1', ':address'),
              $qb->expr()->like('c.address2', ':address'),
              $qb->expr()->like('c.zip', ':address'),
              $qb->expr()->like('c.town', ':address')
            ))
            ->setParameter('address', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'date':
          $d = explode('/', $column['search']['value'], 4);
          switch(count($d)){
            case 1:
              $value = $d[0];
              break;
            case 2:
              $value = $d[1].'-'.$d[0];
              break;
            case 3:
              $value = $d[2].'-'.$d[1].'-'.$d[0];
              break;
            default:
              $value = $column['search']['value'];
              break;
          }

          $qb
            ->andWhere('c.dateCreation LIKE :date')
            ->setParameter('date', '%'.$value.'%')
          ;
          break;
        case 'userContact':
          $userContact = $this
            ->getEntityManager()
            ->getRepository('AWDoliBundle:User')
            ->find($column['search']['value'])
          ;

          $qb
            ->andWhere('c.userContact = :userContact')
            ->setParameter('userContact', $userContact)
          ;
          break;
        case 'status':
          $qb
            ->andWhere('c.status = :status')
            ->setParameter('status', $column['search']['value'])
          ;
          break;
        case 'releveStatus':
          $qb
            ->andWhere('c.releveStatus = :releveStatus')
            ->setParameter('releveStatus', $column['search']['value'])
          ;
          break;
        case 'urgent':
          if($column['search']['value']){
            $qb->andWhere('c.urgent = true');
          }else{
            $qb->andWhere('c.urgent = false');
          }
          break;
        case 'production':
          $qb
            ->andWhere('c.production = :production')
            ->setParameter('production', $column['search']['value'])
          ;
          break;
        case 'alert':
          $qb
            ->andWhere('c.alert = :alert')
            ->setParameter('alert', $column['search']['value'])
          ;
          break;
        case 'batfr':
          if($column['search']['value']){
            $qb->andWhere('c.batOnlyByFr = true');
          }else{
            $qb->andWhere('c.batOnlyByFr = false');
          }
          break;
        case 'userCreation':
          $userCreation = $this
            ->getEntityManager()
            ->getRepository('AWDoliBundle:User')
            ->find($column['search']['value'])
          ;

          $qb
            ->andWhere('c.userCreation = :userCreation')
            ->setParameter('userCreation', $userCreation)
          ;
          break;
        case 'releveUser':
          $releveUser = $this
            ->getEntityManager()
            ->getRepository('AWDoliBundle:User')
            ->find($column['search']['value'])
          ;

          $qb
            ->andWhere('c.releveUser = :releveUser')
            ->setParameter('releveUser', $releveUser)
          ;
          break;
        case 'factures':
          $qb
            ->andWhere('f.ref LIKE :facture')
            ->setParameter('facture', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'billed':
          if($column['search']['value']){
            $qb
              ->andWhere('dC.billed = true')
            ;
          }else{
            $qb
              ->andWhere('c.doliCommande IS NOT NULL')
              ->andWhere('dC.billed = false')
            ;
          }
          break;
      }
    }

    if(empty($orders)){
      $qb
        ->orderBy('c.urgent', 'DESC')
        ->addOrderBy('c.dateCreation', 'DESC')
      ;
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'c.ref';
            break;
          case 'refClient':
            $sort = 'c.refClient';
            break;
          case 'societe':
            $sort = 's.name';
            break;
          case 'site':
            $sort = 'c.site';
            break;
          case 'address':
            $sort = 'c.address1';
            break;
          case 'date':
            $sort = 'c.dateCreation';
            break;
          case 'dateUpdate':
            $sort = 'c.dateUpdate';
            break;
          case 'userContact':
            $sort = 'u.firstname';
            break;
          case 'status':
            $sort = 'c.status';
            break;
          case 'releveStatus':
            $sort = 'c.releveStatus';
            break;
          case 'urgent':
            $sort = 'c.urgent';
            break;
          case 'production':
            $sort = 'c.production';
            break;
          case 'alert':
            $sort = 'c.alert';
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

    return $qb;
  }

  public function getList($start, $length, $columns, $orders, User $user)
  {
    $qb = $this->getListQueryBuilder($columns, $orders, $user);

    $query = $qb
      ->getQuery()
			->setFirstResult($start)
			->setMaxResults($length)
		;

    return new Paginator($query, true);
  }

  public function getAllList($columns, $orders, User $user)
  {
    $qb = $this->getListQueryBuilder($columns, $orders, $user);

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getListProduction($start, $length, $columns, $orders, User $user, $onlyUserProduction)
  {
    $qb = $this
      ->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('c.userProduction', 'u')
        ->addSelect('u')
      ->where('c.status IN (:inStatus)')
        ->setParameter('inStatus', array(Commande::STATUS_VALIDATED, Commande::STATUS_BAT_MODIF, Commande::STATUS_BAT_VALIDATED))
    ;

    $qb
      ->andWhere($qb->expr()->orX(
        $qb->expr()->isNull('c.releveStatus'),
        $qb->expr()->eq('c.releveStatus', ':releveStatus')
      ))
      ->setParameter('releveStatus', Commande::RELEVE_STATUS_TERMINE)
    ;

    if($onlyUserProduction){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->andX(
            $qb->expr()->neq('c.production', ':notInProduction'),
            $qb->expr()->eq('c.userProduction', ':user')
          ),
          $qb->expr()->eq('c.production', ':waitingProduction')
        ))
          ->setParameter('notInProduction', 0)
          ->setParameter('user', $user)
          ->setParameter('waitingProduction', 1)
      ;
    }

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('c.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'refClient':
          $qb
            ->andWhere('c.refClient LIKE :refClient')
            ->setParameter('refClient', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'site':
          $qb
            ->andWhere('c.site LIKE :site')
            ->setParameter('site', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'date':
          $d = explode('/', $column['search']['value'], 4);
          switch(count($d)){
            case 1:
              $value = $d[0];
              break;
            case 2:
              $value = $d[1].'-'.$d[0];
              break;
            case 3:
              $value = $d[2].'-'.$d[1].'-'.$d[0];
              break;
            default:
              $value = $column['search']['value'];
              break;
          }

          $qb
            ->andWhere('c.dateCreation LIKE :date')
            ->setParameter('date', '%'.$value.'%')
          ;
          break;
        case 'operateur':
          $qb
            ->andWhere('u.firstname LIKE :operateur')
            ->setParameter('operateur', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'status':
          $qb
            ->andWhere('c.status = :status')
            ->setParameter('status', $column['search']['value'])
          ;
          break;
        case 'production':
          $qb
            ->andWhere('c.production = :production')
            ->setParameter('production', $column['search']['value'])
          ;
          break;
        case 'alert':
          $qb
            ->andWhere('c.alert = :alert')
            ->setParameter('alert', $column['search']['value'])
          ;
          break;
        case 'urgent':
          if($column['search']['value']){
            $qb->andWhere('c.urgent = true');
          }else{
            $qb->andWhere('c.urgent = false');
          }
          break;
      }
    }

    if(empty($orders)){
      $qb
        ->orderBy('c.urgent', 'DESC')
        ->addOrderBy('c.dateCreation', 'DESC')
      ;
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'c.ref';
            break;
          case 'refClient':
            $sort = 'c.refClient';
            break;
          case 'societe':
            $sort = 's.name';
            break;
          case 'site':
            $sort = 'c.site';
            break;
          case 'date':
            $sort = 'c.dateCreation';
            break;
          case 'dateUpdate':
            $sort = 'c.dateUpdate';
            break;
          case 'operateur':
            $sort = 'u.firstname';
            break;
          case 'status':
            $sort = 'c.status';
            break;
          case 'urgent':
            $sort = 'c.urgent';
            break;
          case 'production':
            $sort = 'c.production';
            break;
          case 'alert':
            $sort = 'c.alert';
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

  public function getListReleves($start, $length, $columns, $orders)
  {
    $qb = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->where('c.status != 0')
    ;

    $qb
      ->andWhere($qb->expr()->orX(
        $qb->expr()->andX(
          $qb->expr()->eq('c.releve', true),
          $qb->expr()->eq('c.releveStatus', ':releveEnAttente')
        ),
        $qb->expr()->andX(
          $qb->expr()->eq('c.pose', true),
          $qb->expr()->eq('c.releveStatus', ':postEnAttente')
        )
      ))
      ->setParameter('releveEnAttente', Commande::RELEVE_STATUS_EN_ATTENTE)
      ->setParameter('postEnAttente', Commande::POSE_STATUS_EN_ATTENTE)
    ;

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('c.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'site':
          $qb
            ->andWhere('c.site LIKE :site')
            ->setParameter('site', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'address':
          $qb
            ->andWhere($qb->expr()->orX(
              $qb->expr()->like('c.address1', ':address'),
              $qb->expr()->like('c.address2', ':address'),
              $qb->expr()->like('c.zip', ':address'),
              $qb->expr()->like('c.town', ':address')
            ))
            ->setParameter('address', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'urgent':
          if($column['search']['value']){
            $qb->andWhere('c.urgent = true');
          }else{
            $qb->andWhere('c.urgent = false');
          }
          break;
      }
    }

    if(empty($orders)){
      $qb
        ->orderBy('c.urgent', 'DESC')
        ->addOrderBy('c.dateCreation', 'DESC')
      ;
    }else{
      foreach($orders as $order){
        $columnName = $columns[$order['column']]['name'];
        switch($columnName){
          case 'ref':
            $sort = 'c.ref';
            break;
          case 'societe':
            $sort = 's.name';
            break;
          case 'site':
            $sort = 'c.site';
            break;
          case 'address':
            $sort = 'c.address1';
            break;
          case 'date':
            $sort = 'c.dateCreation';
            break;
          case 'dateUpdate':
            $sort = 'c.dateUpdate';
            break;
          case 'status':
            $sort = 'c.releveStatus';
            break;
          case 'urgent':
            $sort = 'c.urgent';
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

  public function getListReceipt($start, $length, $columns)
  {
    $qb = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->where('c.status = :status')
        ->setParameter('status', Commande::STATUS_EN_FABRICATION)
      ->orderBy('c.dateUpdate')
    ;

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('c.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'site':
          $qb
            ->andWhere('c.site LIKE :site')
            ->setParameter('site', '%'.$column['search']['value'].'%')
          ;
          break;
      }
    }

    $query = $qb->getQuery();

    $query
			->setFirstResult($start)
			->setMaxResults($length)
		;

    return new Paginator($query, true);
  }

  public function getListBilling($start, $length, $columns)
  {
    $qb = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('c.doliCommande', 'dC')
        ->addSelect('dC')
      ->where('c.status IN (:status)')
        ->setParameter('status', array(Commande::STATUS_EN_EXPEDITION, Commande::STATUS_CLOSED))
      ->andWhere('dC.billed = false')
      ->orderBy('c.dateUpdate')
    ;

    foreach($columns as $column){
      if(empty($column['search']['value']) and !is_numeric($column['search']['value'])){
        continue;
      }

      switch($column['name']){
        case 'ref':
          $qb
            ->andWhere('c.ref LIKE :ref')
            ->setParameter('ref', '%'.$column['search']['value'].'%')
          ;
          break;
        case 'societe':
          $qb
            ->andWhere('s.name LIKE :socName')
            ->setParameter('socName', '%'.$column['search']['value'].'%')
          ;
          break;
      }
    }

    $query = $qb->getQuery();

    $query
			->setFirstResult($start)
			->setMaxResults($length)
		;

    return new Paginator($query, true);
  }

  public function getListFabrication()
  {
    $qb = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('c.listDet', 'd')
        ->addSelect('d')
      ->where('c.status = :status')
        ->setParameter('status', Commande::STATUS_EN_FABRICATION)
      ->orderBy('c.dateUpdate')
    ;

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function findWithDetail($id)
  {
    $qb = $this->createQueryBuilder('c');

    $qb
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('s.infosPlans', 'i')
        ->addSelect('i')
      ->leftJoin('c.userContact', 'uC')
        ->addSelect('uC')
      ->leftJoin('c.listDet', 'd')
        ->addSelect('d')
      ->leftJoin('c.bats', 'b')
        ->addSelect('b')
      ->leftJoin('c.expedition', 'e')
        ->addSelect('e')
      ->leftJoin('c.doliCommande', 'dC')
        ->addSelect('dC')
      ->leftJoin('dC.coFactures', 'cf', 'WITH', $qb->expr()->andX(
        $qb->expr()->eq('cf.sourcetype', ':sourcetype'),
        $qb->expr()->eq('cf.targettype', ':targettype')
      ))
        ->setParameter('sourcetype', 'commande')
        ->setParameter('targettype', 'facture')
        ->addSelect('cf')
      ->leftJoin('cf.facture', 'f')
        ->addSelect('f')
      ->leftJoin('f.extrafields', 'fe')
        ->addSelect('fe')
      ->where('c.id = :id')
        ->setParameter('id', $id)
      ->orderBy('d.type')
      ->addOrderBy('b.numero')
    ;

    return $qb
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  public function findWithNotes($id)
  {
    $query = $this->createQueryBuilder('c')
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('c.notes', 'n')
        ->addSelect('n')
      ->where('c.id = :id')
        ->setParameter('id', $id)
      ->orderBy('n.date')
      ->getQuery()
    ;

    return $query
      ->getOneOrNullResult()
    ;
  }

  public function findWithProductions($id)
  {
    $qb = $this->createQueryBuilder('c');

    $qb
      ->leftJoin('c.societe', 's')
        ->addSelect('s')
      ->leftJoin('s.infosPlans', 'i')
        ->addSelect('i')
      ->leftJoin('c.productions', 'p')
        ->addSelect('p')
      ->leftJoin('c.listDet', 'd')
        ->addSelect('d')
      ->leftJoin('c.bats', 'b')
        ->addSelect('b')
      ->leftJoin('c.doliCommande', 'dC')
        ->addSelect('dC')
      ->leftJoin('dC.coFactures', 'cf', 'WITH', $qb->expr()->andX(
        $qb->expr()->eq('cf.sourcetype', ':sourcetype'),
        $qb->expr()->eq('cf.targettype', ':targettype')
      ))
        ->setParameter('sourcetype', 'commande')
        ->setParameter('targettype', 'facture')
        ->addSelect('cf')
      ->leftJoin('cf.facture', 'f')
        ->addSelect('f')
      ->leftJoin('f.extrafields', 'e')
        ->addSelect('e')
      ->where('c.id = :id')
        ->setParameter('id', $id)
      ->orderBy('p.dateStart')
      ->addOrderBy('d.type')
    ;

    return $qb
      ->getQuery()
      ->getOneOrNullResult()
    ;
  }

  private function getBetweenDateQueryBuilder(\DateTime $start, \DateTime $end)
  {
    return $this
      ->createQueryBuilder('c')
      ->where('c.status != :status')
        ->setParameter('status', Commande::STATUS_CANCELED)
      ->andWhere('c.dateCreation BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
    ;
  }

  public function countBetweenDate(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getBetweenDateQueryBuilder($start, $end)
      ->select('COUNT(c)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

    public function averagePoseTimeBetweenDate(\DateTime $start, \DateTime $end)
    {
       return $this->getAverageTimePoseQueryBuilder($start, $end)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function averagePoseTimeBetweenDateByUser($user, \DateTime $start, \DateTime $end)
    {
        return $this->getAverageTimePoseQueryBuilder($start, $end)
            ->andWhere('c.releveur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

  public function sumQtyBetweenDate(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getBetweenDateQueryBuilder($start, $end)
      ->leftJoin('c.listDet', 'd')
        ->select('SUM(d.qty)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function getBetweenDateWithBAT(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getBetweenDateQueryBuilder($start, $end)
      ->leftJoin('c.bats', 'b')
        ->addSelect('b')
      ->getQuery()
      ->getResult()
    ;
  }

  public function getBetweenDateBATValid(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getBetweenDateQueryBuilder($start, $end)
      ->leftJoin('c.bats', 'b')
        ->addSelect('b')
      ->andWhere('b.dateValidation IS NOT NULL')
      ->getQuery()
      ->getResult()
    ;
  }

  private function getEnFabricationBetweenDateQueryBuilder(\DateTime $start, \DateTime $end)
  {
    return $this
      ->createQueryBuilder('c')
      ->where('c.status != :status')
        ->setParameter('status', Commande::STATUS_CANCELED)
      ->andWhere('c.dateFabrication BETWEEN :start AND :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
    ;
  }

  public function countEnFabricationBetweenDate(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getEnFabricationBetweenDateQueryBuilder($start, $end)
      ->select('COUNT(c)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function sumQtyEnFabricationBetweenDate(\DateTime $start, \DateTime $end)
  {
    return $this
      ->getEnFabricationBetweenDateQueryBuilder($start, $end)
      ->leftJoin('c.listDet', 'd')
        ->select('SUM(d.qty)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  private function getByStatusQueryBuilder($status)
  {
    $qb = $this
      ->createQueryBuilder('c')
      ->where('c.status = :status')
        ->setParameter('status', $status)
    ;

    if($status == Commande::STATUS_VALIDATED){
      $qb
        ->andWhere($qb->expr()->orX(
          $qb->expr()->isNull('c.releveStatus'),
          $qb->expr()->notIn('c.releveStatus', array(
            Commande::RELEVE_STATUS_EN_ATTENTE,
            Commande::POSE_STATUS_EN_ATTENTE,
            Commande::RELEVE_ANOMALIE
          ))
        ))
      ;
    }

    return $qb;
  }


  private function getByStatusWithReleveQueryBuilder($status)
  {
    $qb = $this
      ->createQueryBuilder('c')
      ->where('c.status = :status')
        ->setParameter('status', $status)
        ;
        $qb->andWhere($qb->expr()->orX(
          $qb->expr()->isNotNull('c.releveStatus'),
          $qb->expr()->notIn('c.releveStatus', array(
            Commande::RELEVE_STATUS_EN_ATTENTE,
            Commande::POSE_STATUS_EN_ATTENTE,
            Commande::RELEVE_ANOMALIE
          ))
        ))
      ;

    return $qb;
  }

  public function countByStatus($status)
  {
    return $this
      ->getByStatusQueryBuilder($status)
        ->select('COUNT(c)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function countByStatusBetweenDate($status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->getByStatusQueryBuilder($status)
        ->select('COUNT(c)')
        ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function countByStatusBetweenDateAndPose($status, \DateTime $start, \DateTime $end)
  {
      return $this
          ->getByStatusQueryBuilder($status)
          ->select('COUNT(c)')
          ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
          ->andWhere('c.pose = true')
          ->getQuery()
          ->getSingleScalarResult();
  }

  public function getCountByReleveBetweenDateQueryBuilder(\DateTime $start, \DateTime $end){
      $qb = $this->createQueryBuilder('c');
      $qb->andWhere($qb->expr()->orX(
          $qb->expr()->isNotNull('c.releveStatus'),
          $qb->expr()->notIn('c.releveStatus', array(
              Commande::RELEVE_STATUS_EN_ATTENTE,
              Commande::POSE_STATUS_EN_ATTENTE,
              Commande::RELEVE_ANOMALIE
          ))
      ))
          ->andWhere($qb->expr()->isNotNull('c.dateReleve'))
          ->select('COUNT(c)')
          ->andWhere('c.dateCreation between :start and :end')
          ->setParameter('start', $start)
          ->setParameter('end', $end)
          ->andWhere('c.releve = true');
      return $qb;
  }
    public function countByReleveBetweenDate(\DateTime $start, \DateTime $end)
    {
      return $this->getCountByReleveBetweenDateQueryBuilder($start, $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByReleveBetweenDateAndByUser($user, \DateTime $start, \DateTime $end){
        return $this->getCountByReleveBetweenDateQueryBuilder($start, $end)
            ->andWhere('c.releveur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

    }
  /** si un releveur est associé, alors la commande est forcément en releve  = true */
  public function countByStatusBetweenDateAndByUser($user, $status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->getByStatusWithReleveQueryBuilder($status)
        ->select('COUNT(c)')
        ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ->andWhere('c.releveur = :user')
            ->setParameter('user', $user)
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function countByStatusBetweenDateAndPoseByUser($user, $status, \DateTime $start, \DateTime $end)
  {
    return $this
      ->getByStatusQueryBuilder($status)
        ->select('COUNT(c)')
        ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ->andWhere('c.releveur = :user')
            ->setParameter('user', $user)
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function sumQtyByStatus($status)
  {
    return $this
      ->getByStatusQueryBuilder($status)
      ->leftJoin('c.listDet', 'd')
        ->select('SUM(d.qty)')
      ->getQuery()
      ->getSingleScalarResult()
    ;
  }

  public function findForAlertUpdate()
  {
    return $this
      ->createQueryBuilder('c')
      ->leftJoin('c.bats', 'b')
        ->addSelect('b')
      ->where('c.status IN (:status)')
      ->setParameter('status', array(
        Commande::STATUS_ATTENTE_VALIDATION,
        Commande::STATUS_VALIDATED,
        Commande::STATUS_BAT_MODIF,
        Commande::STATUS_VALIDATED
      ))
      ->getQuery()
      ->getResult()
    ;
  }

  public function findEmptyGeoCode()
  {
    $qb = $this
      ->createQueryBuilder('c')
    ;

    $qb
      ->where($qb->expr()->orX(
        $qb->expr()->eq('c.releve', true),
        $qb->expr()->eq('c.pose', true)
      ))
      ->andWhere($qb->expr()->orX(
        $qb->expr()->isNull('c.geoCode'),
        $qb->expr()->eq('c.geoCode', ':empty')
      ))
      ->setParameter('empty', 'N;')
    ;

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function countCommandeSaisie(\DateTime $start, \DateTime $end){
      return $this
          ->getBetweenDateQueryBuilder($start, $end)
          ->andWhere('c.releve = true')
          ->select('count(c)')
          ->getQuery()
          ->getSingleScalarResult();
      ;
  }
  public function countPlanSaisi(\DateTime $start, \DateTime $end){
      return $this
          ->getBetweenDateQueryBuilder($start, $end)
          ->leftJoin('c.listDet', 'det')
          ->andWhere('c.releve = true')
          ->select('sum(det.qty)')
          ->getQuery()
          ->getSingleScalarResult();
      ;
  }

  public function findWaitingRelevePose($releve, $pose)
  {
    $qb = $this
      ->createQueryBuilder('c')
    ;

    if($releve and $pose){
      $qb
        ->where($qb->expr()->orX(
          $qb->expr()->eq('c.releve', true),
          $qb->expr()->eq('c.pose', true)
        ))
        ->andWhere($qb->expr()->orX(
          $qb->expr()->eq('c.releveStatus', ':releveEnAttente'),
          $qb->expr()->eq('c.releveStatus', ':postEnAttente')
        ))
        ->setParameter('releveEnAttente', Commande::RELEVE_STATUS_EN_ATTENTE)
        ->setParameter('postEnAttente', Commande::POSE_STATUS_EN_ATTENTE)
      ;
    }elseif($releve){
      $qb
        ->where('c.releve = true')
        ->andWhere('c.releveStatus = :releveEnAttente')
        ->setParameter('releveEnAttente', Commande::RELEVE_STATUS_EN_ATTENTE)
      ;
    }elseif($pose){
      $qb
        ->where('c.pose = true')
        ->andWhere('c.releveStatus = :postEnAttente')
        ->setParameter('postEnAttente', Commande::POSE_STATUS_EN_ATTENTE)
      ;
    }

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

    public function getSumPlans($reference){
        return $this
            ->createQueryBuilder('c')
            ->leftJoin('c.listDet', 'det')
            ->select('sum(det.qty)')
            ->where('c.ref  = :ref')
            ->setParameter('ref', $reference)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByAveragePoseTimeBetweenDateBuilder(\DateTime $start, \DateTime $end)
    {
        return $this->getByStatusQueryBuilder(':status')
            ->setParameter('status', Commande::STATUS_CLOSED)
            ->select('SUM(TIMESTAMPDIFF(DAY, c.dateValidation, c.dateClose))')
            ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->andWhere('c.pose = true')
            ->andWhere('c.dateClose != null');
    }

    public function getAverageTimePoseQueryBuilder(\DateTime $start, \DateTime $end)
    {
        $qb =  $this->getByStatusQueryBuilder(Commande::STATUS_CLOSED);
        return $qb
            ->select('AVG(TIMESTAMPDIFF(DAY, c.dateValidation, c.dateClose))')
            ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->andWhere('c.pose = true')
            ->andWhere($qb->expr()->isNotNull('c.dateClose'));
    }

    public function getAverageTimeReleveQueryBuilder(\DateTime $start, \DateTime $end)
    {
        $qb =  $this
        ->createQueryBuilder('c');
        $qb->where('c.releveStatus = :status')
            ->setParameter('status', Commande::RELEVE_STATUS_TERMINE)
        ->andWhere($qb->expr()->orX(
            $qb->expr()->isNull('c.releveStatus'),
            $qb->expr()->notIn('c.releveStatus', array(
                    Commande::RELEVE_STATUS_EN_ATTENTE,
                    Commande::POSE_STATUS_EN_ATTENTE,
                    Commande::RELEVE_ANOMALIE
                ))
            ))
            ->select('AVG(TIMESTAMPDIFF(DAY, c.dateValidation, c.dateProduction))')
            ->andWhere('c.dateCreation between :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
//            ->andWhere('c.releve = true')
            ->andWhere($qb->expr()->isNotNull('c.dateModification'));
        return $qb;
    }

    public function averageReleveTimeBetweenDateByUser($user, \DateTime $start, \DateTime $end)
    {
        return $this->getAverageTimeReleveQueryBuilder($start, $end)
            ->andWhere('c.releveur = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averageReleveTimeBetweenDate(\DateTime $start, \DateTime $end)
    {
        return $this->getAverageTimeReleveQueryBuilder($start, $end)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
