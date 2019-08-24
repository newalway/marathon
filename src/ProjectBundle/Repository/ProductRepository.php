<?php

namespace ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Intl\Locale;

class ProductRepository extends EntityRepository
{
    private $qb;

    public function findAllData()
    {
      return $this->createQueryBuilder('p')
                ->orderBy('p.position', 'ASC')
                ->addOrderBy('p.createdAt', 'DESC');
    }

    public function findDataById($id)
    {
        $this->find($id);
        return $this;
    }

    public function selectProductSkuData($locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
  		$this->qb = $this->createQueryBuilder('p');
        // Doctrine\ORM\Query\Expr::WITH

        $this->qb->select('p', 'pt')
            ->leftjoin('p.skus', 'sku', "WITH", 'sku.status = 1')
            ->leftjoin('p.translations', 'pt')
            ->andWhere("pt.locale = '$locale'")
            ->groupBy('p.id');
        $this->setSelectPriceData();
    }

    public function setSelectPriceData()
    {
        $this->qb->addSelect("COUNT(distinct sku.id) as v_count")
        ->addSelect("(SELECT SUM( ss1.inventoryQuantity) as total
                        FROM ProjectBundle\Entity\Sku ss1
                        WHERE ss1.product = p.id
                            AND ss1.inventoryPolicyStatus = 1
                            AND ss1.status = 1)
                     AS v_inventory_quantity ")
        ->addSelect("SUM(distinct sku.defaultOption) as v_is_default_option")
        ->addSelect("(SELECT ss2.price
                        FROM ProjectBundle\Entity\Sku ss2
                        WHERE ss2.product = p.id
                            AND ss2.defaultOption = 1
                            AND ss2.status = 1)
                     AS v_default_price ")
        ->addSelect("(SELECT ss3.compareAtPrice
                        FROM ProjectBundle\Entity\Sku ss3
                        WHERE ss3.product = p.id
                            AND ss3.defaultOption = 1
                            AND ss3.status = 1)
                     AS v_default_compare_at_price ")
        ->addSelect("sku.price as v_price")
        ->addSelect("sku.compareAtPrice as v_compare_at_price");
    }

    public function setOrderBy()
    {
        $this->qb->addOrderBy('p.position', 'ASC')
            ->addOrderBy('p.createdAt', 'DESC');
    }

    public function setPublic()
    {
        $this->qb->andWhere('NOW() >= p.publishDate')
                ->andWhere('p.status = 1');

            // ->andWhere($this->qb->expr()->andX(
            //     $this->qb->expr()->eq('p.status', ':status')
            // ))->setParameter('status', 1);
    }

    public function setUnPublic()
    {
        $this->qb->andWhere($this->qb->expr()->orX(
            $this->qb->expr()->eq('p.status', 0),
            $this->qb->expr()->lt('NOW()', 'p.publishDate')
        ));
    }

    public function findProductsDiscountsByDiscountIdDataJoined($discount_id, $locale=false)
    {
        $this->selectProductSkuData($locale);
        $this->setOrderBy();
        $this->qb->innerjoin('p.discounts', 'd')
            ->andWhere('d.id = :discount_id')
            ->setParameter('discount_id', $discount_id);
        return $this->qb;
    }

    public function findProductsPromotionsByPromotionIdDataJoined($promotion_id, $locale=false)
    {
        $this->selectProductSkuData($locale);
        $this->setOrderBy();
        $this->qb->innerjoin('p.promotions', 'd')
            ->andWhere('d.id = :promotion_id')
            ->setParameter('promotion_id', $promotion_id);
        return $this->qb;
    }

    public function findAllDataJoined($arr_query_data=false, $locale=false)
    {
        $this->selectProductSkuData($locale);
        //default order
        $this->setOrderBy();

        // $this->qb->addSelect('pc', 'pct');
        // $this->qb
        //     ->leftjoin('p.productCategories', 'pc')
        //     ->leftjoin('pc.translations', 'pct')
        //     ;

        // ->leftjoin('p.equipment', 'e')
        // ->leftjoin('e.translations', 'et')
        //->leftjoin('p.brand', 'b')
        //->leftjoin('b.translations', 'bt')
        //->leftjoin('p.power', 't')
        //->leftjoin('t.translations', 'tt');

  		if(isset($arr_query_data['q']) && $arr_query_data['q']){
            $q = $arr_query_data['q'];
            $this->qb->where($this->qb->expr()->orX(
      	      	$this->qb->expr()->like('pt.title', ':query')
                // $this->qb->expr()->like('et.title', ':query')
                // $this->qb->expr()->like('pt.description', ':query'),
                // $this->qb->expr()->like('bt.title', ':query'),
                // $this->qb->expr()->like('tt.title', ':query')
            ))
  			->setParameter('query', '%'.$q.'%');
  		}

        if(isset($arr_query_data['search_status']) && $arr_query_data['search_status']){
            $search_status = $arr_query_data['search_status'];

            if($search_status=='available'){
                $this->setPublic();
            }elseif($search_status=='unavailable'){
                $this->setUnPublic();
            }elseif($search_status=='bestseller'){
                $this->qb->andWhere("p.isBestSeller = 1");
                $this->setOrderByBestSeller();
            }elseif($search_status=='new_product'){
                $this->qb->andWhere("p.isNew = 1");
            }
        }

  		return $this->qb;
    }

    public function findAllActiveData($arr_query_data=false, $locale=false)
    {
        $this->selectProductSkuData($locale);
        $this->setPublic();

        if($arr_query_data){

            if(isset($arr_query_data['searchBox']) && $arr_query_data['searchBox']){
                $arr_searchBox = ($arr_query_data['searchBox']);
                $this->qb->leftjoin('p.equipment', 'e')
                    ->leftjoin('e.translations', 'et');
                $this->qb->andWhere($this->qb->expr()->orX(
                    $this->qb->expr()->like('pt.title', ':query'),
                    $this->qb->expr()->like('pt.description',':query')
                    // $this->qb->expr()->like('et.title',':query' ),
                ))
                ->setParameter('query', '%'.$arr_searchBox.'%');
            }

            if(isset($arr_query_data['product_category_id']) && $arr_query_data['product_category_id']){
                $this->qb->leftjoin('p.productCategories', 'pc');
                    // ->leftjoin('pc.translations', 'pct');
                $this->qb->andWhere($this->qb->expr()->orX(
                    $this->qb->expr()->in('pc.id',':pc_id')
                ))
                ->setParameter('pc_id', $arr_query_data['product_category_id']);
            }

            if(isset($arr_query_data['brands']) && $arr_query_data['brands']){
                $conditions = array();
                $this->qb->leftJoin('p.brand','b');
                $arr_brands = $arr_query_data['brands'];
                $is_match = false;
                foreach ($arr_brands as $brands) {
                    $brandsId = $brands->getId();
                    $conditions[] = $this->qb->expr()->eq('b.id', $brandsId);
                    $is_match = true;
                }
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }

            if(isset($arr_query_data['age_groups']) && $arr_query_data['age_groups']){
                 $conditions = array();
                 $arr_age_groups = $arr_query_data['age_groups'];
                 $is_match = false;
                 foreach ($arr_age_groups as $age_groups) {
                     $age_groupsId = $age_groups->getId();
                     $conditions[] = $this->qb->expr()->eq('ag.id', $age_groupsId);
                     $is_match = true;
                 }
                 $this->qb->leftJoin('p.ageGroups','ag');
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }

            if(isset($arr_query_data['customerGroups']) && $arr_query_data['customerGroups']){
                $conditions = array();
                $arr_customer_groups = $arr_query_data['customerGroups'];
                $is_match = false;
                foreach ($arr_customer_groups as $customer_group) {
                    $customer_groups_id = $customer_group->getId();
                    $conditions[] = $this->qb->expr()->eq('cusg.id', $customer_groups_id);
                    $is_match = true;
                }
                $this->qb->leftJoin('p.customerGroups','cusg');
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }

            if(isset($arr_query_data['power']) && $arr_query_data['power']){
                 $conditions = array();
                 $arr_power = $arr_query_data['power'];
                  $is_match = false;
                 foreach ($arr_power as $power) {
                     $powerId = $power->getId();
                     $conditions[] = $this->qb->expr()->eq('pw.id', $powerId);
                     $is_match = true;
                 }
                 $this->qb->leftJoin('p.power','pw');
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }

            if(isset($arr_query_data['muscles']) && $arr_query_data['muscles']){
                 $conditions = array();
                 $arr_muscles = $arr_query_data['muscles'];
                 $is_match = false;
                 foreach ($arr_muscles as $muscles) {
                     $musclesId = $muscles->getId();
                     $conditions[] = $this->qb->expr()->eq('mc.id', $musclesId);
                     $is_match = true;
                 }
                 $this->qb->leftJoin('p.muscles','mc');
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }

            if(isset($arr_query_data['ddlPriceSort']) && $arr_query_data['ddlPriceSort']){
                $this->qb->orderBy('p.price', $arr_query_data['ddlPriceSort']);
            }else{
                $this->qb->orderBy('p.position', 'ASC')->addOrderBy('p.createdAt', 'DESC');
            }

            /*
            if(isset($arr_query_data['equipment']) && $arr_query_data['equipment']){
                 $conditions = array();
                 $arr_equipment = $arr_query_data['equipment'];
                  $is_match = false;
                 foreach ($arr_equipment as $equipment) {
                     $equipmentId = $equipment->getId();
                     $conditions[] = $this->qb->expr()->eq('eq.id', $equipmentId);
                     $is_match = true;
                 }
                 $this->qb->leftJoin('p.equipment','eq');
                if($is_match){
                    $orX = $this->qb->expr()->orX();
                    $orX->addMultiple($conditions);
                    $this->qb->andWhere($orX);
                }
            }
            */

            // if(isset($arr_query_data['productCategories']) && $arr_query_data['productCategories']){
            //     $productCategories = $arr_query_data['productCategories'];
            //     foreach ($productCategories as $product_category) {
            //         $category_id = $product_category->getId();
            //         $product_category->getTitle();
            //     }
            // }

        }else{
            $this->setOrderBy();
        }

        return $this->qb;
    }

    public function getActiveDataById($id, $locale=false)
    {
        $this->findAllActiveData(array(), $locale);

        $this->qb->addSelect('ba', 'po', 'eq');
        $this->qb->leftjoin('p.brand', 'ba')
            ->leftjoin('p.power', 'po')
            ->leftjoin('p.equipment', 'eq');

        $this->qb->andWhere($this->qb->expr()->andX(
            $this->qb->expr()->eq('p.id', ':id')
        ))
        ->setParameter('id', $id);
        return $this->qb;
    }

    public function getActiveData($id, $locale=false)
    {
        $this->findAllActiveData(array(), $locale);
        $this->qb->andWhere($this->qb->expr()->andX(
            $this->qb->expr()->eq('p.id', ':id')
        ))
        ->setParameter('id', $id);
        return $this->qb;
    }

    public function getActiveDataByProductsRelated($id,$product=false,$locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $this->qb = $this->createQueryBuilder('p');
        $this->qb->select('p', 'pt')
            ->leftjoin('p.translations', 'pt')
            ->leftjoin('p.skus', 'sku', "WITH", 'sku.status = 1');

        $this->qb->andWhere($this->qb->expr()->andX(
            $this->qb->expr()->eq('pt.locale', ':locale'),
            $this->qb->expr()->notLike('p.id', ':id')
        ))
        ->setParameter('locale', $locale)
        ->setParameter('id', $id);

        $this->setSelectPriceData();
        $this->setPublic();

        if($product->getEquipment()){
            $equipment_id = $product->getEquipment()->getId();
            $this->qb->leftjoin('p.equipment', 'eq');
            $this->qb->andWhere($this->qb->expr()->andX(
                $this->qb->expr()->eq('eq.id',':equipment_id')
            ))->setParameter('equipment_id', $equipment_id);
        }
        $this->qb->groupBy('p.id');
        return $this->qb;
    }

    public function getActiveDataProductsByPromotionId($id,$locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $this->selectProductSkuData($locale);
        $this->qb->addSelect('pm')
            ->leftjoin('p.promotions', 'pm');
        $this->qb->andWhere($this->qb->expr()->andX(
            $this->qb->expr()->eq('pm.id', ':id'),
            $this->qb->expr()->eq('pm.status', ':status')
        ))
        ->setParameter('id', $id)
        ->setParameter('status', 1);
        $this->setPublic();
        return $this->qb;
    }

    public function getPublishProductsDiscountByDiscountCode($discount_code)
	{
		//QueryBuilder Expr
		$this->qb = $this->createQueryBuilder('p');
		$this->qb->select('p')
			->where('pd.discountCode = :discount_code')
			->setParameter('discount_code', $discount_code)
			->innerJoin('p.discounts', 'pd');
        $this->setPublic();
		return $this->qb;
	}





    public function setOrderByBestSeller()
    {
        $this->qb->orderBy('p.bestSellerPosition', 'ASC')
            ->addOrderBy('p.position', 'ASC')
            ->addOrderBy('p.createdAt', 'DESC');
    }

    public function findProductBestSeller($locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $this->qb = $this->createQueryBuilder('p');
        $this->qb->select('p, pt')
            ->leftjoin('p.translations', 'pt')
            ->andWhere("p.isBestSeller = 1")
            ->andWhere("pt.locale = '$locale'");
        $this->setOrderByBestSeller();

        return $this->qb;
    }

    public function findPublishProductBestSeller($locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $this->qb = $this->createQueryBuilder('p');
        $this->qb->select('p, pt');
        $this->qb->addSelect('COUNT(coi) as itemCount')
            ->leftjoin('p.translations', 'pt')
            ->leftjoin('p.customerOrderItems', 'coi')
            ->leftjoin('coi.sku', 'sku', "WITH", 'sku.status = 1')
            ->andWhere("p.isBestSeller = 1")
            ->andWhere("pt.locale = '$locale'")
            ->groupBy('p.id');
        $this->setOrderByBestSeller();

        $this->setSelectPriceData();
        $this->setPublic();

        return $this->qb;
    }

    public function findPublishProductBestSellerByOrderItems($locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $this->qb = $this->createQueryBuilder('p');
        $this->qb->select('p, pt');
        $this->qb->addSelect('COUNT(coi) as itemCount')
            ->leftjoin('p.translations', 'pt')
            ->leftjoin('p.customerOrderItems', 'coi')
            ->leftjoin('coi.sku', 'sku', "WITH", 'sku.status = 1')
            ->andWhere("pt.locale = '$locale'")
            ->groupBy('p.id')
            ->orderBy('itemCount', 'DESC');

        $this->setSelectPriceData();
        $this->setPublic();

        return $this->qb;
    }


    /*
    public function getProductsDiscountsByDiscountId($discount_id, $locale=false)
    {
        $locale = ($locale) ? $locale : Locale::getDefault();
        $qb = $this->createQueryBuilder('p')
            ->select('p.id, pt.title')
            ->innerjoin('p.discounts', 'd')
            ->leftjoin('p.translations', 'pt')
            ->where('d.id = :discount_id')
            ->andWhere("pt.locale = '$locale'")
            ->setParameter('discount_id', $discount_id)
            ;
        return $qb;
    }
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
                ->createQuery('SELECT p FROM ProjectBundle:Product p ORDER BY p.name ASC')
                ->getResult();
    }
    public function findOneByIdJoinedToEquipment($productId)
    {
        $query = $this->getEntityManager()
                    ->createQuery(
                        'SELECT p, c FROM ProjectBundle:Product p
                        JOIN p.equipment c
                        WHERE p.id = :id'
                    )->setParameter('id', $productId);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    public function findAllActiveWithBrandId($brandId)
    {
        $query = $this->getEntityManager()
                    ->createQuery(
                        'SELECT p FROM ProjectBundle:Product p
                        WHERE p.status = :status
                        AND p.brand = :brandId
                        ORDER BY p.position asc, p.createdAt desc'
                    )->setParameter('status', 1)
                    ->setParameter('brandId', $brandId);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    public function findAllActiveWithSalons()
    {
        $query = $this->getEntityManager()->createQuery(
                    'SELECT p FROM ProjectBundle:Product p
                    JOIN p.salons s
                    WHERE p.status = :pstatus
                    AND s.status = :lstatus
                    ORDER BY p.position asc, p.createdAt desc'
                )->setParameter('pstatus', 1)
                ->setParameter('lstatus', 1);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    */
}
