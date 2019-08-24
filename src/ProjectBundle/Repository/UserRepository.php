<?php

namespace ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
	public function getAllClientUser($arr_query_data=false)
	{
		//QueryBuilder Expr
		$qb = $this->createQueryBuilder('u');
		$qb->where($qb->expr()->andX(
			$qb->expr()->like('u.roles', ':user_roles')
		))
		->setParameter('user_roles', '%ROLE_CLIENT%')
		->orderBy('u.createdAt', 'DESC');

		$q = $arr_query_data['q'];
		if($q){
			$qb->andWhere($qb->expr()->orX(
				$qb->expr()->like('u.firstName', ':query'),
				$qb->expr()->like('u.lastName', ':query'),
				$qb->expr()->like('u.companyName', ':query'),
				$qb->expr()->like('u.phoneNumber', ':query'),
				$qb->expr()->like('u.email', ':query'),
				$qb->expr()->like("CONCAT(u.firstName, ' ', u.lastName)", ':query')
			))
			->setParameter('query', '%'.$q.'%');
		}
		return $qb;
	}

	public function getAllAdminUser()
	{
		//QueryBuilder Expr
		$qb = $this->createQueryBuilder('u');
		$qb->where($qb->expr()->andX(
			$qb->expr()->notLike('u.roles', ':user_roles'),
			$qb->expr()->notLike('u.roles', ':user_roles2')
		))
		->setParameter('user_roles', '%ROLE_CUSTOMER%')
		->setParameter('user_roles2', '%ROLE_CLIENT%')
		->orderBy('u.roles', 'ASC');
		return $qb;

		//QueryBuilder
		// $qb->where('u.roles NOT LIKE :user_roles')
		//     ->setParameter('user_roles', '%ROLE_CUSTOMER%')
		//     ->orderBy('u.roles', 'ASC');
		// return $qb;

		//DQL
		// return $query = $this->getEntityManager()
		// 		->createQuery(
		// 				'SELECT u FROM ProjectBundle:User u
		// 				WHERE u.roles NOT LIKE :user_roles
		// 				ORDER BY u.roles ASC'
		// 		)->setParameter('user_roles', '%ROLE_CUSTOMER%')
		// 		->getResult();

		//find by relation user or filed
		// $access_tokens = $em->getRepository(AccessToken::class)->findByUser($user);
	}

	/*
	public function getUser($userId)
	{
	}
	*/

	public function getAllMemberByData($arr_query_data)
	{
		$q = $arr_query_data['q'];
		//QueryBuilder
		$qb = $this->createQueryBuilder('u');
		$qb->where('u.roles LIKE :user_roles')
			->setParameter('user_roles', '%ROLE_CUSTOMER%')
			->orderBy('u.roles', 'ASC');

		if($q){
			$qb->andWhere($qb->expr()->orX(
			$qb->expr()->like('u.firstName', ':query'),
				$qb->expr()->like('u.lastName', ':query'),
				$qb->expr()->like('u.phoneNumber', ':query'),
				$qb->expr()->like('u.email', ':query')
			))
			->setParameter('query', '%'.$q.'%');
		}
		return $qb;
	}
	public function getAllMemberB2BByData($arr_query_data)
	{
		$q = $arr_query_data['q'];
		//QueryBuilder
		$qb = $this->createQueryBuilder('u');
		$qb->where('u.roles LIKE :user_roles')
			->setParameter('user_roles', '%ROLE_CLIENT%')
			->orderBy('u.roles', 'ASC');

		if($q){
			$qb->andWhere($qb->expr()->orX(
			$qb->expr()->like('u.firstName', ':query'),
				$qb->expr()->like('u.lastName', ':query'),
				$qb->expr()->like('u.phoneNumber', ':query'),
				$qb->expr()->like('u.email', ':query')
			))
			->setParameter('query', '%'.$q.'%');
		}
		return $qb;
	}
	public function getMemberB2BById($id)
	{
		//QueryBuilder
		$qb = $this->createQueryBuilder('u');
		$qb->where('u.roles LIKE :user_roles')
			->setParameter('user_roles', '%ROLE_CLIENT%')
			->andWhere('u.id = :user_id')
			->setParameter('user_id', $id);
		return $qb;
	}

	public function getMemberById($id)
	{
		//QueryBuilder
		$qb = $this->createQueryBuilder('u');
		$qb->where('u.roles LIKE :user_roles')
			->setParameter('user_roles', '%ROLE_CUSTOMER%')
			->andWhere('u.id = :user_id')
			->setParameter('user_id', $id);
		return $qb;
	}

	public function getActiveMemberByEmail($email)
	{
		//QueryBuilder
		$qb = $this->createQueryBuilder('u');
		$qb->where('u.roles LIKE :user_roles')
			->setParameter('user_roles', '%ROLE_CUSTOMER%')
			->andWhere('u.enabled = 1')
			->andWhere('u.email = :email')
			->setParameter('email', $email);
		return $qb;
	}

	public function getActiveMemberByIdAndEmail($user_id, $email)
	{
		//QueryBuilder
		$qb = $this->getActiveMemberByEmail($email);
		$qb->andWhere('u.id = :user_id')
			->setParameter('user_id', $user_id);
		return $qb;
	}

	// public function getMemberAllDataHasOrder($arr_query_data=false,$user){
	// 		$qb = $this->getMemberByData($arr_query_data);
	// 		$qb->leftJoin('u.deliveryAddress', 'da')
	// 		    // ->innerJoin('u.Order', 'o')
	// 			->andWhere('da.user = :user')
	// 			// ->andWhere('o.user = :user')
	// 			->andWhere('da.deFaultShipping = :status')
	// 			->andWhere('da.deFaultTax = :status')
	// 			->setParameter('user',$user)
	// 			->setParameter('status',1);
	//
	// 		return $qb;
	// }
	// public function getMemberAllDataHasOrderHistoryById($arr_query_data=false,User $user){
	// 		$qb = $this->getMemberAllDataHasOrderHistory($arr_query_data,$user);
	// 		$qb->leftJoin('u.Order', 'o')
	// 			->setParameter('user',$user);
	//
	//
	// 		return $qb;
	// }



}
