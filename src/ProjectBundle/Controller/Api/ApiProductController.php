<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\Hashtag;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Discount;
use ProjectBundle\Entity\Sku;
use ProjectBundle\Entity\SkuValue;
use ProjectBundle\Entity\Variant;
use ProjectBundle\Entity\VariantOption;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\Showroom;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ProjectBundle\Form\Type\Product\AddToCartType;
use ProjectBundle\Form\Type\Cart\ApplyCouponType;

use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use ProjectBundle\Utils\Products;

class ApiProductController extends FOSRestController
{

	/**
	* List products.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List products",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getPublicFindFastestShowroomDistanceAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$product_id = $request->get('product_id');
		$arr_product = $em->getRepository(Product::class)->getActiveDataById($product_id)->getQuery()->getSingleResult();
		$product = $arr_product[0];

		$lat = $request->get('lat');
		$lng = $request->get('lng');
		$arr_showrooms = array();

		if($product){
			if($lat && $lng){
				$rs = $em->getRepository(Showroom::class)->getFastestDistanceByProductAndLatLng($product_id, $lat, $lng)
						->setMaxResults(3)
						->getQuery()
						->getResult();
				if($rs){
					foreach ($rs as $key => $arr_showroom) {
						$showroom = $arr_showroom[0];
						$data_result['title'] = $showroom->getTitle();
						$data_result['address'] = $showroom->getAddress();
						$data_result['distance'] = $arr_showroom['distance'];
						$data_result['latitude'] = $showroom->getLatitude();
						$data_result['longitude'] = $showroom->getLongitude();
						$data_result['placeId'] = $showroom->getPlaceId();
						$data_result['phone'] = $showroom->getPhone();
						$data_result['fax'] = $showroom->getFax();
						$data_result['mobile'] = $showroom->getMobile();
						array_push($arr_showrooms, $data_result);
					}
				}
			}else{
				//no lat, lng
				$arr_showrooms = $em->getRepository(Showroom::class)->getShowroomByProduct($product)
						// ->setMaxResults(10)
						->getArrayResult();
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
			'arr_showrooms' => $arr_showrooms,
			'product_id' => $product_id,
			'user_lat' => $lat,
			'user_lng' => $lng,
			'time' => date('Y/m/d H:i:s')
	    ));
		return $json_response;
	}

	/**
	* List products.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List products",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getPublicSearchProductsAction(Request $request)
	{
		$arr_query_data['q'] = ($request->get('text')!="undefined") ? $request->get('text') : '' ;
		$arr_products = array();
		$products = $this->getDoctrine()->getRepository(Product::class)->findAllDataJoined($arr_query_data)
			->getQuery()
			->getResult();

		$imagineCacheManager = $this->get('liip_imagine.cache.manager');

		if($products){
			foreach ($products as $product) {
				$data = $product[0];
				$arr_price = Products::getPriceData($product);

				$image = $data->getImage();
				$resolvedPath = ($image) ? $imagineCacheManager->getBrowserPath($image, 'img_small_thumb') : '' ;

				$data_products['id'] = $data->getId();
				$data_products['title'] = $data->getTitle();
				$data_products['price'] = $arr_price['price'];
				$data_products['compare_at_price'] = $arr_price['compare_at_price'];
				$data_products['selected'] = '';
				$data_products['image'] = $resolvedPath;
				array_push($arr_products, $data_products);
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
			"arr_products" => $arr_products,
	    ));
		return $json_response;
	}

	/**
	* List products discounts.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List products discounts",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getProductsDiscountsAction(Request $request)
	{
		$discount_id = $request->get('discount_id');

		// $discount = $this->getDoctrine()->getRepository(Product::class)->getProductsDiscountsByDiscountId($discount_id)->getQuery()->getArrayResult();

		$arr_products = array();
		$products = $this->getDoctrine()->getRepository(Product::class)->findProductsDiscountsByDiscountIdDataJoined($discount_id)
			->getQuery()
			->getResult();

		$imagineCacheManager = $this->get('liip_imagine.cache.manager');

		if($products){
			foreach ($products as $product) {
				$data = $product[0];
				$arr_price = Products::getPriceData($product);

				$image = $data->getImage();
				$resolvedPath = ($image) ? $imagineCacheManager->getBrowserPath($image, 'img_small_thumb') : '' ;

				$data_products['id'] = $data->getId();
				$data_products['title'] = $data->getTitle();
				$data_products['price'] = $arr_price['price'];
				$data_products['compare_at_price'] = $arr_price['compare_at_price'];
				$data_products['selected'] = true;
				$data_products['image'] = $resolvedPath;
				array_push($arr_products, $data_products);
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
			"arr_products" => $arr_products,
	    ));
		return $json_response;
	}

	/**
	* List products promotions.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List products promotions",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getProductsPromotionsAction(Request $request)
	{
		$promotion_id = $request->get('promotion_id');

		$arr_products = array();
		$products = $this->getDoctrine()->getRepository(Product::class)->findProductsPromotionsByPromotionIdDataJoined($promotion_id)
			->getQuery()
			->getResult();

		$imagineCacheManager = $this->get('liip_imagine.cache.manager');

		if($products){
			foreach ($products as $product) {
				$data = $product[0];
				$arr_price = Products::getPriceData($product);

				$image = $data->getImage();
				$resolvedPath = ($image) ? $imagineCacheManager->getBrowserPath($image, 'img_small_thumb') : '' ;

				$data_products['id'] = $data->getId();
				$data_products['title'] = $data->getTitle();
				$data_products['price'] = $arr_price['price'];
				$data_products['compare_at_price'] = $arr_price['compare_at_price'];
				$data_products['selected'] = true;
				$data_products['image'] = $resolvedPath;
				array_push($arr_products, $data_products);
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
			"arr_products" => $arr_products,
			"promotion_id"=>$promotion_id
	    ));
		return $json_response;
	}

	/**
	* List products variants.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List products variants",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getProductVariantsAction(Request $request)
	{
		$user = $this->getUser(); //not all user_data

		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$product_id = $request->query->get('product_id');

		$arr_variant = array();
		$arr_variant_option = array();
		$arr_view_variant = array();
		$arr_view_variant_option = array();
		$variant_is_inventory = false;

		//get product
		$product = $this->getDoctrine()->getRepository(Product::class)->find($product_id);
		if ($product) {
			$arr_data = $product_util->setArrProductVariantsData($product);
			$arr_variant = $arr_data['arr_sku_variant_data']['variant_data'];
			$arr_view_variant = $arr_data['arr_sku_variant_data']['view_variant_data'];

			$arr_variant_option = $arr_data['arr_variant_option_data']['variant_option_data'];
			$arr_view_variant_option = $arr_data['arr_variant_option_data']['view_variant_option_data'];
			$variant_is_inventory = $arr_data['arr_variant_option_data']['variant_is_inventory'];
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
					"variants" => $arr_variant,
					"variant_options" => $arr_variant_option,
					"view_variants" => $arr_view_variant,
					"view_variant_options" => $arr_view_variant_option,
					"variant_is_inventory" => $variant_is_inventory
	    ));
		return $json_response;
	}


	/**
	* Post public add item to cart.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="Post public add item to cart",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function postPublicAdditemtocartAction(Request $request)
	{
		/*
		$product_util = $this->container->get('app.product');
		$container = $this->container;
		$arr_cart_data = array();
		$status = false;
		$error_msg = '';

		$form = $this->createForm(AddToCartType::class);
		$form->submit($request->request->all());
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $request->request->all();

			//create cart session
			$arr_result = $product_util->createCartSessionData($data);
			$arr_cart_data = $product_util->getArrProductCartData();

			$status = $arr_result['status'];
			if(!$status){
				$error_msg = 'Insufficient stock available, only '.$arr_result['arr_inventory']['inventory_quantity'].' remaining.';
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
		$json_response->setData(array(
			'arr_cart_data' => $arr_cart_data,
			'status' => $status,
			'error_msg' => $error_msg,
			'time' => date('Y/m/d H:i:s')
		));
		return $json_response;
		*/
	}

	/**
	* Get update cart.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="Get update cart",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function getPublicUpdateCartAction(Request $request, $quantity, $product_id)
	{
		/*
		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$sku_id = $request->query->get('sku_id');
		$arr_cart_data = array();
		$status = true;
		$error_msg = '';

		if($quantity){
			//update quantity
			$arr_result = $product_util->updateProductQuantityInCart($quantity, $product_id, $sku_id);

			$status = $arr_result['status'];
			if(!$status){
				$error_msg = 'Insufficient stock available, only '.$arr_result['arr_inventory']['inventory_quantity'].' remaining.';
			}

		}else{
			//remove quantity
			$product_util->removeProductInCart($product_id, $sku_id);
		}

		$arr_cart_data = $product_util->getArrProductCartData();

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
					'arr_cart_data'  => $arr_cart_data,
					'status' => $status,
					'error_msg' => $error_msg,
					'time' => date('Y/m/d H:i:s')
	    ));
		return $json_response;
		*/
	}

	/**
	* Get apply discount code.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="Get apply discount code",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request               $request      the request object
	*
	* @return array
	*/
	public function postPublicApplyDiscountCodeAction(Request $request)
	{
		/*
		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

		$arr_code_data = '';
		$arr_cart_data = array();
		$status = true;
		$error_msg = '';

		$form = $this->createForm(ApplyCouponType::class);
		$form->submit($request->request->all());

		if ($form->isSubmitted() && $form->isValid()) {
			$arr_code_data = $request->request->all();
			$discount_code = $arr_code_data['code'];
			$user_id = $arr_code_data['id'];
			$email = $arr_code_data['email'];

			$arr_cart_data = $product_util->getArrProductCartData();
			$arr_result = $product_util->validateDiscountCode($arr_code_data, $arr_cart_data);

			if($arr_result['status']){
				//discount code available
				//set session discount code
				$product_util->setSessionDiscountCode($discount_code);

				//todo:: get $arr_cart_data;
			}else{
				//error
			}
		}

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
					'arr_result' => $arr_result,
					'arr_code_data' => $arr_code_data,
					'arr_cart_data'  => $arr_cart_data,
					'status' => $status,
					'error_msg' => $error_msg,
					'time' => date('Y/m/d H:i:s')
	    ));
		return $json_response;
		*/
	}

}
