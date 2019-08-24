<?php

namespace ProjectBundle\Utils;

use ProjectBundle\Entity\Authentication;
use ProjectBundle\Entity\AccessToken;
use ProjectBundle\Entity\RefreshToken;
use ProjectBundle\Entity\SettingOption;
use ProjectBundle\Entity\CustomerPaymentEpayment;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\BankAccount;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
use phpbrowscap\Browscap;
use GeoIp2\Database\Reader;

class Cybersource
{
	private $kernel;
	private $factory;
	private $mailer;
	private $router;

	private $hmac_sha265;
	private $live_mode;
	private $payment_data;

	public function __construct($kernel, $factory, \Swift_Mailer $mailer, Router $router)
	{
		$this->container = $kernel->getContainer();
		$this->factory = $factory;
		$this->mailer = $mailer;
		$this->router = $router;

		$this->hmac_sha265 = 'sha256';
		$this->live_mode = $this->getLiveMode();
		$this->payment_data = array();
	}

	public function sign($params) {
		$secret_key = $this->getSecretKey();
		return $this->signData($this->buildDataToSign($params), $secret_key);
	}

	public function signData($data, $secretKey) {
	    return base64_encode(hash_hmac($this->hmac_sha265, $data, $secretKey, true));
	}

	public function buildDataToSign($params) {
        $signedFieldNames = explode(",", $params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return $this->commaSeparate($dataToSign);
	}

	public function commaSeparate($dataToSign) {
	    return implode(",",$dataToSign);
	}

	public function getSecretKey()
	{
		$util = $this->container->get('utilities');
		return $util->getAuthenticationValue('cybersource_secret_key');
	}

	public function getAccessKey()
	{
		$util = $this->container->get('utilities');
		return $util->getAuthenticationValue('cybersource_access_key');
	}

	public function getProfileId()
	{
		$util = $this->container->get('utilities');
		return $util->getAuthenticationValue('cybersource_profile_id');
	}

	public function getLiveMode()
	{
		$util = $this->container->get('utilities');
		return filter_var($util->getAuthenticationValue('cybersource_profile_livemode'), FILTER_VALIDATE_BOOLEAN);
	}

	public function getProcessTransactionEndpoint()
	{
		if($this->live_mode){
			$transactions = 'https://secureacceptance.cybersource.com/pay';
		}else{
			$transactions = 'https://testsecureacceptance.cybersource.com/pay';
		}
		return $transactions;
	}


	// -------- Request Payment --------- //
	public function setPaymentData($customerOrder, $arr_cart_data)
	{
		$this->setInitPaymentData($customerOrder, $arr_cart_data);
		$this->addSignedFieldNames();
		$this->addSignatureFieldNames();

		return $this->payment_data;
	}

	public function setInitPaymentData($customerOrder, $arr_cart_data)
	{
		$user = $customerOrder->getUser();
		$email = $user->getEmail();

		$access_key = $this->getAccessKey();
		$profile_id = $this->getProfileId();
		$order_number = $customerOrder->getOrderNumber();
		$local_codes = $this->getLocaleField();

		$shipping_address = $arr_cart_data['delivery_information']['shipping_address'];
		$billing_address = $arr_cart_data['delivery_information']['billing_address'];
		$address_country = 'TH';

		$products = $arr_cart_data['products'];
		$line_item_count = count($products);

		$amount = number_format($customerOrder->getTotalPrice(), 2, '.', '');

		$this->payment_data = array(
			'access_key'=> $access_key,
			'profile_id'=> $profile_id,
			'transaction_uuid'=> $order_number.$access_key,
			'unsigned_field_names'=>'',

			'signed_date_time'=> gmdate("Y-m-d\TH:i:s\Z"),
			'locale'=> $local_codes,
			'transaction_type'=> 'sale',
			'reference_number'=> $order_number,

			'ship_to_forename'=> $shipping_address['firstName'],
			'ship_to_surname'=> $shipping_address['lastName'],
			'ship_to_address_line1'=> $shipping_address['address'].' '.$shipping_address['district'],
			'ship_to_address_city'=> $shipping_address['province'],
			'ship_to_address_country'=> $address_country,
			'ship_to_address_postal_code'=> $shipping_address['postCode'],
			'ship_to_phone'=> $shipping_address['phone'],

			'bill_to_forename'=> $billing_address['firstName'],
			'bill_to_surname'=> $billing_address['lastName'],
			'bill_to_email'=> $email,
			'bill_to_address_line1'=> $billing_address['address'].' '.$billing_address['district'],
			'bill_to_address_city'=> $billing_address['province'],
			'bill_to_phone'=> $billing_address['phone'],
			'bill_to_address_country'=> $address_country,
			'bill_to_address_postal_code'=> $billing_address['postCode'],

			'line_item_count'=> $line_item_count,

			'amount'=> $amount,
			'currency'=> 'THB',
		);

		foreach ($products as $key => $product) {
			$key_field = 'item_'.$key.'_';
			$this->payment_data[$key_field.'sku'] = $product['sku'];
			$this->payment_data[$key_field.'code'] = 'default';
			$this->payment_data[$key_field.'name'] = $product['title'];
			$this->payment_data[$key_field.'quantity'] = $product['quantity'];
			$this->payment_data[$key_field.'unit_price'] = number_format($product['price'], 2, '.', '');
		}

	}

	public function addSignedFieldNames()
	{
		$arr_field_names = array('signed_field_names');
		foreach ($this->payment_data as $key => $value) {
			$arr_field_names[] = $key;
		}
		$signed_field_names = $this->commaSeparate($arr_field_names);
		$this->payment_data['signed_field_names'] = $signed_field_names;
	}

	public function addSignatureFieldNames()
	{
		$signature = $this->sign($this->payment_data);
		$this->payment_data['signature'] = $signature;
	}

	public function getLocaleField()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$locale = $request->getLocale();
		switch ($locale) {
		    case 'th':
		        $local_codes = 'th-th';
		        break;
		    default:
		       $local_codes = 'en-us';
		}
		return $local_codes;
	}

	public function setCybersourceCancel()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		$order_number = $request->get('req_reference_number');

		$em = $this->container->get('doctrine')->getEntityManager();
		$customerOrder = $em->getRepository(CustomerOrder::Class)->getDataByOrderNumberAndUser($order_number, $user)->getQuery()->getOneOrNullResult();
		$customerPaymentEpayment = $customerOrder->getCustomerPaymentEpayment();
		if($customerOrder && $customerPaymentEpayment){

			$decision = $request->get('decision');
			$message = $request->get('message');
			$customerPaymentEpayment->setDecision($decision);
			$customerPaymentEpayment->setMessage($message);

			$payment_status_cancelled = $this->container->getParameter('payment_status_cancelled');
			$customerPaymentEpayment->setStatus($payment_status_cancelled);

			$customerOrder->setCancelled(1);

			$em->flush();

			return $customerPaymentEpayment;
		}else{
			return false;
		}
	}

	public function setCybersourceErrorResp()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		$order_number = $request->get('req_reference_number');

		$em = $this->container->get('doctrine')->getEntityManager();
		$customerOrder = $em->getRepository(CustomerOrder::Class)->getDataByOrderNumberAndUser($order_number, $user)->getQuery()->getOneOrNullResult();
		$customerPaymentEpayment = $customerOrder->getCustomerPaymentEpayment();
		if($customerOrder && $customerPaymentEpayment){

			$decision = $request->get('decision');
			$message = $request->get('message');
			$reason_code = $request->get('reason_code');
			$customerPaymentEpayment->setDecision($decision);
			$customerPaymentEpayment->setMessage($message);
			$customerPaymentEpayment->setReasonCode($reason_code);

			$payment_status_cancelled = $this->container->getParameter('payment_status_cancelled');
			$customerPaymentEpayment->setStatus($payment_status_cancelled);

			$customerOrder->setCancelled(1);
			$customerOrder->setDeleted(1);
			$em->flush();

			$session = $request->getSession();
			$session->getFlashBag()->add('cybersource_errors', $decision.', '.$message);

			return $customerPaymentEpayment;
		}else{
			return false;
		}
	}

	public function setCybersourceResp()
	{
		$request = $this->container->get('request_stack')->getCurrentRequest();
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		$order_number = $request->get('req_reference_number');

		$em = $this->container->get('doctrine')->getEntityManager();
		$customerOrder = $em->getRepository(CustomerOrder::Class)->getDataByOrderNumberAndUser($order_number, $user)->getQuery()->getOneOrNullResult();
		$customerPaymentEpayment = $customerOrder->getCustomerPaymentEpayment();
		if($customerOrder && $customerPaymentEpayment){

			$transaction_id = $request->get('transaction_id');
			$decision = $request->get('decision');
			$message = $request->get('message');
			$reason_code = $request->get('reason_code');
			$req_card_number = $request->get('req_card_number');
			$req_card_expiry_date = $request->get('req_card_expiry_date');
			$score_card_issuer = $request->get('score_card_issuer');
			$score_card_scheme = $request->get('score_card_scheme');
			$score_bin_country = $request->get('score_bin_country');
			$auth_amount = $request->get('auth_amount');
			$req_currency = $request->get('req_currency');
			$auth_time = $request->get('auth_time');

			$timestamp = strtotime($auth_time);
			$dt = new \DateTime();
			$dt->setTimestamp($timestamp);
			// $date_time = date("Y-m-d H:i:s", $timestamp);

			$customerPaymentEpayment->setTransactionId($transaction_id);
			$customerPaymentEpayment->setDecision($decision);
			$customerPaymentEpayment->setMessage($message);
			$customerPaymentEpayment->setReasonCode($reason_code);
			$customerPaymentEpayment->setReferenceNumber($order_number);
			$customerPaymentEpayment->setCardNumber($req_card_number);
			$customerPaymentEpayment->setCardExpiryDate($req_card_expiry_date);
			$customerPaymentEpayment->setCardIssuer($score_card_issuer);
			$customerPaymentEpayment->setCardScheme($score_card_scheme);
			$customerPaymentEpayment->setCardCountry($score_bin_country);
			$customerPaymentEpayment->setAuthAmount($auth_amount);
			$customerPaymentEpayment->setCurrency($req_currency);
			$customerPaymentEpayment->setAuthTime($dt);

			if($decision=='ACCEPT' && $reason_code=='100'){
				// successfully
				$payment_status_paid = $this->container->getParameter('payment_status_paid');
				$customerPaymentEpayment->setStatus($payment_status_paid);
				$customerOrder->setPaid(1);
			}

			$em->flush();

			return $customerPaymentEpayment;
		}else{
			return false;
		}
	}

	public function test()
	{
		// $is_authenticated = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');
	}

}
