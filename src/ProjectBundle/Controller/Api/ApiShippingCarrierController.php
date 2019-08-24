<?php

namespace ProjectBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\ShippingCarrier;
use ProjectBundle\Entity\CustomerOrder;
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

class ApiShippingCarrierController extends FOSRestController
{
	/**
	* List all data.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List all data",
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
	public function getShippingCarriersAction(Request $request)
	{
		$arr_data = $this->getDoctrine()->getRepository(ShippingCarrier::class)->findAllData()
			->getQuery()
			->getArrayResult();

        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData(array(
            'shipping_carriers'  => $arr_data,
            'time' => date('Y/m/d H:i:s')
        ));
        return $response;
	}

	/**
	* List all data.
	*
	* @ApiDoc(
	*  resource=true,
	*  description="List all shipping and tracking_number",
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
	public function getShippingCarriersAndTrackingNumbersAction(Request $request)
	{
		$order_id = $request->get('order_id');
		$arr_data = $this->getDoctrine()->getRepository(ShippingCarrier::class)->findSelectAllData()->getQuery()->getArrayResult();

		$arr_tracking_numbers = array();
		$order = $this->getDoctrine()->getRepository(CustomerOrder::class)->find($order_id);
		if($order){
			$arr_tracking_numbers = $this->getDoctrine()->getRepository(TrackingNumber::class)->findSelectDataByOrder($order)->getQuery()->getArrayResult();
		}

        $response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData(array(
			'shipping_carriers'  => $arr_data,
			'tracking_numbers' => $arr_tracking_numbers,
            'time' => date('Y/m/d H:i:s')
        ));
        return $response;
	}

	/**
     * Update delivery date.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update delivery date.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the timestamp is not found"
     *   }
     * )
     *
     *
     * @param Request $request the request object
     * @param int     $id      the timestamp id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws NotFoundHttpException when user not exist
     */
	public function putDeliveryDateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$isSuccess = false;
		$ship_date = '';

		$delivery_date_str = $request->request->get('delivery_date');
		$order_id = $request->request->get('order_id');
		if($order_id && $delivery_date_str){
			$customer_order = $em->getRepository(CustomerOrder::class)->find($order_id);
			if($customer_order){
				$delivery_date_str = str_replace('/', '-', $delivery_date_str);
				$delivery_date = new \DateTime($delivery_date_str);
				// $update_date = $delivery_date->format('Y-m-d');
				$ship_date = $delivery_date->format('d/m/Y');
				//save shipdate
				$customer_order->setShipDate($delivery_date);
				$em->flush();
				$isSuccess=true;
			}
		}

		$response = new JsonResponse();
        $response->setEncodingOptions(JSON_NUMERIC_CHECK);
        $response->setData(array(
			'success' => $isSuccess,
			'ship_date'=>$ship_date,
			'time' => date('Y/m/d H:i:s')
        ));
        return $response;
	}
}
