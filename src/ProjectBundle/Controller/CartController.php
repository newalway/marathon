<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\DiscountSetting;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use JMS\SecurityExtraBundle\Annotation\Secure;

use ProjectBundle\Form\Type\Cart\ApplyCouponType;
use ProjectBundle\Form\Type\Product\AddToCartType;

class CartController extends Controller
{
	public function blog_cartAction(Request $request)
	{
		$product_util = $this->container->get('app.product');
		$arr_cart_data = $product_util->getArrProductCartData();

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_cart').':_blog_cart.html.twig', array(
			'arr_cart_data'=>$arr_cart_data,
		));
	}

	/**
	* @Secure(roles="ROLE_CLIENT, ROLE_CUSTOMER")
	*/
	public function indexAction(Request $request)
	{
		$user = $this->getUser();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$discount_setting_status = $product_util->validateDiscountSetting();

		$arr_cart_data = $product_util->getArrProductCartData();

		$session = $request->getSession();
		$form = $this->createForm(ApplyCouponType::class);

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_cart').':index.html.twig', array(
			'form'=>$form->createView(),
			'arr_cart_data'=>$arr_cart_data,
			'discount_setting_status'=>$discount_setting_status,
		));
	}

	public function addItemToCartAction(Request $request)
	{
		$product_util = $this->container->get('app.product');
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
	}

	public function getItemCartAction(Request $request)
	{
		$product_util = $this->container->get('app.product');
		$arr_cart_data = $product_util->getArrProductCartData();

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
		$json_response->setData(array(
			'arr_cart_data' => $arr_cart_data
		));
		return $json_response;
	}

	public function updateItemCartAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$sku_id = $request->query->get('sku_id');
		$quantity = $request->query->get('quantity');
		$product_id = $request->query->get('product_id');

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
	}

	/**
	* @Secure(roles="ROLE_CLIENT, ROLE_CUSTOMER")
	*/
	public function applyDiscountCodeAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

		$arr_cart_data = array();
		$arr_result = array('status'=>false, 'error_message'=>'');

		$form = $this->createForm(ApplyCouponType::class);
		$form->submit($request->request->all());

		if ($form->isSubmitted() && $form->isValid()) {
			$arr_code_data = $request->request->all();
			$discount_code = $arr_code_data['code'];
			// $user_id = $arr_code_data['id']; //not used
			// $email = $arr_code_data['email']; //not used

			$arr_cart_data = $product_util->getArrProductCartData();
			$arr_result = $product_util->validateDiscountCode($arr_code_data, $arr_cart_data);

			if($arr_result['status']){
				//discount code available
				//set session discount code
				$product_util->setSessionDiscountCode($discount_code);

				//get new arr_cart_data
				$arr_cart_data = $product_util->getArrProductCartData();
			}else{
				//error
			}
		}

		// $cart_session = $product_util->getCartSession();

		$json_response = new JsonResponse();
		$json_response->setEncodingOptions(JSON_NUMERIC_CHECK);
	    $json_response->setData(array(
					'arr_cart_data'  => $arr_cart_data,
					'arr_result' => $arr_result,
					'time' => date('Y/m/d H:i:s')
	    ));
		return $json_response;
	}

	/**
	* @Secure(roles="ROLE_CLIENT, ROLE_CUSTOMER")
	*/
	public function removeDiscountCodeAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

		$arr_cart_data = array();
		$status = true;
		$error_msg = '';

		$product_util->removeSessionDiscountCode();

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
	}

}
