<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerPaymentBankTransfer;
use ProjectBundle\Entity\DeliveryMethod;
use ProjectBundle\Entity\Holiday;

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

class AdminCustomerOrderController extends Controller
{
	// const ROUTER_ORDER_PAYSBUY_PREFIX = '/order/paysbuy-resp-back';
	const ROUTER_CONTROLLER = 'AdminCustomerOrder';
	const ROUTER_ROUTE = 'admin_customer_order_view';

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
		$query = $repository->findCustomerOrderWithoutQuotationJoinedPaymentBankTransfer($arr_query_data, $payment_quotation_code);

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

		try {
			$acctoken = $util->getAccessToken();
		} catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
		}
		$em = $this->getDoctrine()->getManager();
		$orderNumber = $customer_orderObj->getOrderNumber();
		$orders = $em->getRepository(CustomerOrder::class)->findCustomerOrderHasItemByOrderNumber($orderNumber)->getQuery()->getResult();

		if (!$orders) {
			throw $this->createNotFoundException('This data doesn\'t exist');
		}
		$payment_bank_transfer = $em->getRepository(CustomerPaymentBankTransfer::class)->findCustomerPaymentBankTransferByOrder($orders)->getQuery()->getResult();

		$order = $orders[0];
		if($order->getIsRead() == 0){
			$order->setIsRead(1);
			$em->flush();
		}

		//get blackout delivery days
		$arr_non_delivery_date=array();
		$delivery_methods = $em->getRepository(DeliveryMethod::class)->findAll();
		if($delivery_methods){
			foreach ($delivery_methods as $delivery_method) {
				$non_delivery_dates = $delivery_method->getNonDeliveryDate();
				if(!empty($non_delivery_dates)){
					foreach ($non_delivery_dates as $key => $non_delivery_date) {
						$arr_non_delivery_date[$key] = date_create($non_delivery_date)->format('w');
					}
				}
			}
		}
		//get holidays
		$arr_holidays=array();
		$holidays = $em->getRepository(Holiday::class)->findActiveData()->getQuery()->getResult();
		if($holidays){
			foreach ($holidays as $key_holiday => $holiday) {
				$arr_holidays[$key_holiday] = $holiday->getHolidayDate()->format('j-n-Y');
			}
		}

		// 	//get payment
		// 	$payments = $order->getPayments();
		// 	$arr_payment=array();
		// 	if($payments){
		// 		foreach ($payments as $payment) {
		// 			$arr_payment['payment_option'] = $payment->getPaymentOptions();
		// 			$arr_payment['payment_option_title'] = $payment->getPaymentOptionTitle();
		// 		}
		// 	}

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':view.html.twig', array(
			'acctoken' => $acctoken,
			'orders'=>$orders,
			'payment_bank_transfer'=>$payment_bank_transfer,
			'arr_non_delivery_date'=>$arr_non_delivery_date,
			'arr_holidays'=>$arr_holidays,
			// 'arr_payment'=>$arr_payment,
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function order_summary_reportExcelAction(request $request)
	{
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');
		$em = $this->getDoctrine()->getManager();
		$arr_query_data = $this->prepare_query_data($request);
		$orders = $em->getRepository(CustomerOrder::class)->findCustomerOrderWithoutQuotation($arr_query_data, $payment_quotation_code)->getQuery()->getResult();
		$export_excel = $this->container->get('exportexcel');
		$export_excel->getHeaderExcelOrder();
		$export_excel->setDataExcelOrder($orders);
		$name_post_fix = date('dMy-His');
		$file_name = 'Order-Summary-'.$name_post_fix.'.xlsx';
		$export_excel->saveOutputExcel($file_name);
		$response =  $export_excel->streamOutputExcel($file_name);

		return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function updateStatusAction(CustomerOrder $customer_orderObj,Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();

		try {
			$acctoken = $util->getAccessToken();
		} catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
		}
		$em = $this->getDoctrine()->getManager();
		$orderNumber = $customer_orderObj->getOrderNumber();
		$orderId = $customer_orderObj->getId();
		$orders = $em->getRepository(CustomerOrder::class)->findCustomerOrderHasItemByOrderNumber($orderNumber)->getQuery()->getResult();

		$req_status = $request->get('status');

		$payment_status_paid = $this->container->getParameter('payment_status_paid');
		$payment_status_shipped = $this->container->getParameter('payment_status_shipped');
		$payment_status_cancelled = $this->container->getParameter('payment_status_cancelled');
		$payment_status_refunded = $this->container->getParameter('payment_status_refunded');
		$payment_bank_transfer_code = $this->container->getParameter('payment_bank_transfer_code');
		$payment_cash_on_deliveryr_code = $this->container->getParameter('payment_cash_on_deliveryr_code');

		if (!$orders) {
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		//$date_now = date('Y-m-d H:i:s');
		if ($req_status == $payment_status_paid){
			$customer_orderObj->setPaid(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'paid status update success');

		}elseif($req_status == $payment_status_shipped){
			$customer_orderObj->setFulfilled(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'shipped status update success');

		}elseif($req_status == $payment_status_cancelled){
			$customer_orderObj->setCancelled(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'cancelled update success');

		}elseif($req_status == $payment_status_refunded){
			$customer_orderObj->setCancelled(1);
			$customer_orderObj->setRefunded(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'refunded update success');

		}elseif($req_status=="soft_deleted"){
			$customer_orderObj->setDeleted(1);
			$em->flush($customer_orderObj);
			$this->get('session')->getFlashBag()->add('notice', 'deleted update success');

			return $this->redirectToRoute('admin_customer_order_list');
		}

		return $this->redirectToRoute('admin_customer_order_view', array('id'=>$orderId));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function customer_order_payment_downloadAction(CustomerPaymentBankTransfer $customer_bank_transfer,Request $request)
	{
		// You only need to provide the path to your static file
		// $filepath = 'path/to/TextFile.txt';

		// i.e Sending a file from the resources folder in /web
		// in this example, the TextFile.txt needs to exist in the server
		$publicResourcesFolderPath = $this->container->getParameter('files_upload_bank_transfer');
		$filename = $customer_bank_transfer->getAttachFile();


		// This should return the file to the browser as response
		$response = new BinaryFileResponse($publicResourcesFolderPath.$filename);

		// To generate a file download, you need the mimetype of the file
		$mimeTypeGuesser = new FileinfoMimeTypeGuesser();

		// Set the mimetype with the guesser or manually
		if($mimeTypeGuesser->isSupported()){
			// Guess the mimetype of the file according to the extension of the file
			$response->headers->set('Content-Type', $mimeTypeGuesser->guess($publicResourcesFolderPath.$filename));
		}else{
			// Set the mimetype of the file manually, in this case for a text file is text/plain
			$response->headers->set('Content-Type', 'text/plain');
		}

		// Set content disposition inline of the file
		$response->setContentDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,$filename
		);

		return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function _getunreadAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$payment_quotation_code = $this->container->getParameter('payment_quotation_code');
		$count = $em->getRepository(CustomerOrder::class)->countUnreadCustomerOrderWithoutQuotation($payment_quotation_code)
			->select('count(o.id)')
			->getQuery()
			->getSingleScalarResult();

		$count_text='';
		if($count>0){
			$count_text='<small class="label pull-right bg-yellow">'.$count.'</small>';
	    }
    	return new Response($count_text);
	}


	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function group_export_excel_summary_reportAction(request $request)
	{
		// $payment_cod_code = $this->container->getParameter('payment_cash_on_deliveryr_code');
		// $arr_query_data = $this->prepare_query_data($request);
		// $orders = OrderQuery::create()->getAllDataWithDeliveryAddressGuest($arr_query_data)->find();
		//
		// $export_excel = $this->container->get('exportexcel');
		// $export_excel->getExcelObjectOrderSummary();
		// $export_excel->exportOrderSummaryData($orders, $payment_cod_code);
		// $response = $export_excel->getExcelResponse();
		//
		// $name_post_fix = date('dMy-His');
		// $file_name = 'Order-Summary-'.$name_post_fix.'.xls';
		//
		// //$source_path = $this->container->getParameter('data_private_absolute_paths');
		// //$source_file = $export_excel->saveExcelFile($source_path, $file_name);
		//
		// //headers
		// $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		// $response->headers->set('Content-Disposition', 'attachment;filename="'.$file_name.'"');
		// $response->headers->set('Pragma', 'public');
		// $response->headers->set('Cache-Control', 'maxage=1');
		// return $response;
	}


	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function order_items_summary_reportExcelAction(CustomerOrder $customer_orderObj,request $request){

		// $em = $this->getDoctrine()->getManager();
		// $orderNumber = $customer_orderObj->getOrderNumber();
		// $orders = $em->getRepository(CustomerOrder::class)->findCustomerOrderHasItemByOrderNumber($orderNumber)->getQuery()->getResult();
		//
		// if (!$orders) {
		// 	throw $this->createNotFoundException('This data doesn\'t exist');
		// }
		// $payment_bank_transfer = $em->getRepository(CustomerPaymentBankTransfer::class)->findCustomerPaymentBankTransferByOrder($orders)->getQuery()->getResult();
		//
		// $export_excel = $this->container->get('exportexcel');
		// $export_excel->getHeaderExcelOrder();
		// $export_excel->getHeaderExcelOrderItems();
		// $export_excel->setDataExcelOrderAndItems($orders,$payment_bank_transfer);
		// $name_post_fix = date('dMy-His');
		// $file_name = 'Order-Summary-'.$name_post_fix.'.xlsx';
		// $export_excel->saveOutputExcel($file_name);
		// $response =  $export_excel->streamOutputExcel($file_name);
		//
		// return $response;

	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function group_export_excelAction(request $request)
	{
		//  $utils = $this->container->get('utils');
		//  $utils->slugify('test');

		// $arr_query_data = $this->prepare_query_data($request);
		// $orders = OrderQuery::create()->getAllDataWithDeliveryAddressGuest($arr_query_data)->find();
		//
		// $index=0;
		// $phpExcelObject = $this->getExcelObjectOrder($index);
		// $phpExcelObject = $this->exportOrderData($phpExcelObject, $index, $orders);
		//
		// $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
		// $response = $this->get('phpexcel')->createStreamedResponse($writer);
		//
		// $name_post_fix = date('dMy');
		// $file_name = 'Order '.$name_post_fix.'.xls';
		//
		// //headers
		// $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		// $response->headers->set('Content-Disposition', 'attachment;filename="'.$file_name.'"');
		// $response->headers->set('Pragma', 'public');
		// $response->headers->set('Cache-Control', 'maxage=1');
		// return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function view_order_numberAction(Request $request, $order_number)
	{
		// 	$order = OrderQuery::create()->getActiveOrder()->filterByOrderNumber($order_number)->findOne();
		// 	if(!$order){
		// 		throw $this->createNotFoundException('This data doesn\'t exist');
		// 	}
		// 	$response = $this->forward('ProjectBundle:AdminOrder:view', array(
		//     'id'  => $order->getId()
		// ));
		// 	return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function pdfAction(Request $request)
	{
		// 	$id = $request->get('id');
		// 	$order = OrderQuery::create()->getActiveOrder()->filterById($id)->findOne();
		// if (!$order) {
		//   throw $this->createNotFoundException('This data doesn\'t exist');
		// }
		//
		// $snappy = $this->get('knp_snappy.pdf');
		// $filename = 'order-'.$order->getOrderNumber().'-'.date('YmdHis').'.pdf';
		//
		// $html = $this->renderView('ProjectBundle:AdminOrder:pdf.html.twig', array('order'=>$order));
		//
		// return new Response(
		//     $snappy->getOutputFromHtml($html),
		// 			200,
		//     array(
		//       'Content-Type'          => 'application/pdf',
		//       'Content-Disposition'   => 'inline; filename="'.$filename.'"'
		//     )
		// );
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function cover_pdfAction(Request $request)
	{
		// 	$id = $request->get('id');
		// 	$order = OrderQuery::create()->getActiveOrder()->filterById($id)->findOne();
		// if (!$order) {
		//   throw $this->createNotFoundException('This data doesn\'t exist');
		// }
		//
		// $snappy = $this->get('knp_snappy.pdf');
		// $filename = 'cover-'.$order->getOrderNumber().'-'.date('YmdHis').'.pdf';
		//
		// $html = $this->renderView('ProjectBundle:AdminOrder:cover_pdf.html.twig', array('order'=>$order));
		//
		// return new Response(
		//     $snappy->getOutputFromHtml($html,array('orientation'=>'Landscape','default-header'=>false)),
		// 			200,
		//     array(
		//       'Content-Type'          => 'application/pdf',
		//       'Content-Disposition'   => 'inline; filename="'.$filename.'"'
		//     )
		// );
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function payment_statusAction(Request $request)
	{
		// 	$this->setCkAuthorized();
		// 	$id = $request->get('id');
		// 	$order = OrderQuery::create()->getActiveOrder()->filterById($id)->findOne();
		//
		// if (!$order) { throw $this->createNotFoundException('This data doesn\'t exist'); }
		//
		// 	$req_status = $request->get('status');
		// 	$payment_id = $request->get('payment_id');
		//
		// 	$date_now = date('Y-m-d H:i:s');
		//
		// 	$payment = PaymentQuery::create()->findPk($payment_id);
		//
		// 	$payment_status_paid = $this->container->getParameter('payment_status_paid');
		// 	$payment_status_shipped = $this->container->getParameter('payment_status_shipped');
		// 	$payment_status_cancelled = $this->container->getParameter('payment_status_cancelled');
		// 	$payment_status_refunded = $this->container->getParameter('payment_status_refunded');
		// 	$payment_bank_transfer_code = $this->container->getParameter('payment_bank_transfer_code');
		// 	$payment_cash_on_deliveryr_code = $this->container->getParameter('payment_cash_on_deliveryr_code');
		//
		// 	if($req_status == 'Paid'){
		// 		$order->setPaid(1);
		// 		$order->setPaymentStatus($payment_status_paid);
		// 		$order->setPaymentDate($date_now);
		// 		$order->save();
		// 		if($payment){
		// 			$payment->setPaid(1);
		// 			$payment->setStatus($payment_status_paid);
		// 			$payment->save();
		// 		}
		//
		// 		//product inventory move to sold
		// 		ApplicationController::moveProductInventoryToSold($order->getId());
		//
		// 	}elseif($req_status == 'Shipped'){
		// 		$order->setFulfilled(1);
		// 		$order->setFulfillmentStatus($payment_status_shipped);
		// 		$order->save();
		//
		// 	}elseif($req_status == 'Cancelled'){
		// 		$order->setCancelled(1);
		// 		$order->setPaymentStatus($payment_status_cancelled);
		// 		$order->save();
		// 		$cancel_payments = $order->getPayments();
		// 		if($cancel_payments){
		// 			foreach ($cancel_payments as $cancel_payment) {
		// 				//payment cancel only bank transfer
		// 				switch ($cancel_payment->getPaymentOptions()) {
		// 			    case $payment_bank_transfer_code:
		// 					case $payment_cash_on_deliveryr_code:
		// 						if($cancel_payment->getPaid() ==  1){
		// 							$cancel_payment->setCancelled(1);
		// 							$cancel_payment->setRefunded(1);
		// 						}else{
		// 							$cancel_payment->setCancelled(1);
		// 						}
		// 						$cancel_payment->save();
		// 					break;
		// 				}
		//
		// 			}//end foreach
		// 		}
		// 		//product sold move to inventory
		// 		ApplicationController::moveProductSoldToInventory($order->getId());
		// 	}
		//
		// return $this->redirectToRoute('admin_order_view', array('id'=>$id));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		// $data = OrderQuery::create()->findPk($request->get('id'));
		// if (!$data) {
		// 	throw $this->createNotFoundException('This data doesn\'t exist');
		// }
		//
		// //soft delete
		// $data->setDeleted(1);
		// $data->save();
		//
		// //hard delete
		// //$data->delete();
		//
		// $this->get('session')->getFlashBag()->add('notice', 'Data deleted');
		// return $this->redirect($this->getBackToUrl('admin_order'));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function paysbuy_recheck_payment_dataAction(Request $request)
	{
		// $payment = PaymentQuery::create()->findPk($request->get('payment_id'));
		// if($payment){
		// 	$order = $payment->getOrder();
		// 	$order_id = $payment->getOrderId();
		// 	$order_number = $order->getOrderNumber();
		//
		// 	$res = ApplicationController::paysbuyGetTransactionByInvoice($order_number);
		// }else{
		// 	$res = false;
		// }
		//
		// $response = new JsonResponse();
		// $response->setEncodingOptions(JSON_NUMERIC_CHECK);
		// $response->setData(array(
		// 		$res,
		// ));
		// return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function paysbuy_update_payment_dataAction(Request $request)
	{
		// $payment = PaymentQuery::create()->findPk($request->get('payment_id'));
		// if($payment){
		// 	$order = $payment->getOrder();
		// 	$order_id = $payment->getOrderId();
		// 	$order_number = $order->getOrderNumber();
		//
		// 	$res = ApplicationController::paysbuyGetTransactionByInvoice($order_number);
		//
		// 	//change format result to {result}{Invoice}
		// 	$res['result'] .= $res['Invoice'];
		//
		// 	$response = $this->call_form_params(
		//     'POST',
		//     self::ROUTER_ORDER_PAYSBUY_PREFIX,
		//     $res
		// );
		// 	$status = json_decode($response->getBody());
		//
		// 	if($status->status){
		// 		$this->get('session')->getFlashBag()->add('notice', 'Updated payment data successfully');
		// 	}
		//
		// }
		// return $this->redirectToRoute('admin_order_view', array('id'=>$order_id));
	}

}
