<?php

namespace AW\PlansBundle\Repository;

class AnomalieRepository extends \Doctrine\ORM\EntityRepository
{

    public function getQueryBuilder(){
        return $this->createQueryBuilder('a');
    }

    public function getAnomalieFromId($id){
        return $this->getQueryBuilder()->where('a.id = :id')->setParameter('id', $id);
//        return $this->findOneBy(array('id' => $id));
    }

}
