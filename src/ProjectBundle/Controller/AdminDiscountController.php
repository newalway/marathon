<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Finder\Finder;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

use ProjectBundle\Entity\Discount;
use ProjectBundle\Entity\DiscountSetting;
use ProjectBundle\Entity\Product;

use ProjectBundle\Form\Type\AdminDiscountType;
use ProjectBundle\Form\Type\AdminDiscountSettingType;

class AdminDiscountController extends Controller
{
	const ROUTER_INDEX = 'admin_discount';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  	const ROUTER_PREFIX = 'discount';
	const ROUTER_CONTROLLER = 'AdminDiscount';

	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Discount::class);
    	$query = $repository->findAllDataJoinedCustomerOrder($arr_query_data);
		return $query;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function settingAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(DiscountSetting::class)->createQueryBuilder('p')->getQuery()->getOneOrNullResult();
		$form = $this->createForm(AdminDiscountSettingType::class, $data);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();
			$util->setUpdateNotice();
		}

	    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':setting.html.twig', array(
			'form' => $form->createView()
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
	    try {
			$acctoken = $util->getAccessToken();
	    } catch(\Exception $e) {
			return $this->redirectToRoute('admin_user_generate_token');
	    }
    	$arr_query_data = $util->prepare_query_data($request);
		$query = $this->getQuerySearchData($arr_query_data);

		$paginated = $util->setPaginatedOnPagerfanta($query);

	    $util->setBackToUrl();
	    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'paginated' =>$paginated
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function newAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();

		$form = $this->createForm(AdminDiscountType::class, new Discount(), array('allow_extra_fields'=>true));
		//set default startDate
		$form->get('startDate')->setData(new \DateTime);
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'acctoken'=>$acctoken
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function createAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$acctoken = $util->getAccessToken();
		$em = $this->getDoctrine()->getManager();

		$data = new Discount();
		$form = $this->createForm(AdminDiscountType::class, $data, array('allow_extra_fields'=>true));
		$form->handleRequest($request);

		$form = $this->validateForm($form, $data);

		if ($form->isSubmitted() && $form->isValid()) {

			$em->persist($data);
			$em->flush();

			//save discount_applies_specific_products
			$product_util->saveDiscountAppliesSpecificProducts($data);

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'acctoken'=>$acctoken
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function editAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();
		$data = $this->getDoctrine()->getRepository(Discount::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminDiscountType::class, $data, array('allow_extra_fields'=>true));
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function updateAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$acctoken = $util->getAccessToken();
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Discount::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminDiscountType::class, $data, array('allow_extra_fields'=>true));
		$form->handleRequest($request);

		$form = $this->validateForm($form, $data);

		if ($form->isSubmitted() && $form->isValid()) {

			$em->flush();

			//save discount_applies_specific_products
			$product_util->saveDiscountAppliesSpecificProducts($data);

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Discount::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		try {
			$em->remove($data);
			$em->flush();
			$util->setRemoveNotice();
		} catch(\Doctrine\DBAL\DBALException $e) {
			$util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getTitle());
		}

		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function group_deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data_ids = $request->get('data_ids');

		if ($data_ids) {
			$flg_del = false;
			foreach ($data_ids as $data_id) {
				$data = $em->getRepository(Discount::class)->find($data_id);
				if ($data) {
					try {
						$em->remove($data);
						$em->flush();
						$flg_del = true;
					} catch(\Doctrine\DBAL\DBALException $e) {
						$util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getTitle());
					}
				}
			}
			if ($flg_del) {
				$util->setRemoveNotice();
			}
		}
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}



	private function validateForm($form, $data)
	{
		$minimumRequirement = $data->getMinimumRequirement();
		if($minimumRequirement==2){
			$minimumRequirementAmountValue = $data->getMinimumRequirementAmountValue();
			if(!$minimumRequirementAmountValue){
				$form->get('minimumRequirementAmountValue')->addError(new FormError('Please enter this information.'));
			}
		}elseif($minimumRequirement==3){
			$minimumRequirementQuantityValue = $data->getMinimumRequirementQuantityValue();
			if(!$minimumRequirementQuantityValue){
				$form->get('minimumRequirementQuantityValue')->addError(new FormError('Please enter this information.'));
			}
		}

		$usageLimitDiscountTotal = $data->getUsageLimitDiscountTotal();
		if($usageLimitDiscountTotal==1){
			$usageLimitDiscountTotalValue = $data->getUsageLimitDiscountTotalValue();
			if(!$usageLimitDiscountTotalValue){
				$form->get('usageLimitDiscountTotalValue')->addError(new FormError('Please enter this information.'));
			}
		}

		$isEndDate = $data->getIsEndDate();
		if($isEndDate==1){
			$endDate = $data->getEndDate();
			if(!$endDate){
				$form->get('endDate')->addError(new FormError('Please enter this information.'));
			}
		}

		return $form;
	}


}
