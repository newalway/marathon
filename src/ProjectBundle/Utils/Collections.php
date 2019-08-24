<?php

namespace ProjectBundle\Utils;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

use Exception;
use GuzzleHttp\Client;

class Collections
{
	private $kernel;
	private $mailer;
	private $router;

	public function __construct($kernel, \Swift_Mailer $mailer, Router $router)
	{
		$this->container = $kernel->getContainer();
		$this->mailer = $mailer;
		$this->router = $router;
	}

	public static function wordStatus($status)
	{
		switch($status){
			case 1:
				$return = '<span class="label label-success">Published</span>';
				break;
			default:
				$return = '<span class="label label-warning">Unpublished</span>';
		}
		return $return;
	}

	public static function wordStatusAvailable($product)
	{
		$status = $product->getStatus();
		$now = new \DateTime();

		switch($status){
			case 1:
				if($now >= $product->getPublishDate()){
					$return = '';
				}else{
					$return = '<br/><div><small style="color:#777;" class="text-secondary">Scheduled - publish on '.$product->getPublishDate()->format('d/m/Y  H:i').' </small></div>';
				}
				// $return = '<span class="label label-success">Available</span>';
				break;
			default:
				$return = '<br/><div><small style="color:#777;" class="text-secondary">Unavailable</small></div>';
				// $return = '<span class="label label-default">Unavailable</span>';
		}
		return $return;
	}

	public static function isDiscountCodeActive($discount)
	{
		$now = new \DateTime();
		$isEndDate = $discount->getIsEndDate();
		$endDate = $discount->getEndDate();
		$startDate = $discount->getStartDate();

		$status_code = '';
		if($isEndDate==1){
			if($endDate<$now){
				$status_code = 'expired';
			}else{
				if($startDate<$now){
					$status_code = 'active';
				}else{
					$status_code = 'scheduled';
				}
			}
		}else{
			if($startDate<$now){
				$status_code = 'active';
			}else{
				$status_code = 'scheduled';
			}
		}
		return $status_code;
	}

	public static function couponStatusText($discount)
	{
		$status_code = Collections::isDiscountCodeActive($discount);
		switch($status_code){
			case 'active':
				$return = '<span class="label label-success">Active</span>';
				break;
			case 'scheduled':
				$return = '<span class="label label-warning">Scheduled</span>';
				break;
			case 'expired':
				$return = '<span class="label label-default">Expired</span>';
				break;
			default:
				$return = '';
		}
		return $return;
	}

	public static function wordGender($gender)
	{
		switch($gender){
			case 'F':
				$return_gender = 'Female';
				break;
			case 'M':
				$return_gender = 'Male';
				break;
			default:
				$return_gender = '';
		}
		return $return_gender;
	}

	public function getCustomerPlan($member)
	{
		$user_roles = $member->getRoles();
		if((in_array("ROLE_CLIENT",$user_roles))){
			$plan = "Client";
		}elseif(in_array("ROLE_CUSTOMER",$user_roles)){
			$plan = "Customer";
		}
		return $plan;
	}
	public static function wordPaymentStatus($status)
	{
		$return_status = $status;
		switch($status){
			case 'Awaiting payment':
				$return_status = '<span class="label label-default">Awaiting payment</span>';
				break;
			case 'Waiting':
				$return_status = '<span class="label label-default">Waiting</span>';
				break;
			case 'Paid':
				$return_status = '<span class="label label-success">Paid</span>';
				break;
			case 'Cancelled':
				$return_status = '<span class="label label-danger">Cancelled</span>';
				break;
			case 'Refunded':
				$return_status = '<span class="text-muted">Refunded</span>';
				break;
			case 'Shipped':
				$return_status = '<span class="label label-success">Shipped</span>';
				break;
			case 'Unfulfilled':
				$return_status = '<span class="label label-warning">Unfulfilled</span>';
				break;
			case 'Processing':
				$return_status = '<span class="label label-warning">Processing</span>';
				break;
			case 'successful':
				$return_status = '<span class="label label-success">Successful</span>';
				break;
			case 'failed':
				$return_status = '<span class="label label-dange">Failed</span>';
				break;
			case 'Unpaid':
				$return_status = '<span class="label label-default">Unpaid</span>';
				break;
			case 'Pending':
				$return_status = '<span class="label label-default">Pending</span>';
				break;
			default:
				$return_status = $status;
		}
		return $return_status;
  	}

	public function getTrackingURL($tracking_url, $tracking_number)
	{
		$strTemplate = $tracking_url;
		$strParams = [
			'#ID#' => $tracking_number,
		];
		$return_uri = strtr($strTemplate, $strParams);
		return $return_uri;
	}
}
