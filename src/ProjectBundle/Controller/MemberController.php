<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\User;
use ProjectBundle\Entity\DeliveryAddress;
use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerOrderItem;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;

use ProjectBundle\Form\Type\MemberGenerateTokenType;

class MemberController extends Controller
{
	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function indexAction(Request $request)
	{
		$session = $request->getSession();
		$user = $this->getUser();
		$user_id = $user->getId();
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':index.html.twig',array());
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function member_generate_tokenAction(Request $request)
	{
		$user = $this->getUser();
		$form = $this->createForm(MemberGenerateTokenType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':member_generate_token.html.twig',array('form' =>$form->createView()));
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function member_generate_token_updateAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$user = $this->getUser();
		if(!$user){
			throw $this->createAccessDeniedException('This User not found in the database');
		}
		$form = $this->createForm(MemberGenerateTokenType::class, $user, array('csrf_protection' => true,'allow_extra_fields'=>true));
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){

			$arr_data =  $request->get('member_generate_token');
			$current_passowrd = $arr_data['current_password'];
			$userManager = $this->container->get('fos_user.user_manager');
			$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
			$pwd_encoded = $encoder->encodePassword($current_passowrd, $user->getSalt());

			if($user->getPassword() != $pwd_encoded) {
				$form->get('current_password')->addError(new FormError('Incorrect Password'));
			}

			if($form->isSubmitted() && $form->isValid()){
				$email = $user->getEmail();

				// delete all token before create new
				$util->deleteAccessAndRefreshToken($user);

				//get user scope
				$user_roles = $user->getRoles();
				if( in_array("ROLE_CLIENT",$user_roles) ){
					$scope = $this->container->getparameter('access_token_client_scope');
				}else{
					$scope = $this->container->getparameter('access_token_customer_scope');
				}

				//set oauth token
				$util->setAccessToken($email, $current_passowrd, $scope);

				//reset session access token
				$token = $util->getAccessTokenFromDB();

				return $this->redirectToRoute('fos_user_profile_show');
			}
		}
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':member_generate_token.html.twig',array('form' =>$form->createView()));
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function member_set_passwordAction(Request $request)
	{
		$user = $this->getUser();
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':member_set_password.html.twig',array());
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function ordersAction(Request $request)
	{
		$session = $request->getSession();
		$user = $this->getUser();
		$em = $this->getDoctrine();
		$util = $this->container->get('utilities');
		$limitPages = $this->container->getParameter('max_per_page_order');
		$customerOrder = $em->getRepository(CustomerOrder::Class)->findCustomerOrderHasItemByUser($user);
		$paginated = $util->setPaginatedOnPagerfanta($customerOrder,$limitPages);

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':orders.html.twig',array('paginated'=>$paginated));
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function member_social_connectionsAction(Request $request)
	{
		$user = $this->getUser();
		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':member_social_connections.html.twig',array());
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function _address_bookAction(Request $request)
	{
		$session = $request->getSession();
		$user = $this->getUser();
		$em = $this->getDoctrine();

		$default_shipping_address = $em->getRepository(DeliveryAddress::Class)->findDefaultShippingAddressById($user)->getQuery()->getOneOrNullResult();
		$default_billing_address = $em->getRepository(DeliveryAddress::Class)->findDefaultBillingAddressById($user)->getQuery()->getOneOrNullResult();

		if(empty($default_shipping_address)){
			$default_shipping_address = $em->getRepository(DeliveryAddress::Class)->findAllDataById($user)->setMaxResults(1)->getQuery()->getOneOrNullResult();
		}
		if(empty($default_billing_address)){
			$default_billing_address = $em->getRepository(DeliveryAddress::Class)->findAllDataById($user)->setMaxResults(1)->getQuery()->getOneOrNullResult();
		}

		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':_address_book.html.twig',array(
			'default_shipping_address'=>$default_shipping_address,
			'default_billing_address'=>$default_billing_address,
		));
	}

	/**
	* @Secure(roles="ROLE_CUSTOMER, ROLE_CLIENT")
	*/
	public function _recent_ordersAction(Request $request)
	{
		$user = $this->getUser();

		$em = $this->getDoctrine();
		$limit = $this->container->getParameter('max_query_recent_order');
		$customerOrder = $em->getRepository(CustomerOrder::Class)->findCustomerOrderHasItemByUser($user)->setMaxResults($limit)->getQuery()->getResult();


		return $this->render('ProjectBundle:'.$this->container->getParameter('view_member').':_recent_orders.html.twig',array('orders'=>$customerOrder));
	}

}
