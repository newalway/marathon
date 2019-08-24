<?php

namespace ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Intl\Locale;

class ShowroomRepository extends EntityRepository
{
    public function findAllData($arr_query_data=false)
    {
        $qb = $this->createQueryBuilder('s')
                ->orderBy('s.position', 'ASC')
                ->addOrderBy('s.createdAt', 'DESC');
        $q = $arr_query_data['q'];
        if($q){
            $qb->where($qb->expr()->orX(
                $qb->expr()->like('s.title', ':query'),
                $qb->expr()->like('s.address', ':query')
            ))
            ->setParameter('query', '%'.$q.'%');
        }
        return $qb;
    }

    public function findAllActiveData($locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();

        $qb = $this->createQueryBuilder('s');
        $qb ->leftjoin('s.translations', 'st')
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.createdAt', 'DESC')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->like('s.status', ':status'),
				$qb->expr()->like('st.locale', ':locale')
            ))
            ->setParameters(array(
                'status' => 1,
				'locale' => $locale
            ))
            ->select('s','st')
        ;
      return $qb;
    }

    public function findActiveDataById($id)
    {
        $qb = $this->findAllActiveData();
        $qb->andWhere("s.id = '$id'");
        return $qb;
    }

    public function getFastestDistanceByLatLng($lat, $lng, $locale=false)
	{
        $locale = ($locale) ? $locale : Locale::getDefault();

        $qb = $this->createQueryBuilder('s');
        $qb->select('s', 'st')
            ->leftjoin('s.translations', 'st')
            ->addSelect("
                ( 6371 *
                    ACOS(
                        COS( RADIANS( :lat ) ) *
                        COS( RADIANS( s.latitude ) ) *
                        COS( RADIANS( s.longitude ) - RADIANS( :lng ) ) +
                        SIN( RADIANS( :lat ) ) *
                        SIN( RADIANS( s.latitude) )
                    )
                )
                AS distance "
            )
            ->where("st.locale = '$locale'")
            ->andWhere("s.status = 1")
            ->setParameters(array(
				'lat'=> $lat,
				'lng'=> $lng
        ));
        $qb->orderBy('distance', 'ASC');
        #$qb->having('distance <= 5'); //distant 5 kilometer

        // 6371 = kilometer
        // 3959 = mile

		return $qb;
	}

    public function getFastestDistanceByProductAndLatLng($product_id, $lat, $lng, $locale=false)
	{
        $locale = ($locale) ? $locale : Locale::getDefault();
        $qb = $this->getFastestDistanceByLatLng($lat, $lng);
        $qb->innerJoin('s.products', 'p');
        $qb->andWhere("p.id = '$product_id'");
        return $qb;
    }

    public function getShowroomByProduct($product, $locale=false)
	{
        $locale = ($locale) ? $locale : Locale::getDefault();
		$query = $this->getEntityManager()
			->createQuery(
				'SELECT srt.title,
                    srt.address,
                    sr.latitude,
                    sr.longitude,
                    sr.placeId,
                    sr.phone,
                    sr.fax,
                    sr.mobile
                FROM ProjectBundle:Showroom sr
				JOIN sr.products p
				LEFT JOIN sr.translations srt
				WHERE p.status = :status
					AND p.id = :product_id
					AND srt.locale = :locale'

			)->setParameters(array(
				'status'=> 1,
				'product_id'=> $product->getId(),
				'locale'=> $locale
			));
			// ORDER BY a.position asc, a.createdAt desc'

        return $query;
		//return $query->getResult();
		// return $query->getArrayResult();
	}
}
