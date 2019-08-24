<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\SettingOption;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminSettingController extends Controller
{
	const ROUTER_PREFIX = 'setting/options';
	const ROUTER_INDEX = 'admin_setting';
	const ROUTER_CONTROLLER = 'AdminSetting';

	/**
	* @Secure(roles="ROLE_ADMIN")
	*/
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$repository = $this->getDoctrine()->getRepository(SettingOption::class);
		$group_datas = $repository->getDataByCatType('email')->getQuery()->getResult();
		$util->setBackToUrl();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array('group_datas' => $group_datas));
	}

	/**
	* @Secure(roles="ROLE_ADMIN")
	*/
	public function updateAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$datas = $this->getDoctrine()->getRepository(SettingOption::class)->findAll();
		if(count($datas)<=0) {
			throw $this -> createNotFoundException('The data doesn\'t exist');
		}

		if($request->get('data')){
			try {
				$acctoken = $util->getAccessToken();
			} catch(\Exception $e) {
				return $this->redirectToRoute('admin_user_generate_token');
			}

			$response= $util->call(
				'PUT',
				self::ROUTER_PREFIX.'/update',
				$acctoken,
				json_encode($request->request->all())
			);
			$status = json_decode($response->getBody());
			if($status->success){
				$this->get('session')->getFlashBag()->add('notice', 'Your changes were saved');
			}
		}

		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

	/**
	* @Secure(roles="ROLE_ADMIN")
	*/
	public function _databygroupAction(Request $request, $group_data)
	{
		$cat_type = $group_data->getCatType();
		$group_type = $group_data->getGroupType();

		$repository = $this->getDoctrine()->getRepository(SettingOption::class);
		$datas = $repository->getDataByGroup($cat_type, $group_type)->getQuery()->getResult();

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':_databygroup.html.twig', array('group_data'=>$group_data, 'datas'=>$datas));
	}
}
