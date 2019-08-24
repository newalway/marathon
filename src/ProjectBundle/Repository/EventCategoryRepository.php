<?php

namespace ProjectBundle\Repository;

use Symfony\Component\Intl\Locale;

/**
 * EventCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventCategoryRepository extends \Doctrine\ORM\EntityRepository
{
	private $qb;

	public function findAllData($arr_query_data=false, $locale=false)
    {
		$locale = ($locale) ? $locale : Locale::getDefault();
  		$this->qb = $this->createQueryBuilder('ev');

		//join translation
		$this->qb->join('ev.translations', 'evt')
				->select('ev', 'evt')
				->orderBy('ev.position', 'ASC')
				->addOrderBy('ev.id', 'DESC');

		$q = $arr_query_data['q'];
  		if($q){
			//search from translation
  			$this->qb->where($this->qb->expr()->orX(
	  	      	$this->qb->expr()->like('evt.title', ':query')
	  			// $this->qb->expr()->like('evt.description', ':query')
			))
  			->setParameter('query', '%'.$q.'%');
  		}

  		return $this->qb;
    }

	public function setPublic()
    {
        $this->qb->andWhere('ev.status = 1');
    }

	public function findAllActiveData($arr_query_data=false, $locale=false)
    {
		$this->findAllData($arr_query_data, $locale);
		$this->setPublic();
		return $this->qb;
	}

	public function findActiveDataByCategory($arr_query_data=false, $locale=false, $catObj)
	{
		$this->findAllActiveData($arr_query_data, $locale);

		$this->qb->andWhere($this->qb->expr()->andX(
			$this->qb->expr()->eq('ev.eventCategory', ':catObj')
		))
		->setParameter('catObj',$catObj)
		->andWhere('evt.status = 1');

		// $this->qb->where($this->qb->expr()->andX(
		// 		$this->qb->expr()->eq('ec.id', ':catObj')
		// ))

		return $this->qb;
	}

}
