<?php

namespace ProjectBundle\Repository;

/**
 * CustomerPaymentBankTransferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerPaymentBankTransferRepository extends \Doctrine\ORM\EntityRepository
{
    private $qb;

    function findAllData($arr_query_data=false){
        $this->qb = $this->createQueryBuilder('cpbt')
            ->select('cpbt')
            ->orderBy('cpbt.createdAt', 'DESC');

        if(isset($arr_query_data['q'])){
            $q = $arr_query_data['q'];
            $this->qb->where($this->qb->expr()->orX(
                $this->qb->expr()->like('cpbt.orderNumber', ':query')
            ))
            ->setParameter('query', '%'.$q.'%');
        }
        return $this->qb;
    }

    function findAllDataJoin($arr_query_data=false){
        $this->qb = $this->findAllData();
        $this->qb->addSelect('codit');
        $this->qb->innerJoin('cpbt.customerOrder','codit');
        return $this->qb;
    }

    function findCustomerPaymentBankTransferByOrder($order){
        $this->qb = $this->findAllData();

        $this->qb->addSelect('ba');
        $this->qb->leftJoin('cpbt.bankAccount','ba');

        $this->qb->andWhere('cpbt.customerOrder = :order')
            ->setParameter(':order', $order);
        return $this->qb;
    }

}
