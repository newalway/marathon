<?php

namespace ProjectBundle\Utils;

use ProjectBundle\Entity\Authentication;
use ProjectBundle\Entity\AccessToken;
use ProjectBundle\Entity\RefreshToken;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Sku;
use ProjectBundle\Entity\SkuValue;
use ProjectBundle\Entity\Hashtag;
use ProjectBundle\Entity\Variant;
use ProjectBundle\Entity\VariantOption;
use ProjectBundle\Entity\ProductImage;
use ProjectBundle\Entity\ShippingRate;
use ProjectBundle\Entity\Discount;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\DeliveryAddress;
use ProjectBundle\Entity\PaymentGateway;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\DiscountSetting;
use ProjectBundle\Entity\CustomerOrderItem;
use ProjectBundle\Entity\CustomerOrderDelivery;
use ProjectBundle\Entity\CustomerPaymentEpayment;
use ProjectBundle\Entity\DeliveryMethod;
use ProjectBundle\Entity\Holiday;
use ProjectBundle\Entity\Showroom;
use ProjectBundle\Entity\ProductCategory;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Exception;
use GuzzleHttp\Client;
use phpbrowscap\Browscap;
use GeoIp2\Database\Reader;

class Products
{
	private $kernel;
	private $factory;
	private $mailer;
	private $router;
	protected $authorizationChecker;

	public function __construct($kernel, $factory, \Swift_Mailer $mailer, Router $router, AuthorizationCheckerInterface $authorizationChecker)
	{
		$this->container = $kernel->getContainer();
		$this->factory = $factory;
		$this->mailer = $mailer;
		$this->router = $router;
		$this->authorizationChecker = $authorizationChecker;
	}

	public function updateProductVariants(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$update_variant_sku_status = $request->request->get('update_variant_sku_status'); //for check update all sku

		// check update sku or product no variant
		if(!empty($update_variant_sku_status) && $update_variant_sku_status!=='false'){

			//get data
			$update_variant_sku = $request->request->get('update_variant_sku');

			$sku_ids = $update_variant_sku['id'];
			if(!empty($sku_ids)){

				$sku_status = $update_variant_sku['status'];

				$sku_inventory_quantity = array();
				if (array_key_exists('inventory_quantity', $update_variant_sku)) {
					$sku_inventory_quantity = $update_variant_sku['inventory_quantity'];
				}

				$sku_price = $update_variant_sku['price'];
				$sku_compare_at_price = $update_variant_sku['compare_at_price'];
				$sku_sku = $update_variant_sku['sku'];
				$sku_images = $update_variant_sku['image'];
				$update_status = $update_variant_sku['update_status']; //for check update this sku_id

				foreach ($sku_ids as $key => $sku_id) {

					// check update status this sku_id
					if($update_status[$key]=='true') {
						$sku = $em->getRepository(Sku::class)->findOneById($sku_id);
						if($sku){

							$status_data = ($sku_status[$key]=='on')?1:0;
							$sku->setStatus($status_data);

							if (array_key_exists($key, $sku_inventory_quantity)) {
								if(empty($sku_inventory_quantity[$key]) && $sku_inventory_quantity[$key]!=='0'){
									$sku->setInventoryQuantity(null);
									$sku->setInventoryPolicyStatus(0);
								}else{
									$sku->setInventoryQuantity($sku_inventory_quantity[$key]);
									$sku->setInventoryPolicyStatus(1);
								}
							}

							$price_data = ($sku_price[$key]) ? $sku_price[$key] : null ;
							$sku->setPrice($price_data);

							$compare_at_price_data = ($sku_compare_at_price[$key]) ? $sku_compare_at_price[$key] : null ;
							$sku->setCompareAtPrice($compare_at_price_data);

							$sku_data = ($sku_sku[$key]) ? $sku_sku[$key] : null ;
							$sku->setSku($sku_data);

							if(!empty($sku_images[$key])){
								//image
								$image_data = $sku_images[$key];
								$sku->setImage($image_data);
								//image filter
								$arr_img = $this->getPublicProductCacheImage($image_data);
								if($arr_img['s']){$sku->setImageSmall($arr_img['s']);}
								if($arr_img['m']){$sku->setImageMedium($arr_img['m']);}
								if($arr_img['l']){$sku->setImageLarge($arr_img['l']);}
							}

							$em->persist($sku);
							$em->flush();
						}
					}else{
						// not update this sku_id
					}
				}
			}
		}else{
			// not update all sku data
		}
	}

	public function saveProductVariants(Product $product, $frm_product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();

		$is_variant_enable = $request->request->get('is_variant_enable');
		if($is_variant_enable=='true'){
			//get variant data
			$variant_sku = $request->request->get('variant_sku');
			$variant_sku_option = $variant_sku['option'];
			// $variant_sku_option = $request->request->get('variant_sku_option');
			if(!empty($variant_sku_option)){
				//create variant and variant_option data
				$master_data_variant = $this->updateVariantAndVariantOption($product);
				//create sku and sku_option
				$this->updateSkuValue($product, $master_data_variant);
			}else{
				//remove all variants
				$this->removeVariant($product);
				$this->removeSku($product);
			}
		}else{
			// disable variants
		}
	}

	public function updateVariantAndVariantOption(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$variant_name = $request->request->get('variant_name');
		$variant_value = $request->request->get('variant_value');

		$master_data_variant = array();
		$master_data_variant_option = array();

		if(!empty($variant_value)){
			//remove current Variant and VariantOption
			$this->removeVariant($product);

			foreach ($variant_value as $option_name => $option_values) {
				$option_values = json_decode($option_values);
				if(!empty($option_values)){
					// create new variant
					$variant = new Variant();
					$variant->setProduct($product);
					$variant->setName($option_name);
					$em->persist($variant);
					$em->flush();
					$v_id = $variant->getId();
					$v_name = $variant->getName();
					$master_data_variant[$v_name] = $v_id;

					/*
					//get and set variant
					$variant = $em->getRepository(Variant::class)->findNameByProduct($product, $option_name);
					if(count($variant)){
						// update variant
						$variant->setName($option_name);//reset name
						$em->flush();
						$v_id = $variant->getId();
						$v_name = $variant->getName();
						$master_data_variant[$v_name] = $v_id;
					}else{
						// create new variant
						$variant = new Variant();
						$variant->setProduct($product);
						$variant->setName($option_name);
						$em->persist($variant);
						$em->flush();
						$v_id = $variant->getId();
						$v_name = $variant->getName();
						$master_data_variant[$v_name] = $v_id;
					}*/

					foreach ($option_values as $option_value) {
						// create new variant_option
						$p_id = $product->getId();
						$variant_option = new VariantOption();
						$variant_option->setVariant($variant);
						$variant_option->setName($option_value);
						$variant_option->setProductId($p_id);
						$em->persist($variant_option);
						$em->flush();
						$vo_id = $variant_option->getId();
						$vo_name = $variant_option->getName();
						$master_data_variant_option[$v_name][$vo_name] = $vo_id;

						/*
						//get and set variant_option
						$variant_option = $em->getRepository(VariantOption::class)->findNameByVariant($variant, $option_value);
						if(count($variant_option)){
							// update variant_option
							$variant_option->setName($option_value);//reset name
							$em->flush();
							$vo_id = $variant_option->getId();
							$vo_name = $variant_option->getName();
							$master_data_variant_option[$v_name][$vo_name] = $vo_id;
						}else{
							// create new variant_option
							$p_id = $product->getId();
							$variant_option = new VariantOption();
							$variant_option->setVariant($variant);
							$variant_option->setName($option_value);
							$variant_option->setProductId($p_id);
							$em->persist($variant_option);
							$em->flush();
							$vo_id = $variant_option->getId();
							$vo_name = $variant_option->getName();
							$master_data_variant_option[$v_name][$vo_name] = $vo_id;
						}*/

					}
				}
			}
		}//end variant and variant_option

		return array(
			'variant'=>$master_data_variant,
			'variant_option'=>$master_data_variant_option);
	}

	public function removeVariant(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		//remove current Variant and VariantOption
		$remove_variants = $em->getRepository(Variant::class)->findByProduct($product);
		if($remove_variants){
			foreach ($remove_variants as $remove_variant) {
				$em->remove($remove_variant);
			}
			$em->flush();
		}
	}

	public function updateSkuValue(Product $product, $master_data_variant)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();

		$variant_name = $request->request->get('variant_name');
		$variant_value = $request->request->get('variant_value');

		$variant_sku = $request->request->get('variant_sku');
		$sku_id = $variant_sku['id'];
		$status = $variant_sku['status'];
		$variant_sku_option = $variant_sku['option'];
		$inventory_quantity = (isset($variant_sku['inventory_quantity'])) ? $variant_sku['inventory_quantity'] : false ;
		$price = $variant_sku['price'];
		$compare_at_price = $variant_sku['compare_at_price'];
		$skus = $variant_sku['sku'];
		$image = $variant_sku['image'];
		$default_option = $variant_sku['default_option'];

		$product_id = $product->getId();
		$p_weight = $product->getWeight();
		$p_weight_unit = $product->getWeightUnit();

		$md_variant = $master_data_variant['variant'];
		$md_variant_option = $master_data_variant['variant_option'];

		if(!empty($variant_sku_option)){

			//remove current Sku
			$this->removeSku($product);

			foreach ($variant_sku_option as $key => $sku_option){
				//save sku
				$sku_status = ($status[$key]=='on')?1:0;
				$sku = new Sku();
				$sku->setProduct($product);
				$sku->setPrice($price[$key]);
				$sku->setStatus($sku_status);

				//sku
				if(!empty($skus[$key])) {
					$sku->setSku($skus[$key]);
				}

				// compare_at_price
				if(!empty($compare_at_price[$key])) {
					$sku->setCompareAtPrice($compare_at_price[$key]);
				}

				//inventory quantity
				if($inventory_quantity) {
					if (array_key_exists($key, $inventory_quantity)) {
						if(!empty($inventory_quantity[$key])){
							$sku->setInventoryPolicyStatus(1);
							$sku->setInventoryQuantity($inventory_quantity[$key]);
						}else{
							$sku->setInventoryPolicyStatus(0);
						}
					}
				}

				//weight
				if($p_weight && $p_weight_unit) {
					$sku->setWeight($p_weight);
					$sku->setWeightUnit($p_weight_unit);
					$weight_grams = $this->getWeightGrams($p_weight, $p_weight_unit);
					$sku->setGrams($weight_grams);
				}

				//image
				if(!empty($image[$key])) {
					//image
					$sku_image = $image[$key];
					$sku->setImage($sku_image);
					//image filter
					$arr_img = $this->getPublicProductCacheImage($sku_image);
					if($arr_img['s']){$sku->setImageSmall($arr_img['s']);}
					if($arr_img['m']){$sku->setImageMedium($arr_img['m']);}
					if($arr_img['l']){$sku->setImageLarge($arr_img['l']);}
				}

				// default variant
				if($default_option[$key]=='true'){
					$sku->setDefaultOption(1);
				}else{
					$sku->setDefaultOption(0);
				}

				$em->persist($sku);
				$em->flush();

				//save sku_value
				$arr_option = json_decode($sku_option);
				if(!empty($arr_option)){
					foreach ($arr_option as $v_key => $option_value) {
						$option_name = $variant_name[$v_key];
						$variant_id = $md_variant[$option_name];
						$variant_option_id = $md_variant_option[$option_name][$option_value];
						//get variant and variant_option
						$variant = $em->getRepository(Variant::class)->findOneById($variant_id);
						$variant_option = $em->getRepository(VariantOption::class)->findOneById($variant_option_id);

						$sku_value = new SkuValue();
						$sku_value->setVariant($variant);
						$sku_value->setVariantOption($variant_option);
						$sku_value->setSku($sku);
						$sku_value->setProductId($product_id);
						$em->persist($sku_value);
						$em->flush();
					}
				}
			}
		}
	}

	public function removeSku(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		//remove current Sku
		$remove_skus = $em->getRepository(Sku::class)->findByProduct($product);
		if($remove_skus){
			foreach ($remove_skus as $remove_sku) {
				$em->remove($remove_sku);
			}
			$em->flush();
		}
	}

	//set product inventory with button "add" and "set"
	public function saveProductInventoryAdjustment(Product $product, $frm_product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$inventoryPolicyStatus = $frm_product['inventoryPolicyStatus'];
		if($inventoryPolicyStatus=='0'){
			// //clear inventory
			// $product->setInventoryQuantity(null);
			// $em->flush();
		}elseif($inventoryPolicyStatus=='1'){
			if($frm_product['inventory_quantity_adjustment']===''){
			}else{
				$inventory_quantity = intval($frm_product['inventory_quantity']);
				$inventory_quantity_adjustment = intval($frm_product['inventory_quantity_adjustment']);
				$inventory_quantity_adjustment_type = $frm_product['inventory_quantity_adjustment_type'];

				if($inventory_quantity_adjustment_type=='set'){
					if($inventory_quantity_adjustment>=0 && $inventory_quantity>=0){
						$product->setInventoryQuantity($inventory_quantity);
						$em->flush();
					}
				}elseif($inventory_quantity_adjustment_type=='add'){
					if( $inventory_quantity_adjustment && $inventory_quantity){
						$product->setInventoryQuantity($inventory_quantity);
						$em->flush();
					}
				}
			}
		}
	}

	public function saveProductHashtags(Product $product, $frm_product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$frm_hash_tags = $frm_product['tags'];
		$arr_hash_tags = json_decode($frm_hash_tags);

		$arr_curr_hashtags = array();
		$current_hashtags = $product->getHashtags();
		if($current_hashtags){
			//check removed hashtags
			foreach ($current_hashtags as $current_hashtag) {
				$current_tag = $current_hashtag->getTitle();
				if(!in_array($current_tag, $arr_hash_tags)){
					//remove products_hashtags
					$product->removeHashtags($current_hashtag);
					$em->flush();
				}
			}//endfor
		}

		if(!empty($arr_hash_tags)){
			//add products_hashtags
			foreach ($arr_hash_tags as $tag) {
				$this->addOrUpdateProductHashtag($product, $tag);
			}
		}
	}

	public function addOrUpdateProductHashtag(Product $product, $tag)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$hashtag = $em->getRepository(Hashtag::class)->findOneByTitle($tag);
		if($hashtag){
			//set products_hashtags
			$product->addHashtags($hashtag);
			$em->flush();
		}else{
			//create new hashtag and set products_hashtags
			$hashtag = new Hashtag();
	    	$hashtag->setTitle($tag);
			$em->persist($hashtag);
	    	$em->flush();

			$product->addHashtags($hashtag);
			$em->flush();
		}
	}

	public function saveProductWeightGrams(Product $product, $frm_product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$frm_weight = $frm_product['weight'];
		$frm_weight_unit = $frm_product['weightUnit'];
		$weight_grams = $this->getWeightGrams($frm_weight, $frm_weight_unit);
		$product->setGrams($weight_grams);
		$em->flush();
	}

	public function saveSkuWeightGrams(Sku $sku, $frm_sku)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$frm_weight = $frm_sku['weight'];
		$frm_weight_unit = $frm_sku['weightUnit'];
		$weight_grams = $this->getWeightGrams($frm_weight, $frm_weight_unit);
		$sku->setGrams($weight_grams);
		$em->flush();
	}

	public function getWeightGrams($weight, $weight_unit)
	{
		$weight = intval($weight);
		$weight_grams = null;
		if($weight && $weight_unit) {
			switch ($weight_unit) {
				case 'kg':
					$weight_grams = ($weight * 1000);
					break;
				case 'lb':
					$weight_grams = ($weight * 453.59237);
					break;
				case 'g':
					$weight_grams = $weight;
					break;
			}
		}
		return $weight_grams;
	}

	public function parseKsuStatusText($sku_status)
	{
		if($sku_status==1){
			$str = "on";
		}else{
			$str = "off";
		}
		return $str;
	}

	public function getArrSkuVariantData(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$arr_variant_data = array();
		$arr_view_variant_data = array();
		if ($product) {
			//get variant
			$variants = $product->getVariants();
			if ($variants) {
				foreach ($variants as $key => $variant) {
					$arr_variant = array(
						"id"=>$variant->getId(),
						"option_name"=>$variant->getName()
					);
					//get variant options
					$variant_options = $variant->getVariantOptions();
					if ($variant_options) {
						$arr_variant_option = array();
						foreach ($variant_options as $vo_key => $variant_option) {
							array_push($arr_variant_option,$variant_option->getName());
						}
						$arr_variant["option_value"] = $arr_variant_option;
					}

					$arr_variant_data[$key] = $arr_variant;
					array_push($arr_view_variant_data, $variant->getName());
				}
			}
		}
		$return_data = array('variant_data'=>$arr_variant_data,'view_variant_data'=>$arr_view_variant_data);
		return $return_data;
	}

	public function getArrSkuVariantOption(Sku $sku)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$sku_variant_option = array();
		$sku_values = $em->getRepository(SkuValue::class)->findAllDataBySku($sku)->getQuery()->getArrayResult();
		if ($sku_values) {
			$sku_variant_option = array();
			foreach ($sku_values as $sku_value) {
				array_push($sku_variant_option, $sku_value['variantOption']['name']);
			}
		}
		return $sku_variant_option;
	}

	public function getArrVariantOptionData(Product $product)
	{
		$arr_variant_option_data = array();
		$arr_view_variant_option_data = array();
		$arr_variant_default_option = array();
		$variant_is_inventory = false;

		$isSetFirstOption=false;
		$isSetDefaultOption=false;

		if ($product) {
			$skus = $product->getSkus();
			if ($skus) {
				foreach ($skus as $key => $sku) {

					//get arr sku_variant_option

					$sku_status = $sku->getStatus();
					$sku_status_text = $this->parseKsuStatusText($sku_status);

					//get arr sku_variant_option
					$tmp_sku_variant_option = $this->getArrSkuVariantOption($sku);

					$inventory_policy_status = $sku->getInventoryPolicyStatus();
					if($inventory_policy_status){
						$variant_is_inventory = true;
					}

					$tmp_data = array(
						"id"	=> $sku->getId(),
						"status"=> $sku_status_text,
						"price"	=> floatval($sku->getPrice()),
						"compare_at_price"	=> floatval($sku->getCompareAtPrice()),
						"inventory_policy_status"=> $sku->getInventoryPolicyStatus(),
						"inventory_quantity"=> $sku->getInventoryQuantity(),
						"sku"	=> $sku->getSku(),
						"image"	=> $sku->getImage(),
						"image_small"	=> $sku->getImageSmall(),
						"default_option"=> $sku->getDefaultOption(),
						"option" => $tmp_sku_variant_option
					);
					$sku_key_name = strtolower(implode("-", $tmp_sku_variant_option));
					$arr_variant_option_data[$sku_key_name] = $tmp_data;
					array_push($arr_view_variant_option_data, $tmp_data);

					//set first_option or default_option
					if($sku->getStatus()==1){
						if($isSetFirstOption==false){
							$arr_variant_default_option = array_map('strtolower', $tmp_sku_variant_option);
							$isSetFirstOption=true;
						}
						if($isSetDefaultOption==false){
							if($sku->getDefaultOption()==1){
								$arr_variant_default_option = array_map('strtolower', $tmp_sku_variant_option);
								$isSetDefaultOption=true;
							}
						}
					}
				}
			}
		}
		$return_data = array(
			'variant_option_data'=>$arr_variant_option_data,
			'view_variant_option_data'=>$arr_view_variant_option_data,
			'variant_default_option'=>$arr_variant_default_option,
			'variant_is_inventory'=>$variant_is_inventory
		);
		return $return_data;
	}

	public function isVariants(Product $product)
	{
		//get variants
		$variants = $product->getVariants();
		if ($variants->count()) {
			$status = true;
		}else{
			$status = false;
		}
		return $status;
	}

	public function isVariantsFromArrVariantData(array $arr_variant_data)
	{
		$status = (count($arr_variant_data)>0) ? true : false ;
		return $status;
	}

	public function skuSaveProductInventoryStatus(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$arr_variant_option_data = $this->getArrVariantOptionData($product);

		$count_variant_option_data = count($arr_variant_option_data['variant_option_data']);
		if($count_variant_option_data>0){
			//have variant

			$variant_is_inventory = $arr_variant_option_data['variant_is_inventory'];
			if($variant_is_inventory){
				$product->setInventoryPolicyStatus(1);
			}else{
				$product->setInventoryPolicyStatus(0);
			}
			$em->persist($product);
			$em->flush();
		}
	}

	public function getPublicProductCacheImage($image_path)
	{
		$util = $this->container->get('utilities');
		$arr_image = array('s'=>null,'m'=>null,'l'=>null);

		if($image_path){
			//check file exists
			$web_path = $this->container->getParameter('web_path');
			$image_path = urldecode($image_path);
			if(file_exists($web_path.$image_path)){
				$arr_image['s'] = $util->publicCacheImage($image_path, 'img_product_small');
				$arr_image['m'] = $util->publicCacheImage($image_path, 'img_product_medium');
				$arr_image['l'] = $util->publicCacheImage($image_path, 'img_product_large');

				$arr_image['s'] = $util->removeHttp($arr_image['s']);
				$arr_image['m'] = $util->removeHttp($arr_image['m']);
				$arr_image['l'] = $util->removeHttp($arr_image['l']);
			}
		}
		return $arr_image;
	}

	public function saveProductImageSize(Product $product, $ori_image)
	{
		$image = $product->getImage();

		if($image!=$ori_image){
			$em = $this->container->get('doctrine')->getEntityManager();
			$arr_img = $this->getPublicProductCacheImage($image);

			if($arr_img['s']){$product->setImageSmall($arr_img['s']);}
			if($arr_img['m']){$product->setImageMedium($arr_img['m']);}
			if($arr_img['l']){$product->setImageLarge($arr_img['l']);}

			$em->persist($product);
			$em->flush();
		}
	}

	public function saveSkuImageSize(Sku $sku, $ori_image)
	{
		$image = $sku->getImage();
		if($image!=$ori_image){
			$em = $this->container->get('doctrine')->getEntityManager();
			$arr_img = $this->getPublicProductCacheImage($image);
			if($arr_img['s']){$sku->setImageSmall($arr_img['s']);}
			if($arr_img['m']){$sku->setImageMedium($arr_img['m']);}
			if($arr_img['l']){$sku->setImageLarge($arr_img['l']);}

			$em->persist($sku);
			$em->flush();
		}
	}

	public function saveProductCategoryImageSize(ProductCategory $product_category, $ori_image)
	{
		$image = $product_category->getImage();
		if($image!=$ori_image){
			$em = $this->container->get('doctrine')->getEntityManager();
			$arr_img = $this->getPublicProductCacheImage($image);
			if($arr_img['s']){$product_category->setImageSmall($arr_img['s']);}
			if($arr_img['m']){$product_category->setImageMedium($arr_img['m']);}
			if($arr_img['l']){$product_category->setImageLarge($arr_img['l']);}

			$em->persist($product_category);
			$em->flush();
		}
	}

	public function saveProductImageGallery(Product $product)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		//get image_gallery
		$image_path = $request->request->get('img_path');
		//get delete image_gallery
		$del_img_gallery_ids = $request->get('del_img_gallery');

		//save image_gallery
		if(!empty($image_path)){
			foreach ($image_path as $image_uri) {
				if($image_uri){
					$gallery_image = new ProductImage();
					$gallery_image->setImage($image_uri);
					$gallery_image->setProduct($product);
					$em->persist($gallery_image);
				}
			}
			$em->flush();
		}

		//delete image_gallery
		if(!empty($del_img_gallery_ids)){
			foreach ($del_img_gallery_ids as $del_img_id) {
				$del_style_image = $em->getRepository(ProductImage::class)->find($del_img_id);
				$em->remove($del_style_image);
			}
			$em->flush();
		}
	}
	public function getPercentProductDiscount($rs)
	{
		$data = Products::getPriceData($rs);
		$sale_price	=  $data['price'];
		$original_price	=  $data['compare_at_price'];
		$total = (($original_price - $sale_price) * 100) / $original_price;
		return (floor($total));
	}
	public function getPriceData($rs)
	{
		$result = array();
		$data = $rs[0];

		$price='';
		$compareAtPrice='';

		if( $rs['v_count'] > 0 ){

			if($rs['v_default_price']){
				$price = $rs['v_default_price'];
				if($rs['v_default_compare_at_price']){
					$compareAtPrice = $rs['v_default_compare_at_price'];
				}
			}else{
				$price = $rs['v_price'];
				if($rs['v_compare_at_price']){
					$compareAtPrice = $rs['v_compare_at_price'];
				}
			}

		}else{
			$price = $data->getPrice();
			if($data->getCompareAtPrice()){
				$compareAtPrice = $data->getCompareAtPrice();
			}
		}

		$result['price'] = $price;
		$result['compare_at_price'] = $compareAtPrice;

		return $result;
	}

	public function saveDiscountAppliesSpecificProducts($discount)
	{
		$em = $this->container->get('doctrine')->getEntityManager();

		if($discount->getAppliesTo() == $this->container->getParameter('cons_discount_applies_specific_products') ){
			$request = $this->container->get('request_stack')->getCurrentRequest();
			$data_request = $request->request->get('admin_discount');
			// $data_request = $request->get('admin_discount');
			if(isset($data_request['appliesToSpecificProducts'])){
				//add specificProducts
				$arr_appliesToSpecificProducts = $data_request['appliesToSpecificProducts'];
				foreach ($arr_appliesToSpecificProducts as $appliesToSpecificProductId) {
					$specific_product = $em->getRepository(Product::class)->find($appliesToSpecificProductId);
					$discount->addProducts($specific_product);
				}
				$em->flush();
			}
			if(isset($data_request['removeAppliesToSpecificProducts'])){
				//remove specificProducts
				$arr_removeAppliesToSpecificProducts = $data_request['removeAppliesToSpecificProducts'];
				foreach ($arr_removeAppliesToSpecificProducts as $removeAppliesToSpecificProductId) {
					$remove_specific_product = $em->getRepository(Product::class)->find($removeAppliesToSpecificProductId);
					$discount->removeProducts($remove_specific_product);
				}
				$em->flush();
			}
		}else{
			//remove all products_discounts
			$products_discounts = $discount->getProducts();
			if($products_discounts){
				foreach ($products_discounts as $remove_product) {
					$discount->removeProducts($remove_product);
				}
				$em->flush();
			}
		}
	}

	public function savePromotionProducts($promotion)
	{
		$em = $this->container->get('doctrine')->getEntityManager();

		$request = $this->container->get('request_stack')->getCurrentRequest();
		$data_request = $request->request->get('admin_promotion');

		if(isset($data_request['specificProducts'])){
			//add specificProducts
			$arr_specificProducts = $data_request['specificProducts'];
			foreach ($arr_specificProducts as $specificProductId) {
				$specific_product = $em->getRepository(Product::class)->find($specificProductId);
				$promotion->addProducts($specific_product);
			}
			$em->flush();
		}
		if(isset($data_request['removespecificProducts'])){
			//remove specificProducts
			$arr_removespecificProducts = $data_request['removespecificProducts'];
			foreach ($arr_removespecificProducts as $removespecificProductId) {
				$remove_specific_product = $em->getRepository(Product::class)->find($removespecificProductId);
				$promotion->removeProducts($remove_specific_product);
			}
			$em->flush();
		}

	}

	public function setArrProductVariantsData($product)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$arr_data = array(
			'arr_sku_variant_data'=>array(),
			'arr_variant_option_data'=>array(),
		);

		if($product){
			//get variant
			$arr_sku_variant_data = $this->getArrSkuVariantData($product);
			$arr_data['arr_sku_variant_data'] = $arr_sku_variant_data;

			// get variant_option_data
			// // $skus = $this->getDoctrine()->getRepository(Sku::class)->findByProduct($product); //not used
			$arr_variant_option_data = $this->getArrVariantOptionData($product);
			$arr_data['arr_variant_option_data'] = $arr_variant_option_data;

			//get uri_variant for manual set variant by url
			$uri_variant = $request->get('v');
			if($uri_variant){
				$is_valid_uri_variant = true;
				$uri_variant = urldecode($uri_variant);
				$arr_uri_variant = explode("-", $uri_variant);
				//validate uri_variant
				foreach ($arr_uri_variant as $key => $value) {
					if(!in_array($value, $arr_data['arr_sku_variant_data']['variant_data'][$key]['option_value'])){
						$is_valid_uri_variant = false;
						break;
					}
				}
				if($is_valid_uri_variant){
					$arr_variant = array_map('strtolower', $arr_uri_variant);
					$arr_variant_option_data['variant_default_option']=$arr_uri_variant;
					$arr_data['arr_variant_option_data']['variant_default_option']=$arr_variant;
				}
			}

			//set default variant option
			if( count($arr_variant_option_data['variant_default_option'])>0 ){
				foreach ($arr_data['arr_sku_variant_data']['variant_data'] as $key => $value) {
					$arr_data['arr_sku_variant_data']['variant_data'][$key]['selected'] = strtolower($arr_variant_option_data['variant_default_option'][$key]);
				}
			}
		}

		return $arr_data;
	}


	public function getCartSession()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();
		$session_cart = $session->get('cart');
		return $session_cart;
	}

	public function setCartSession($session_cart)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();
		$session->set('cart', $session_cart);
	}

	public function removeCartSession()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();
		$session->remove('cart');
	}

	public function initCartSession()
	{
		// $request = $this->container->get('request_stack')->getCurrentRequest();
		// $session = $request->getSession();
		// $session_cart = $session->get('cart');

		$session_cart = $this->getCartSession();
		if(!isset($session_cart)){
			$session_cart = array('products'=>array());
			$this->setCartSession($session_cart);
		}
	}

	public function updateCartSession($session_cart)
	{
		// $request = $this->container->get('request_stack')->getCurrentRequest();
		// $session = $request->getSession();

		if($session_cart){
			if(isset($session_cart['products'])){
				if(count($session_cart['products'])>0){
					//$session->set('cart', $session_cart);
					$this->setCartSession($session_cart);
				}else{
					//remove cart session
					// $session->remove('cart');
					$this->removeCartSession();
				}
			}
		}
	}

	public function getInventoryData($product_id, $sku_id=false)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$em = $this->container->get('doctrine')->getEntityManager();

		if($sku_id){
			//get inventory from sku
			$sku = $em->getRepository(Sku::class)->getActiveDataJoinedProductBySkuId($sku_id)->getQuery()->getOneOrNullResult();
			$inventory_status = $sku->getInventoryPolicyStatus();
			$inventory_quantity = $sku->getInventoryQuantity();
		}else{
			//get inventory from product
			$product_data = $em->getRepository(Product::class)->getActiveData($product_id, $request->getLocale())->getQuery()->getOneOrNullResult();
			$product = $product_data[0];
			$inventory_status = $product->getInventoryPolicyStatus();
			$inventory_quantity = $product->getInventoryQuantity();
		}
		return array('inventory_status'=>$inventory_status, 'inventory_quantity'=>$inventory_quantity);
	}

	public function createCartSessionData($data)
	{
		//set initial cart session
		$this->initCartSession();

		//prepare index
		$product_id = intval($data['product_id']);

		$quantity = intval($data['quantity']);
		if(isset($data['varian_option'])){
			$is_variant = true;
			$arr_varian_option = $data['varian_option'];
			$sku_id = $arr_varian_option['id'];
			//set data_index for search in cart
			$data_index = $product_id.'-'.$sku_id;

			//get inventory by sku
			$arr_inventory = $this->getInventoryData($product_id, $sku_id);
		}else{
			//no variant
			$is_variant = false;
			$sku_id = '';
			//set data_index for search in cart
			$data_index = $product_id;

			//get inventory by product
			$arr_inventory = $this->getInventoryData($product_id);
		}
		//set default return data
		$arr_result = array('status'=>false, 'arr_inventory'=>$arr_inventory);

		//add new item on array
		$cart_item = array(
			'quantity' 	=> $quantity,
			'product_id'=> $product_id,
			'sku_id' 	=> $sku_id
		);

		// check array products in cart
		// $session_cart = $session->get('cart');
		$session_cart = $this->getCartSession();

		if(isset($session_cart['products']))
		{
			// check if the item is in the array
			$session_cart_product = $session_cart['products'];
			if(array_key_exists($data_index, $session_cart_product))
			{
				//exists product
				$current_quantity = $session_cart_product[$data_index]['quantity'];
				$quantity += $current_quantity;
				$session_cart_product[$data_index]['quantity'] = $quantity;
			}else{
				//new product
				$session_cart_product[$data_index] = $cart_item;
			}

			if (false === $this->authorizationChecker->isGranted('ROLE_CLIENT')) {
				// check policy inventory
				if($arr_inventory['inventory_status']==1){
					if($quantity > $arr_inventory['inventory_quantity']){
						return $arr_result;
					}
				}
			}

			$session_cart['products'] = $session_cart_product;
			// $session->set('cart', $session_cart);
			$this->updateCartSession($session_cart);
		}

		$arr_result['status'] = true;
		return $arr_result;
	}

	public function getCartSessionDataIndex($product_id, $sku_id)
	{
		if($sku_id){
			$data_index = $product_id.'-'.$sku_id;
		}else{
			$data_index = $product_id;
		}
		return $data_index;
	}

	public function validateInventoryInCart($arr_cart_data)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();

		$arr_products = $arr_cart_data['products'];
		$message = array();
		$arr_result = array('status'=>true, 'message'=>$message);

		foreach ($arr_products as $key => $arr_product) {
			$product_id = $arr_product['product_id'];
			$sku_id = $arr_product['sku_id'];
			$quantity = $arr_product['quantity'];
			$arr_inventory = $this->getInventoryData($product_id, $sku_id);

			// check policy inventory
			if($arr_inventory['inventory_status']==1){
				if($arr_inventory['inventory_quantity']==0){
					//remove product in cart
					$this->removeProductInCart($product_id, $sku_id);

					//set message and status
					$message[$key] = 'Product "'.$arr_product['title'].'" not available';
					$session->getFlashBag()->add('inventory_errors', $message[$key]);
					$arr_result['status'] = false;

				}elseif($quantity > $arr_inventory['inventory_quantity']){
					//update quantity to maximum (inventory_quantity)
					$arr_result_update = $this->updateProductQuantityInCart($arr_inventory['inventory_quantity'], $product_id, $sku_id);
					if($arr_result_update['status']){
						//set message and status
						$message[$key] = 'Product "'.$arr_product['title'].'" stock available '.$arr_inventory['inventory_quantity'].' remaining';
						$session->getFlashBag()->add('inventory_errors', $message[$key]);
						$arr_result['status'] = false;
					}
				}
			}
		}

		$arr_result['message'] = $message;
		return $arr_result;
	}

	public function updateProductQuantityInCart($quantity, $product_id, $sku_id)
	{
		if($sku_id){
			//get inventory by sku
			$arr_inventory = $this->getInventoryData($product_id, $sku_id);
		}else{
			//get inventory by product
			$arr_inventory = $this->getInventoryData($product_id);
		}
		//set default return data
		$arr_result = array('status'=>false, 'arr_inventory'=>$arr_inventory);

		$session_cart = $this->getCartSession();
		$data_index = $this->getCartSessionDataIndex($product_id, $sku_id);
		if(count($session_cart['products'])>0){
			if (array_key_exists($data_index, $session_cart['products'])) {

				if (false === $this->authorizationChecker->isGranted('ROLE_CLIENT')) {
					// check policy inventory
					if($arr_inventory['inventory_status']==1){
						if($quantity > $arr_inventory['inventory_quantity']){
							return $arr_result;
						}
					}
		        }

				$session_cart['products'][$data_index]['quantity'] = $quantity;
				$this->updateCartSession($session_cart);
				$arr_result['status'] = true;
			}
		}

		return $arr_result;
	}

	public function removeProductInCart($product_id, $sku_id)
	{
		// $key = array_search($data_index, array_column($session_cart['products'], 'product_id'));

		// $request = $this->container->get('request_stack')->getCurrentRequest();
		// $session = $request->getSession();
		// $session_cart = $session->get('cart');

		$session_cart = $this->getCartSession();
		$data_index = $this->getCartSessionDataIndex($product_id, $sku_id);
		if(count($session_cart['products'])>0){
			if (array_key_exists($data_index, $session_cart['products'])) {
			    unset($session_cart['products'][$data_index]);
				$this->updateCartSession($session_cart);
			}
		}
	}

	public function getDeliveryDate()
	{
		$delivery_date = null;
		$em = $this->container->get('doctrine')->getEntityManager();
		$delivery_method = $em->getRepository(DeliveryMethod::class)->createQueryBuilder('d')->getQuery()->getOneOrNullResult();
		if($delivery_method){

			if($delivery_method->getStatus()){
				//enable delivery
				$place_order_time = $delivery_method->getPlaceOrderTime();
				$before_set_date = $delivery_method->getBeforeSetDate();
				$after_set_date = $delivery_method->getAfterSetDate();
				$non_delivery_date = $delivery_method->getNonDeliveryDate();
				$place_order_time->setDate(date('Y'), date('m'), date('d'));

				$order_date_time = new \DateTime();

				if($order_date_time<$place_order_time){
					$delivery_date_str = $before_set_date;
				}else{
					$delivery_date_str = $after_set_date;
				}

				if($non_delivery_date){
					$chk_loop = count($non_delivery_date);
				}else{
					$chk_loop = 0;
					$non_delivery_date = array();
				}

				if($chk_loop>6){
					//no calculate, because setting non delivery-date Monday-Sunday (7 days of week)
				}else{
					$delivery_date = new \DateTime($delivery_date_str);
					$delivery_date->setTime(0, 0, 0);

					// debug delivery_date
					// $delivery_date = $order_date_time->modify($delivery_date_str);

					//blackout delivery days
					$valid_blackout = true;

					//set match holiday date
					$is_match_holiday = false;
					$is_match_first_holiday = false;

					do {
						//check blackout
						$delivery_date_word = $delivery_date->format('l'); //Wednesday
						if(in_array($delivery_date_word, $non_delivery_date)){
							//unavailable find next day, current day is blackout
							$delivery_date->modify('+1 day');
						}else{

							//check holiday
							$count_holiday = $em->getRepository(Holiday::class)->findPublicHolidayByDate($delivery_date)->getQuery()->getOneOrNullResult();
							if($count_holiday){
								//match holiday
								//unavailable find next day, current day is holiday
								$delivery_date->modify('+1 day');
								//set parameter is_match_holiday
								if($is_match_holiday==false){
									$is_match_holiday=true;
								}
							}else{
								//not match holiday

								//not check is_match_holiday
								$valid_blackout = false;

								//check is_match_holiday for +2business day
								// if(($is_match_holiday==true) && ($is_match_first_holiday==false)){
								// 	$delivery_date->modify('+1 day'); //if match holiday will +2business day (find next business day)
								// 	$is_match_first_holiday = true;
								// }else{
								// 	$valid_blackout = false;
								// }
							}

						}
					} while ($valid_blackout==true);
				} //end

			}else{
				//disable delivery
			}
		}

		return $delivery_date;
	}

	public function getArrProductCartData()
	{
		$util = $this->container->get('utilities');
		$em = $this->container->get('doctrine')->getEntityManager();

		$request = $this->container->get('request_stack')->getCurrentRequest();

		// $session = $request->getSession();
		// $session_cart = $session->get('cart');

		$session_cart = $this->getCartSession();

		$arr_products = array();
		$arr_summary = array();
		$arr_delivery_information = array('shipping_address'=>array(), 'billing_address'=>array());

		if(isset($session_cart['products']))
		{
			$arr_summary['shipping_cost'] = 0;
			$arr_summary['shipping_cost_by_distance'] = 0;
			$arr_summary['item_count'] = 0;
			$arr_summary['sub_total'] = 0;
			$arr_summary['total'] = 0;
			$arr_summary['discount_amount'] = 0;
			$arr_summary['discount_code'] = null;
			$arr_summary['weight_grams_total'] = 0;
			$arr_summary['payment_option'] = null;
			$arr_summary['direction'] = array(
				'distance'=>'',
				'distance_text'=>'',
				'origin_showroom_id'=>'',
				'origin_lat_lng'=>'',
				'destination_delivery_address_id'=>'',
				'destination_lat_lng'=>'',
				'origin_showroom_name'=>''
			);

			$session_cart_product = $session_cart['products'];
			if(count($session_cart_product)>0){
				$i=0;
				foreach ($session_cart_product as $arr_cart_product)
				{
					$quantity = $arr_cart_product['quantity'];
					$product_id = $arr_cart_product['product_id'];
					$sku_id = $arr_cart_product['sku_id'];
// debug product_id
// $product_id = 9999;

					//Query with public status
					$product_data = $em->getRepository(Product::class)->getActiveDataById($product_id, $request->getLocale())->getQuery()->getOneOrNullResult();
					// getOneOrNullResult
					// getSingleResult

					if(!$product_data){
						//not found this procut, remove the product from session
						$this->removeProductInCart($product_id, $sku_id);
						continue;
					}

					$product = $product_data[0];
					if($sku_id){
						//get product variant data
						$sku = $em->getRepository(Sku::class)->getActiveDataBySkuId($sku_id)->getQuery()->getOneOrNullResult();
						if($sku){
							//get arr sku_variant_option
							$tmp_sku_variant_option = $this->getArrSkuVariantOption($sku);
							$sku_title = $sku->getSku();
							$imageSmall = ($sku->getImageSmall()) ? $sku->getImageSmall() : $product->getImageSmall();
							$imageMedium = ($sku->getImageMedium()) ? $sku->getImageMedium() : $product->getImageMedium();
							$price = floatval($sku->getPrice());
							$compareAtPrice = floatval($sku->getCompareAtPrice());
							$inventoryPolicyStatus = $sku->getInventoryPolicyStatus();
							$inventoryQuantity = $sku->getInventoryQuantity();
							$grams = $sku->getGrams();
							$weight = $sku->getWeight();
							$weightUnit = $sku->getWeightUnit();
							$variant_option = $tmp_sku_variant_option;
						}else{
							//no sku data remove the product from session
							$this->removeProductInCart($product_id, $sku_id);
							continue;
						}

					}else{
						// no variant data
						$sku_title = $product->getSku();
						$imageSmall = $product->getImageSmall();
						$imageMedium = $product->getImageMedium();
						$price = floatval($product->getPrice());
						$compareAtPrice = floatval($product->getCompareAtPrice());
						$inventoryPolicyStatus = $product->getInventoryPolicyStatus();
						$inventoryQuantity = $product->getInventoryQuantity();
						$grams = $product->getGrams();
						$weight = $product->getWeight();
						$weightUnit = $product->getWeightUnit();
						$variant_option = array();
					}

					$arr_products[$i] = array(
						'quantity' => $quantity,
						'product_id' => $product_id,
						'sku_id' => $sku_id,
						'title' => $product->getTitle(),
						'slug' => $product->getSlug(),
						'sku' => $sku_title,
						'image_small' => $imageSmall,
						'image_medium' => $imageMedium,
						'price' => $price,
						'compare_at_price' => $compareAtPrice,
						'inventory_policy_status' => $inventoryPolicyStatus,
						'inventory_quantity' => $inventoryQuantity,
						'grams' => $grams,
						'weight' => $weight,
						'weight_unit' => $weightUnit,
						'variant_option' => $variant_option,
						'discount_amount' => 0
					);

					$arr_products[$i]['amount'] = $price * $quantity;
					$arr_summary['item_count']+=$quantity;
					$arr_summary['sub_total']+=$arr_products[$i]['amount'];

					//set weight total
					$arr_products[$i]['weight_grams_sub_total'] = ($grams) ? ($grams * $quantity) : 0 ;
					$arr_summary['weight_grams_total'] += $arr_products[$i]['weight_grams_sub_total'];

					$i++;
				}//end foreach
			}//end if

			//get delivery information
			$arr_delivery_information = $this->getDeliveryInformation($session_cart);

			//set calculateroute distance for shipping cost
			$origin_showroom = $this->setCalculateRouteDistance($session_cart, $arr_delivery_information);
			$session_cart = $this->getCartSession(); // update session_cart
			if(isset($session_cart['direction']) && $origin_showroom){
				$arr_summary['direction'] = $session_cart['direction'];
				$arr_summary['direction']['origin_showroom_name'] = $origin_showroom->getTitle();
			}

			//get shipping rate
			$arr_shipping_cost = $this->getDeliveryCostByCondition($arr_summary);
			$arr_summary['shipping_cost'] = $arr_shipping_cost['shipping_cost'];
			$arr_summary['shipping_cost_by_distance'] = $arr_shipping_cost['shipping_cost_by_distance'];

			//get use discount code
			$arr_use_discount_code = $this->getUseDiscountCode($arr_products, $arr_summary);
			$arr_products = $arr_use_discount_code['products'];
			$arr_summary = $arr_use_discount_code['summary'];

			//get payment option
			$payment_option = $this->getSelectedPaymentOption();
			$arr_summary['payment_option'] = $payment_option;

			//set total price
			$sub_total = $arr_summary['sub_total'];
			if(isset($arr_summary['discount_amount'])){
				$sub_total = ($sub_total - $arr_summary['discount_amount']);
			}
			$arr_summary['total'] = $sub_total + $arr_summary['shipping_cost'];

		}

		return array(
			'products' => $arr_products,
			'summary' => $arr_summary,
			'delivery_information' => $arr_delivery_information,
		);
	}

	public function getSelectedPaymentOption()
	{
		$code = null;
		$selected_payment_option = $this->getSessionPaymentOption();
		if($selected_payment_option){
			$payment_options = $this->getPaymentGateway();
			if (in_array($selected_payment_option, $payment_options)) {
				$code = $selected_payment_option;
			}else{
				//not found remove session
				$this->removeSessionPaymentOption();
			}
		}
		return $code;
	}

	public function getDeliveryInformation($session_cart)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$arr_delivery_information = array('shipping_address'=>array(),'billing_address'=>array());

		if(isset($session_cart['shipping_address_id'])){
			$shipping_address = $em->getRepository(DeliveryAddress::class)->findDataById($session_cart['shipping_address_id'])->getQuery()->getArrayResult();
			if($shipping_address){
				$arr_delivery_information['shipping_address'] = $shipping_address[0];
			}else{
				//remove session shipping
				$this->removeSessionShippingAddress();
			}
		}

		$is_remove_billing = false;
		if(isset($session_cart['billing_address_id'])){
			if((isset($session_cart['shipping_address_id'])) && ($session_cart['shipping_address_id'] == $session_cart['billing_address_id'])){
				if($shipping_address){
					$arr_delivery_information['billing_address'] = $shipping_address[0];
				}else{
					$is_remove_billing = true;
				}
			}else{
				$billing_address = $em->getRepository(DeliveryAddress::class)->findDataById($session_cart['billing_address_id'])->getQuery()->getArrayResult();
				if($billing_address){
					$arr_delivery_information['billing_address'] = $billing_address[0];
				}else{
					$is_remove_billing = true;
				}
			}
			if($is_remove_billing){
				//remove session billing
				$this->removeSessionBillingAddress();
			}
		}

		return $arr_delivery_information;
	}

	public function getUseDiscountCode($arr_products, $arr_summary)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$is_authenticated = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
		$user = $this->container->get('security.token_storage')->getToken()->getUser();

		$discount_code = $this->getSessionDiscountCode();
		if($discount_code){

			$arr_cart_data = array('products'=>$arr_products, 'summary'=>$arr_summary);

			if(!$is_authenticated){
				//remove discount code session
				$this->removeSessionDiscountCode();
				return $arr_cart_data;
			}

			$arr_result = $this->validateDiscountCode( array('code'=>$discount_code), $arr_cart_data );
			if($arr_result['status']){
				//valid discount code
				$discount_data = $em->getRepository(Discount::class)->getPublishDiscountCodeWitTotalUsage($discount_code)->getQuery()->getOneOrNullResult();
				$discount = $discount_data[0];

				if($discount){

					$sub_total = $arr_summary['sub_total'];
					$shipping_cost = $arr_summary['shipping_cost'];

					$applies_to = $discount->getAppliesTo();
					$onlyAppliesOnceProductPerProduct = $discount->getOnlyAppliesOnceItemPerProduct();
					$discount_type = $discount->getDiscountType();
					$discount_value = $discount->getDiscountValue();

					$discount_amount = 0;

					if($applies_to==1){
						//Entire order
						if($discount_type==1){
							//percentage
							if($discount_value>0){
								$discount_amount = floor($sub_total * ($discount_value/100));
								//floor - round fractions down (100.1 = 100)
							}
						}elseif($discount_type==2){
							//fixed amount
							if($discount_value<=$sub_total){
								$discount_amount = $discount_value;
							}else{
								//discount more than sub_total
								$discount_amount = $sub_total;
							}
						}

					}elseif($applies_to==2){
						//Specific products
						$product_discount = $em->getRepository(Product::class)->getPublishProductsDiscountByDiscountCode($discount_code)->getQuery()->getArrayResult();
						foreach ($arr_products as $key => $cart_product) {

							$cart_product_id = $cart_product['product_id'];
							$cart_product_price = $cart_product['price'];
							$cart_product_quantity = $cart_product['quantity'];
							$product_discount_amount = 0;

							$found_product_discount_key = array_search($cart_product_id, array_column($product_discount, 'id'));
							if($found_product_discount_key !== false){
								// $arr_found_product = $product_discount[$found_product_discount_key];

								if($discount_type==1){
									//percentage
									if($discount_value>0){
										$product_discount_amount = floor($cart_product_price * ($discount_value/100));
										//floor - round fractions down (100.1 = 100)
										if(!$onlyAppliesOnceProductPerProduct && $cart_product_quantity>1){
											$product_discount_amount = ($product_discount_amount*$cart_product_quantity);
										}
									}

								}elseif($discount_type==2){
									//fixed amount
									if($discount_value<=$cart_product_price){
										$product_discount_amount = $discount_value;
									}else{
										//discount more than sub_total
										$product_discount_amount = $cart_product_price;
									}
									if(!$onlyAppliesOnceProductPerProduct && $cart_product_quantity>1){
										$product_discount_amount = ($product_discount_amount*$cart_product_quantity);
									}
								}

								$arr_products[$key]['discount_amount'] = $product_discount_amount;
								$discount_amount += $product_discount_amount;
							}//found
						}
					}

					//set product_summary
					$arr_summary['discount_amount'] = $discount_amount;
					$arr_summary['discount_code'] = $discount_code;

// echo 'Total<br/>';
// print_r($arr_products);
// print_r($arr_summary);
// exit;

				}else{
					//not found discount code
					//remove discount code session
					$this->removeSessionDiscountCode();
					return $arr_cart_data;
				}
			}else{
				//invalid discount code
				//remove discount code session
				$this->removeSessionDiscountCode();
				return $arr_cart_data;
			}
		}

		return array('products'=>$arr_products, 'summary'=>$arr_summary);
	}

	public function getDeliveryCostByCondition($arr_summary)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session_cart = $this->getCartSession();

		$sub_total = $arr_summary['sub_total'];
		$weight_grams_total = $arr_summary['weight_grams_total'];
		$shipping_cost = 0;
		$shipping_cost_by_distance = 0;

		$shipping_price_based_rate = $this->container->getParameter('shipping_price_based_rate');
		$shipping_weight_based_rate = $this->container->getParameter('shipping_weight_based_rate');
		$shipping_distance_based_rate = $this->container->getParameter('shipping_distance_based_rate');

		$shipping_rates = $em->getRepository(ShippingRate::class)->findAllData()->getQuery()->getResult();
		if($shipping_rates){
			foreach ($shipping_rates as $shipping_rate) {
				$rate_type = $shipping_rate->getRateType();
				$minimum_range = $shipping_rate->getMinimumRange();
				$maximum_range = $shipping_rate->getMaximumRange();
				$rate_amount = $shipping_rate->getRateAmount();
				$is_match=false;

				switch ($rate_type) {
					case $shipping_price_based_rate:
						//price_based_rate
						if(($sub_total>=$minimum_range)){
							if($maximum_range==0){
								//no limit for maximum
								$is_match=true;
							}else{
								if($sub_total<=$maximum_range){
									$is_match=true;
								}
							}
						}
						if($is_match){
							$shipping_cost += $rate_amount;
						}
					break;

					case $shipping_weight_based_rate:
						//weight_based_rate
						if(($weight_grams_total>=$minimum_range)){
							if($maximum_range==0){
								//no limit for maximum
								$is_match=true;
							}else{
								if($weight_grams_total<=$maximum_range){
									$is_match=true;
								}
							}
						}
						if($is_match){
							$shipping_cost += $rate_amount;
						}
					break;

					case $shipping_distance_based_rate:
						//distance_based_rate
						$variable_cost = $shipping_rate->getVariableCost();
						if( isset($session_cart['direction']) && isset($session_cart['direction']['distance']) ){
							$distance = $session_cart['direction']['distance'];
							$fixed_cost = $rate_amount;
							$min_distance = $minimum_range * 1000;
							$max_distance = $maximum_range;
							if($max_distance>0){
								$max_distance = $max_distance * 1000;
							}

							if($distance>=$min_distance){

								if($max_distance==0){
									//no limit for maximum
									$is_match=true;
								}else{
									if($distance<=$max_distance){
										$is_match=true;
									}
								}

							}
							if($is_match){
								$distance_cost = 0;
								if($variable_cost>0){
									$var_cost_per_meter = $variable_cost/1000;
									$distnace_for_cal_meter = $distance - $min_distance;
									$distance_cost = ($distnace_for_cal_meter*$var_cost_per_meter);
								}
								if($fixed_cost>0){
									$distance_cost += $fixed_cost;
								}

								$shipping_cost += $distance_cost;
								$shipping_cost_by_distance += $distance_cost;
							}

						}


					break;

				}
			}//endfor
		}

		$arr_return = array(
			'shipping_cost'=>$shipping_cost,
			'shipping_cost_by_distance'=>$shipping_cost_by_distance
		);
		return $arr_return;
	}

	public function validateDiscountCode($arr_code_data, $arr_cart_data)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$app_col = $this->container->get('collections');
		$arr = array(
			'status'=> true,
			'error_message'=> ''
		);

		$is_authenticated = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
		if($is_authenticated){
			$user = $this->container->get('security.token_storage')->getToken()->getUser();
			$user_id = $user->getId();
		}else{
			$arr['status'] = false;
			$arr['error_message'] = 'access denied for user';
			return $arr;
		}

		$discount_code = $arr_code_data['code'];

		// $user_id = $arr_code_data['id'];
		// $email = $arr_code_data['email'];

		// //get user by id, email
		// $user = $em->getRepository(User::class)->getActiveMemberByIdAndEmail($user_id, $email)->getQuery()->getOneOrNullResult();
		// if(!$user){
		// 	$arr['status'] = false;
		// 	$arr['error_message'] = 'access denied for user';
		// 	return $arr;
		// }

		if(count($arr_cart_data['products'])<=0){
			$arr['status'] = false;
			$arr['error_message'] = 'no data in cart';
			return $arr;
		}

		if($discount_code){
			// getOneOrNullResult
			// getArrayResult
			$discount_data = $em->getRepository(Discount::class)->getPublishDiscountCodeWitTotalUsage($discount_code)->getQuery()->getOneOrNullResult();
			$discount = $discount_data[0];

			if($discount){
				$total_usage = $discount_data['total_usage'];
				$usage_limit_discount_total_value = $discount->getUsageLimitDiscountTotalValue();

				$status_code = $app_col->isDiscountCodeActive($discount);

				if($status_code=='active'){
					$discount_type = $discount->getDiscountType();
					$discount_value = $discount->getDiscountValue(); // 1=Percentage, 2=FixedAmount

					$summary_sub_total = $arr_cart_data['summary']['sub_total'];
					$summary_item_count = $arr_cart_data['summary']['item_count'];

					do {
						// Minimum requirement
						$minimumRequirement = $discount->getMinimumRequirement(); // 1=None, 2=MinimumPurchaseAmount, 3=MinimumQuantityOfItems
						if($minimumRequirement==2){
							// MinimumPurchaseAmount
							$minimumRequirementAmountValue = $discount->getMinimumRequirementAmountValue();
							if($summary_sub_total<$minimumRequirementAmountValue){
								$arr['status'] = false;
								$arr['error_message'] = "The minimum purchase amount is ".$minimumRequirementAmountValue."฿ or more";
								break;
							}
						}elseif($minimumRequirement==3){
							// MinimumQuantityOfItems
							$minimumRequirementQuantityValue = $discount->getMinimumRequirementQuantityValue();
							if($summary_item_count<$minimumRequirementQuantityValue){
								$arr['status'] = false;
								$arr['error_message'] = "The minimum quantity of items is ".$minimumRequirementQuantityValue." or more";
								break;
							}
						}

						//check total usage limits
						$usageLimitDiscountTotal = $discount->getUsageLimitDiscountTotal();
						if($usageLimitDiscountTotal==1){
							if($total_usage>=$usage_limit_discount_total_value){
								//coupon not available
								$arr['status'] = false;
								$arr['error_message'] = "This coupon code has reached its redemption limit";
								break;
							}else{
								//coupon available
							}
						}else{
							//unlimited
						}

						//check limit to one use per customer
						$usageLimitDiscountOnePerCustomer = $discount->getUsageLimitDiscountOnePerCustomer();
						if($usageLimitDiscountOnePerCustomer==1){
							$total_user_used_discount_code = $em->getRepository(Discount::class)->getCountDiscountCodeUsageByUser($discount_code, $user_id)->getQuery()->getSingleScalarResult();
							if($total_user_used_discount_code>0){
								//coupon not available
								$arr['status'] = false;
								$arr['error_message'] = "This coupon code already used";
								break;
							}
						}

					} while (0);

				}else{
					//error coupon not active (expired or scheduled)
					$arr['status'] = false;
					$arr['error_message'] = 'Invalid discount code';
				}
			}else{
				//invalid code
				$arr['status'] = false;
				$arr['error_message'] = 'Invalid discount code';
			}
		}

		return $arr;
	}

	public function removeSessionDiscountCode()
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['discount_code'])){
			unset($session_cart['discount_code']);
			$this->updateCartSession($session_cart);
		}
	}
	public function setSessionDiscountCode($discount_code)
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['products'])){
			$session_cart['discount_code'] = $discount_code;
			$this->updateCartSession($session_cart);
		}
	}
	public function getSessionDiscountCode()
	{
		$discount_code = false;
		$session_cart = $this->getCartSession();
		if(isset($session_cart['discount_code'])){
			$discount_code = $session_cart['discount_code'];
		}
		return $discount_code;
	}


	public function setSessionInitShippingAddress($arr_delivery_address)
	{
		$session_cart = $this->getCartSession();
		if(!isset($session_cart['shipping_address_id'])){
			if($arr_delivery_address){
				foreach ($arr_delivery_address as $shipping_address) {
					//get first address
					$this->setSessionShippingAddress($shipping_address['id']);
					break;
				}
			}
		}
	}
	public function setSessionInitBillingAddress($default_billing_address)
	{
		$session_cart = $this->getCartSession();
		if(!isset($session_cart['billing_address_id'])){
			if($default_billing_address){
				$this->setSessionBillingAddress($default_billing_address['id']);
			}else{
				if(isset($session_cart['shipping_address_id'])){
					//no default billing set same shipping if exists
					$this->setSessionBillingAddress($session_cart['shipping_address_id']);
				}
			}
		}
	}
	public function setSessionShippingAddress($shipping_address_id)
	{
		if($shipping_address_id){
			$session_cart = $this->getCartSession();
			if(isset($session_cart['products'])){
				$session_cart['shipping_address_id'] = $shipping_address_id;
				$this->updateCartSession($session_cart);
			}
		}
	}
	public function setSessionBillingAddress($billing_address_id)
	{
		if($billing_address_id){
			$session_cart = $this->getCartSession();
			if(isset($session_cart['products'])){
				$session_cart['billing_address_id'] = $billing_address_id;
				$this->updateCartSession($session_cart);
			}
		}
	}
	public function removeSessionShippingAddress()
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['shipping_address_id'])){
			unset($session_cart['shipping_address_id']);
			$this->updateCartSession($session_cart);
		}
	}
	public function removeSessionBillingAddress()
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['billing_address_id'])){
			unset($session_cart['billing_address_id']);
			$this->updateCartSession($session_cart);
		}
	}

	public function getPaymentGateway()
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$payment_gateway = $em->getRepository(PaymentGateway::class)->createQueryBuilder('p')->getQuery()->getOneOrNullResult();
		$pw = array();
		if($payment_gateway){
			$pw = $payment_gateway->getGateway();
		}
		return $pw;
	}

	public function setSessionPaymentOption($code)
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['products'])){
			$session_cart['payment_option'] = $code;
			$this->updateCartSession($session_cart);
		}
	}
	public function getSessionPaymentOption()
	{
		$payment_option = null;
		$session_cart = $this->getCartSession();
		if(isset($session_cart['payment_option'])){
			$payment_option = $session_cart['payment_option'];
		}
		return $payment_option;
	}
	public function removeSessionPaymentOption()
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['payment_option'])){
			unset($session_cart['payment_option']);
			$this->updateCartSession($session_cart);
		}
	}

	public function generateOrderNumber($user_id = null)
	{
		if($user_id){
			$key_string = 'C'.time().$user_id;
		}else{
			$num_str = sprintf("%02d", mt_rand(0, 99));
			$key_string = 'G'.time().$num_str;
		}

		//$rand_str_length = 16;
		//$key_string = $this->random_str($rand_str_length);

		do {
			$order_number = $this->generateSerial($key_string, null, null);
		} while ( $this->checkifOrderNumberexist($order_number) );

		return $order_number;
	}

	public function checkifOrderNumberexist($order_number)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$obj_order_number = $em->getRepository(CustomerOrder::class)->findByOrderNumber($order_number);
		if($obj_order_number){
			return true;
		}else{
			return false;
		}
	}

	public function generateSerial($key_string, $code_length, $code_prefix)
	{
		$ran_number = rand(0, 9999);
		//generate by key_string
		$md5 = strtoupper(md5($key_string.$ran_number));

		//convert to a number
		$num_hash = base_convert($md5, 16, 10);

		if($code_length){
			$membcode = substr($num_hash, 0, $code_length);
		}else{
			//9 digit
			$code = array();
			$code[] = substr($num_hash, 0, 5);
			$code[] = substr($num_hash, 8, 2);
			$code[] = substr($num_hash, 12, 2);
			$membcode = implode ("", $code);
		}

		//add code prefix
		if($code_prefix){
			$membcode = $code_prefix.$membcode;
		}
		return ($membcode);
	}

	public function setSessionOrderSuccess($order, $arr_cart_data)
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$arr_cart_data_summary = $arr_cart_data['summary'];
		$session = $request->getSession();
		$session_cart = $this->getCartSession();
		$session_cart['order_id'] = $order->getId();
		$session_cart['order_number'] = $order->getOrderNumber();
		$session_cart['shipping_cost_by_distance'] = $arr_cart_data_summary['shipping_cost_by_distance'];
		$session->remove('order_success');
		$session->set('order_success', $session_cart);
		$this->removeCartSession();
	}

	public function getSessionOrderSuccess()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();
		$session_order_success = $session->get('order_success');
		return $session_order_success;
	}

	public function validateDiscountSetting()
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$session = $request->getSession();

		$discount_setting = $em->getRepository(DiscountSetting::class)->getOneData();
		$status = $discount_setting->getStatus();
		if(!$status){
			$session_cart = $this->getCartSession();
			if(isset($session_cart['discount_code'])){
				$this->removeSessionDiscountCode();
			}
		}
		return $status;
	}

	public function saveCustomerOrderItemAndUpdateInventory($customer_order, $arr_product, $payment_option)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');

		$customer_order_item = new CustomerOrderItem();
		$customer_order_item->setCustomerOrder($customer_order);

		$product_id = $arr_product['product_id'];
		$sku_id = $arr_product['sku_id'];
		$quantity = $arr_product['quantity'];
		$sku_value = $arr_product['sku'];

		$product = $em->getRepository(Product::class)->find($product_id);
		if($product){
			$customer_order_item->setProduct($product);
			$image = $product->getImage();
			$customer_order_item->setImage($image);
		}

		if($sku_id){
			$variant_option = $arr_product['variant_option'];
			$sku = $em->getRepository(Sku::class)->find($sku_id);
			if($sku){
				$customer_order_item->setSku($sku);
				$customer_order_item->setSkuTitle($variant_option);
				$sku_image = $sku->getImage();
				if($sku_image){
					$customer_order_item->setImage($sku_image);
				}
			}
		}

		$customer_order_item->setProductTitle($arr_product['title']);
		$customer_order_item->setPrice($arr_product['price']);
		$customer_order_item->setQuantity($arr_product['quantity']);
		$customer_order_item->setAmount($arr_product['amount']);
		$customer_order_item->setInventoryPolicyStatus($arr_product['inventory_policy_status']);

		if(isset($arr_product['compare_at_price'])){
			$customer_order_item->setCompareAtPrice($arr_product['compare_at_price']);
		}
		if(isset($arr_product['discount_amount'])){
			$customer_order_item->setDiscount($arr_product['discount_amount']);
		}
		if($sku_value){
			$customer_order_item->setSkuValue($sku_value);
		}

		if($payment_option != $payment_quotation_code){
			//update inventory
			if($sku_id && $sku){
				//get inventory
				$sku_inventory_status = $sku->getInventoryPolicyStatus();
				$sku_inventory_quantity = $sku->getInventoryQuantity();
				if($sku_inventory_status==1){
					$sku_inventory_quantity = $sku_inventory_quantity-$quantity;
					$sku->setInventoryQuantity($sku_inventory_quantity);
				}
			}elseif($product){
				//get inventory
				$product_inventory_status = $product->getInventoryPolicyStatus();
				$product_inventory_quantity = $product->getInventoryQuantity();
				if($product_inventory_status==1){
					$product_inventory_quantity = $product_inventory_quantity-$quantity;
					$product->setInventoryQuantity($product_inventory_quantity);
				}
			}
		}else{
			// RFQ
		}

		$em->persist($customer_order_item);
		$em->flush();

		return $customer_order_item;
	}

	public function saveCustomerOrderDelivery($customer_order, $address_type, $arr_address)
	{
		$em = $this->container->get('doctrine')->getEntityManager();

		$customer_order_delivery = new CustomerOrderDelivery();
		$customer_order_delivery->setCustomerOrder($customer_order);

		$customer_order_delivery->setAddressType($address_type);
		$customer_order_delivery->setFirstName($arr_address['firstName']);
		$customer_order_delivery->setLastName($arr_address['lastName']);
		$customer_order_delivery->setAddress($arr_address['address']);
		$customer_order_delivery->setPhone($arr_address['phone']);
		$customer_order_delivery->setDistrict($arr_address['district']);
		$customer_order_delivery->setProvince($arr_address['province']);
		$customer_order_delivery->setCountry($arr_address['countryCode']['country']);
		$customer_order_delivery->setPostcode($arr_address['postCode']);
		$customer_order_delivery->setTaxPayerId($arr_address['taxPayerId']);
		$customer_order_delivery->setCompanyName($arr_address['companyName']);
		$customer_order_delivery->setHeadOffice($arr_address['headOffice']);

		$customer_order_delivery->setAmphure($arr_address['amphure']);
		$customer_order_delivery->setLatitude($arr_address['latitude']);
		$customer_order_delivery->setLongitude($arr_address['longitude']);
		$customer_order_delivery->setPlaceId($arr_address['placeId']);

		$em->persist($customer_order_delivery);
		$em->flush();

		return $customer_order_delivery;
	}

	public function saveCustomerOrdersDiscounts($customer_order, $discount_code)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$discount = $em->getRepository(Discount::class)->findOneByDiscountCode($discount_code);
		if($discount){
			$customer_order->addDiscounts($discount);
			$em->persist($customer_order);
	    	$em->flush();
		}
	}

	public function saveCustomerPaymentEpayment($customer_order, $arr_cart_data_summary)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$payment_status_awaiting_payment = $this->container->getParameter('payment_status_awaiting_payment');

		$customer_payment_epayment = new CustomerPaymentEpayment();
		$customer_payment_epayment->setCustomerOrder($customer_order);
		$customer_payment_epayment->setAmount($arr_cart_data_summary['total']);
		$customer_payment_epayment->setStatus($payment_status_awaiting_payment);
		$em->persist($customer_payment_epayment);
		$em->flush();

		return $customer_payment_epayment;
	}

	public function setCalculateRouteDistance($session_cart, $arr_delivery_information)
	{
		$origin_showroom = null;
		if(isset($session_cart['direction'])){
			$em = $this->container->get('doctrine')->getEntityManager();
			$origin_showroom_id = $session_cart['direction']['origin_showroom_id'];
			$origin_showroom = $em->getRepository(Showroom::class)->findActiveDataById($origin_showroom_id)->getQuery()->getOneOrNullResult();
			if( !isset($session_cart['shipping_address_id']) || !$origin_showroom ){
				//remove session direction. not found showroom.
				$this->removeSessionDirection();

				//find new direction
				$origin_showroom = $this->setSessionDirection($arr_delivery_information);
			}
		}else{
			//init session direction
			$origin_showroom = $this->setSessionDirection($arr_delivery_information);
		}
		return $origin_showroom;
		// return $this->getCartSession();
	}
	public function setSessionDirection($arr_delivery_information)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$util = $this->container->get('utilities');
		$session_cart = $this->getCartSession();
		$showroom = null;

		if( isset($session_cart['shipping_address_id']) && isset($arr_delivery_information['shipping_address']) ){
			//customer delivery address
			$shipping_address = $arr_delivery_information['shipping_address'];
			$destination_lat = $shipping_address['latitude'];
			$destination_lng = $shipping_address['longitude'];
			$destination_place_id = $shipping_address['placeId'];
			$destination_delivery_address_id = $shipping_address['id'];

			if($destination_lat && $destination_lng){
				//find fastest showroom
				$result = $em->getRepository(Showroom::class)->getFastestDistanceByLatLng($destination_lat, $destination_lng)->setMaxResults(1)->getQuery()->getResult();
				if($result){
					// $direct_distance = $result[0]['distance'];
					$showroom = $result[0][0];
					$origin_showroom_id = $showroom->getId();
					$origin_showroom_lat = $showroom->getLatitude();
					$origin_showroom_lng = $showroom->getLongitude();
					$origin_showroom_place_id = $showroom->getPlaceId();

					if($origin_showroom_lat && $origin_showroom_lng){
						$origin_lat_lng = $origin_showroom_lat.','.$origin_showroom_lng;
						$destination_lat_lng = $destination_lat.','.$destination_lng;
						$arr_distance = $util->getDirectionsBetweenLocations($origin_lat_lng, $destination_lat_lng, $origin_showroom_place_id, $destination_place_id);
						if($arr_distance['value']){
							$arr_direction = array(
								'distance'=> $arr_distance['value'],
								'distance_text'=> $arr_distance['text'],
								'origin_showroom_id'=> $origin_showroom_id,
								'origin_lat_lng'=> $origin_lat_lng,
								'destination_delivery_address_id'=> $destination_delivery_address_id,
								'destination_lat_lng'=> $destination_lat_lng
							);
// print_r($arr_direction);
// exit;
							$session_cart['direction'] = $arr_direction;
							$this->updateCartSession($session_cart);
						}
					}
				}
			}
		}
		return $showroom;
	}
	public function removeSessionDirection()
	{
		$session_cart = $this->getCartSession();
		if(isset($session_cart['direction'])){
			unset($session_cart['direction']);
			$this->updateCartSession($session_cart);
		}
	}

	public function getChildrenProductCategoryByCategoryId($arr_product_search)
	{
		$em = $this->container->get('doctrine')->getEntityManager();
		$cateogry_repo = $em->getRepository(ProductCategory::class);
		$data_cat_id = array();
		if(isset($arr_product_search['productCategories']) && $arr_product_search['productCategories']){
			foreach ($arr_product_search['productCategories'] as $key => $category_id) {
				$arr_cat_id = array();
				$pr_category = $cateogry_repo->find($category_id);
				$children = $cateogry_repo->children($pr_category, false, null, 'asc', true);
				foreach ($children as $chi) {
					$arr_cat_id[] = $chi->getId();
				}
				$data_cat_id = array_merge($data_cat_id, $arr_cat_id);
			}
			$data_cat_id = array_unique($data_cat_id);
		}
		return $data_cat_id;
	}

	public function testRemoveDeliveryAddressData()
	{
		$session_cart = $this->getCartSession();
		unset($session_cart['shipping_address_id']);
		unset($session_cart['billing_address_id']);
		$this->updateCartSession($session_cart);
	}

	public function testAddSku()
	{
		/*
		//set sku data
		$frm_data = $request->request->get('admin_product');
		$sku_id = $frm_data['sku_id'];
		$sku_price = $frm_data['sku_price'];
		$sku_sku = $frm_data['sku_sku'];
		if($sku_id){
			//update sku
			$sku = $em->getRepository(Sku::class)->findOneById($sku_id);
			$sku->setPrice($sku_price);
			$sku->setSku($sku_sku);
		}else{
			//create sku
			$sku = new Sku();
			$sku->setPrice($sku_price);
			$sku->setSku($sku_sku);
			$sku->setProduct($data);
			$em->persist($sku);
	    $em->flush();
		}
		*/
	}

}
