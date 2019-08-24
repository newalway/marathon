<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\ShippingCarrier;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerOrderItem;
use ProjectBundle\Entity\TrackingNumber;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ProjectBundle\Form\Type\Product\AddToCartType;
use ProjectBundle\Form\Type\Cart\ApplyCouponType;

use FOS\RestBundle\Controller\Annotations;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use ProjectBundle\Utils\Products;

class ApiTrackingNumberController extends FOSRestController
{
	/**
	* Post tracking number
	*
	* @ApiDoc(
	*  resource=true,
	*  description="Post tracking number",
	*   statusCodes = {
	*     200 = "Returned when successful"
	*   }
	* )
	*
	* @Annotations\View()
	*
	* @param Request	$request	the request object
	*
	* @return array
	*/
	public function postTrackingNumberAction(Request $request)
	{
		$arr_data = $request->request->all();
		$order_id = $arr_data['order_id'];
		$shipping_carrier_id = $arr_data['shipping_carrier_id'];
		$tracking_number = $arr_data['tracking_number'];
		$shipping_confirm_email = filter_var($arr_data['shipping_confirm_email'], FILTER_VALIDATE_BOOLEAN);

		$status = false;
		$arr_tracking_number = array();

		$order = $this->getDoctrine()->getRepository(CustomerOrder::class)->find($order_id);
		$shipping_carrier = $this->getDoctrine()->getRepository(ShippingCarrier::class)->find($shipping_carrier_id);

		$em = $this->getDoctrine()->getManager();
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

		if($order && $shipping_carrier && $tracking_number){
			$data = new TrackingNumber();
			$data->setOrderNumber($order->getOrderNumber());
			$data->setTrackingNumber($tracking_number);
			$data->setCustomerOrder($order);
			$data->setShippingCarrier($shipping_carrier);
			$em->persist($data);
			$em->flush();
			$status = true;

			$arr_tracking_number['id'] = $data->getId();
			$arr_tracking_number['orderNumber'] = $data->getOrderNumber();
			$arr_tracking_number['trackingNumber'] = $tracking_number;
			$arr_tracking_number['shippingCarrierId'] = $shipping_carrier->getId();
			$arr_tracking_number['shippingCarrierName'] = $shipping_carrier->getName();
			$arr_tracking_number['trackingUrl'] = $shipping_carrier->getTrackingUrl();

			//send notification email to customer
			if($shipping_confirm_email){
				$repo_order_item = $this->container->get('doctrine')->getRepository(CustomerOrderItem::class);
				$order_items = $repo_order_item->findByCustomerOrder($order);
				$util->sendTrackingNotificationMail($order, $order_items, $shipping_carrier, $tracking_number);
			}
		}

		$response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData(array(
			'success' => $status,
			'tracking_number' => $arr_tracking_number,
			'shipping_confirm_email'=>$shipping_confirm_email,
            'time' => date('Y/m/d H:i:s')
        ));
        return $response;
	}

	/**
	* Removes tracking number
	*
	* @ApiDoc(
	*   resource = true,
	*   description = "Removestracking number.",
	*   statusCodes={
	*     204="Returned when successful",
	*     404={
	*       "Returned when the user is not found",
	*     }
	*   }
	* )
	*
	* @param Request $request the request object
	* @param int     $id      the user id
	*
	* @return RouteRedirectView
	*
	* @throws NotFoundHttpException when user not exist
	*/
	public function deleteTrackingNumberAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$data = $this->getDoctrine()->getRepository(TrackingNumber::class)->find($id);
		if ($data) {
			// delete data
			$em->remove($data);
			$em->flush();

			return new JsonResponse([
				'success' => true,
				'msg' => 'Tracking number deleted',
				'time' => date('Y/m/d H:i:s')
			]);

		}else{
			return new JsonResponse([
				'success' => false,
				'error_msg' => 'Tracking number doesn\'t exist',
				'time' => date('Y/m/d H:i:s')
			]);
		}
	}

}
