<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Brand;
use ProjectBundle\Entity\Equipment;
use ProjectBundle\Entity\AgeGroup;
use ProjectBundle\Entity\CustomerGroup;
use ProjectBundle\Entity\Muscle;
use ProjectBundle\Entity\Showroom;
use ProjectBundle\Entity\Authentication;
use ProjectBundle\Entity\ProductCategory;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use ProjectBundle\Form\Type\Product\ProductSearchType;
use ProjectBundle\Form\Type\Product\AddToCartType;

class ProductController extends Controller
{
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

		$locale = $request->getLocale();
		$session = $request->getSession();
		$form = $this->createForm(ProductSearchType::class);
		$repository = $this->getDoctrine()->getRepository(Product::class);
		$formData = $request->query->get($form->getName('product_search'));

		$form->handleRequest($request);
		$data = $form->getData();

		$limitPages = $this->container->getParameter('max_per_page_latest_product');

		$limitPerPage = (isset($data['limitPerPage'])) ? $data['limitPerPage'] : $limitPages;

		$data['product_category_id'] = $product_util->getChildrenProductCategoryByCategoryId($formData);
		$query = $repository->findAllActiveData($data, $locale);
		$paginated = $util->setPaginatedOnPagerfanta($query,$limitPerPage);

		//dump($form->submit($request->request->get('age_groups')));

		// $brands = $this->getDoctrine()->getRepository(Brand::class)->findAllActiveByProduct($locale);
		// $equipments = $this->getDoctrine()->getRepository(Equipment::class)->findAllActiveByProduct($locale);
		// $age_groups = $this->getDoctrine()->getRepository(AgeGroup::class)->findAllActiveByProduct($locale);

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_product').':index.html.twig', array(
			'paginated'=>$paginated,
			'form' =>$form->createView(),
			// 'brands'=>$brands,
			// 'equipments'=>$equipments,
			// 'age_groups'=>$age_groups,
		));
	}

	public function detailAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$session = $request->getSession();
		$em = $this->getDoctrine();
		$product = $em->getRepository(Product::class)->getActiveDataById($request->get('id'), $request->getLocale())->getQuery()->getSingleResult();

		if (!$product) { throw $this->createNotFoundException('No data found'); }
		$obj_product = $product[0];

		//get variant data
		$arr_variant_data = $product_util->setArrProductVariantsData($obj_product);
		$arr_sku_variant_data = $arr_variant_data['arr_sku_variant_data'];
		$arr_variant_option_data = $arr_variant_data['arr_variant_option_data'];
		$is_variant = $product_util->isVariantsFromArrVariantData($arr_sku_variant_data['variant_data']);

		$customer_groups = $em->getRepository(CustomerGroup::class)->getCustomerGroupByProduct($obj_product, $request->getLocale());
		$age_groups = $em->getRepository(AgeGroup::class)->getAgeGroupByProduct($obj_product, $request->getLocale());
		$muscles = $em->getRepository(Muscle::class)->getMuscleByProduct($obj_product, $request->getLocale());

		$showrooms = $em->getRepository(Showroom::class)
						->getShowroomByProduct($obj_product, $request->getLocale())
						->setMaxResults(3)
						// ->getResult();
						->getArrayResult();

		$arr_tags = array();
		$hash_tags = $obj_product->getHashtags();
		foreach ($hash_tags as $key => $hash_tag) {
			$arr_tags[$key] = $hash_tag->getTitle();
		}

		$products_relateds = $em->getRepository(Product::class)->getActiveDataByProductsRelated($request->get('id'), $obj_product)->setMaxResults(6)->getQuery()->getResult();

		$form = $this->createForm(AddToCartType::class);

		$geolocation_api_key = $util->getGeolocationApiKey();

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_product').':detail.html.twig', array(
			'form'=>$form->createView(),
			'product'=>$product,
			'arr_sku_variant_data'=>$arr_sku_variant_data,
			'arr_variant_option_data'=>$arr_variant_option_data,
			'is_variant'=>$is_variant,
			'customer_groups'=>$customer_groups,
			'age_groups'=>$age_groups,
			'muscles'=>$muscles,
			'showrooms'=>$showrooms,
			'products_relateds'=>$products_relateds,
			'arr_tags'=>$arr_tags,
			'geolocation_api_key'=>$geolocation_api_key
		));
	}

	public function categoryAction(Request $request)
	{
		$repo = $this->getDoctrine()->getRepository(ProductCategory::class);
		$product_category_root_id = $this->container->getParameter('product_category_root_id');
		$product_categorys = $repo->findHighlightDataByRootId($product_category_root_id, $request->get('id'))->getQuery()->getResult();

		if (!$product_categorys) { throw $this->createNotFoundException('No data found'); }
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_product').':category.html.twig', array(
			'product_categorys'=>$product_categorys
		));
	}

	public function box_searchAction(Request $request)
	{
		$form = $this->createForm(ProductSearchType::class);
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_product').':box_search.html.twig', array(
			'form' =>$form->createView(),
		));
	}

}
