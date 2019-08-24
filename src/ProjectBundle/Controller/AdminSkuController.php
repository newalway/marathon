<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Sku;
use ProjectBundle\Entity\SkuValue;
use ProjectBundle\Entity\Variant;
use ProjectBundle\Entity\VariantOption;

use ProjectBundle\Form\Type\AdminSkuType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminSkuController extends Controller
{
	const ROUTER_INDEX = 'admin_sku';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
	const ROUTER_CONTROLLER = 'AdminSku';

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function editAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();
		$id = $request->get('id');

		$data = $this->getDoctrine()->getRepository(Sku::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$product = $data->getProduct();
		// $skus = $product->getSkus();

		//get arr sku_variant_option
		$sku_variant_option = $product_util->getArrSkuVariantOption($data);
		$sku_key_name = strtolower(implode("-", $sku_variant_option));

		//get sku_variant
		$arr_sku_variant_data = $product_util->getArrSkuVariantData($product);
		$view_variant_data = $arr_sku_variant_data['view_variant_data'];

		//get arr variant_option_data
		$arr_variant_option_data = $product_util->getArrVariantOptionData($product);
		$variant_option_data = $arr_variant_option_data['variant_option_data'];

		$form = $this->createForm(AdminSkuType::class, $data, array('allow_extra_fields'=>true));

    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken,
			'product'=>$product,
			'sku_variant_option'=>$sku_variant_option,
			'sku_key_name'=>$sku_key_name,
			'view_variant_data'=>$view_variant_data,
			'variant_option_data'=>$variant_option_data
		));
  	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function updateAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

    	$em = $this->getDoctrine()->getManager();
		$acctoken = $util->getAccessToken();

		$data = $this->getDoctrine()->getRepository(Sku::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$product = $data->getProduct();
		$ori_image = $data->getImage();
		// $skus = $product->getSkus();

		//get arr sku_variant_option
		$sku_variant_option = $product_util->getArrSkuVariantOption($data);
		$sku_key_name = strtolower(implode("-", $sku_variant_option));

		//get sku_variant
		$arr_sku_variant_data = $product_util->getArrSkuVariantData($product);
		$view_variant_data = $arr_sku_variant_data['view_variant_data'];

		//get arr variant_option_data
		$arr_variant_option_data = $product_util->getArrVariantOptionData($product);
		$variant_option_data = $arr_variant_option_data['variant_option_data'];

	    $form = $this->createForm(AdminSkuType::class, $data, array('allow_extra_fields'=>true));
	    $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid())
		{
			//sku data
			$frm_sku = $request->request->get('admin_sku');

			//reset default_option
			if (array_key_exists('defaultOption', $frm_sku)) {
				// reset default_option
				$em->getRepository(Sku::class)->setClearDefaultOptionValue($product);
			}

			//remove image
			if($request->get('removefileimage')==1){
				$data->removeImage();
			}

			$em->flush();

			//save sku image size s,m,l
			$product_util->saveSkuImageSize($data, $ori_image);

			//weight grams
			$product_util->saveSkuWeightGrams($data, $frm_sku);

			//save product inventory status
			$product_util->skuSaveProductInventoryStatus($product);

			$util->setUpdateNotice();
			$redirect_uri = $this->generateUrl(self::ROUTER_EDIT, array('id'=>$data->getId()));
			return $this->redirect($redirect_uri);
    	}
    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken,
			'product'=>$product,
			'sku_variant_option'=>$sku_variant_option,
			'sku_key_name'=>$sku_key_name,
			'view_variant_data'=>$view_variant_data,
			'variant_option_data'=>$variant_option_data
		));
  	}
}
