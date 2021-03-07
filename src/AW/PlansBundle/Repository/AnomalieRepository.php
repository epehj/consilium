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

    public function getAnomaliesPose(){
        return $this->getQueryBuilder()->where('a.anoPose = true');
    }

    public function getAnomaliesReleve(){
        return $this->getQueryBuilder()->where('a.anoPose = false');
    }

}
