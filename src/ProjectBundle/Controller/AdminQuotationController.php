<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerPaymentBankTransfer;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminQuotationController extends Controller
{
	const ROUTER_CONTROLLER = 'AdminQuotation';
	const ROUTER_ROUTE = 'admin_quotation';

	protected function prepare_query_data($request){
		if($request->query->get('search_year')){
			$arr_query_data['date_type'] = preg_replace('/\D/', '', $request->query->get('date_type'));
		}else{
			$arr_query_data['date_type'] = 1;
		}

		if($request->query->get('search_type')){
			$arr_query_data['search_type'] = preg_replace('/\D/', '', $request->query->get('search_type'));
		}else{
			$arr_query_data['search_type'] = 1;
		}
		if($request->query->get('search_year')){
			$arr_query_data['search_year'] = preg_replace('/\D/', '', $request->query->get('search_year'));
		}else{
			$arr_query_data['search_year'] = 0;
		}
		$arr_query_data['search_month'] = preg_replace('/\D/', '', $request->query->get('search_month'));
		$arr_query_data['search_start_date'] = $request->query->get('search_start_date');
		$arr_query_data['search_end_date'] = $request->query->get('search_end_date');
		$arr_query_data['search_status'] = ($request->query->get('search_status')) ? preg_replace('/\D/', '', $request->query->get('search_status')) : 2;

		$arr_query_data['q'] = trim($request->query->get('q'));
		return $arr_query_data;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');

		try {
			$acctoken = $util->getAccessToken();
		} catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
		}

		$arr_query_data = $this->prepare_query_data($request);
		$repository = $this->getDoctrine()->getRepository(CustomerOrder::class);
		$query = $repository->findRequestForQuotation($arr_query_data, $payment_quotation_code);

		$paginated = $util->setPaginatedOnPagerfanta($query);
		$util->setBackToUrl();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'acctoken' => $acctoken,
			'paginated' =>$paginated,
			'arr_query_data'=>$arr_query_data
		));
	}


	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function viewAction(CustomerOrder $customer_orderObj,Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');

		try {
			$acctoken = $util->getAccessToken();
		} catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
		}
		$em = $this->getDoctrine()->getManager();
		$orderNumber = $customer_orderObj->getOrderNumber();
		$orders = $em->getRepository(CustomerOrder::class)->findRequestForQuotationByOrderNumber($orderNumber, $payment_quotation_code)->getQuery()->getResult();

		if (!$orders) {
			throw $this->createNotFoundException('This data doesn\'t exist');
		}
		$payment_bank_transfer = $em->getRepository(CustomerPaymentBankTransfer::class)->findCustomerPaymentBankTransferByOrder($orders)->getQuery()->getResult();

		$order = $orders[0];
		if($order->getIsRead() == 0){
			$order->setIsRead(1);
			$em->flush();
		}

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':view.html.twig', array(
			'acctoken' => $acctoken,
			'orders'=>$orders,
			'payment_bank_transfer'=>$payment_bank_transfer
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function updateStatusAction(CustomerOrder $customer_orderObj,Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');

		try {
			$acctoken = $util->getAccessToken();
		} catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
		}
		$em = $this->getDoctrine()->getManager();
		$orderNumber = $customer_orderObj->getOrderNumber();
		$orderId = $customer_orderObj->getId();
		$orders = $em->getRepository(CustomerOrder::class)->findRequestForQuotationByOrderNumber($orderNumber, $payment_quotation_code)->getQuery()->getResult();

		$req_status = $request->get('status');

		if (!$orders) {
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		//$date_now = date('Y-m-d H:i:s');
		if($req_status=="soft_deleted"){
			$customer_orderObj->setDeleted(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'deleted update success');
		}

		return $this->redirectToRoute('admin_quotation');
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function _getunreadAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');
		$count = $em->getRepository(CustomerOrder::class)->countUnreadQuotation($payment_quotation_code)
			->select('count(o.id)')
			->getQuery()
			->getSingleScalarResult();

		$count_text='';
		if($count>0){
			$count_text='<small class="label pull-right bg-yellow">'.$count.'</small>';
	    }
    	return new Response($count_text);
	}
}
