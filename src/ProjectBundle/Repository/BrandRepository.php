<?php

namespace ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Intl\Locale;

class BrandRepository extends EntityRepository
{
	public function findAllData($arr_query_data=false)
    {
		/*
        //QueryBuilder Expr
  			$qb = $this->createQueryBuilder('b');
			$qb->select('b', 't')
				->leftjoin('b.brandType', 't')
  				->orderBy('b.position', 'ASC')
				->addOrderBy('b.createdAt', 'DESC');
        	$q = $arr_query_data['q'];
  			if($q){
  				$qb->where($qb->expr()->orX(
  	      			$qb->expr()->like('b.title', ':query'),
  					$qb->expr()->like('b.description', ':query'),
					$qb->expr()->like('t.title', ':query')
  	      		))
  				->setParameter('query', '%'.$q.'%');
  			}
		*/

		$qb = $this->createQueryBuilder('b');
		//join translation
		$qb->join('b.translations', 'bt')
				->select('b', 'bt')
				->orderBy('b.position', 'ASC')
				->addOrderBy('b.createdAt', 'DESC');

		$q = $arr_query_data['q'];
  		if($q){
			//search from translation
  			$qb->where($qb->expr()->orX(
	  	      	$qb->expr()->like('bt.title', ':query'),
	  			$qb->expr()->like('bt.description', ':query')
			))
  			->setParameter('query', '%'.$q.'%');
  		}

  		return $qb;
    }

	/*
	public function findAllData()
	{
		return $this->createQueryBuilder('b')
				->orderBy('b.position', 'ASC')
				->addOrderBy('b.createdAt', 'DESC');
	}
	*/

	public function findAllActiveByProductWithBrandType()
    {
		// SELECT brand_type.*
		// FROM brand
		// LEFT JOIN product ON product.brand_id = brand.id
		// LEFT JOIN brand_type ON brand.brand_type_id = brand_type.id
		// where product.id and product.brand_id and product.status = 1
		// group by brand.brand_type_id

		/*
		$sql = 'SELECT t.id, t.title, t.description, t.image
						FROM ProjectBundle:Brand b
		        JOIN b.brandType t
						JOIN b.products p
						WHERE p.status = 1
						GROUP BY b.brandType
						ORDER BY t.position asc, t.createdAt desc
		       ';
		*/

		$sql = '';
		return $this->getEntityManager()->createQuery($sql);
    }

	public function findAllActiveByProduct($locale=false)
    {
		$locale = ($locale) ? $locale : Locale::getDefault();
		$qb = $this->createQueryBuilder('b');
        $qb ->select('b.id','bt.title')
			->join('b.products', 'p')
			->leftjoin('b.translations', 'bt')
            ->andWhere($qb->expr()->andX(
				$qb->expr()->like('p.status', ':status'),
				$qb->expr()->like('bt.locale', ':locale')
            ))
            ->setParameters(array(
                'status' => 1,
				'locale' => $locale
            ))
			->orderBy('b.position', 'ASC')
            ->addOrderBy('b.createdAt', 'DESC')
			->groupBy('b.id')
		;

        try {
            return $qb->getQuery()->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
	public function findBrandAllActiveByProduct($locale=false)
    {
		$locale = ($locale) ? $locale : Locale::getDefault();
		$qb = $this->createQueryBuilder('b');
        $qb ->select('b','bt')
			->join('b.products', 'p')
			->leftjoin('b.translations', 'bt')
            ->andWhere($qb->expr()->andX(
				$qb->expr()->like('p.status', ':status'),
				$qb->expr()->like('bt.locale', ':locale')
            ))
            ->setParameters(array(
                'status' => 1,
				'locale' => $locale
            ))
			->orderBy('b.position', 'ASC')
            ->addOrderBy('b.createdAt', 'DESC')
			->groupBy('b.id')
		;

    	return $qb;
    }
}
