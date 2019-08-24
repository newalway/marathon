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

use ProjectBundle\Entity\PaymentGateway;
use ProjectBundle\Form\Type\AdminPaymentGatewayType;

class AdminPaymentGatewayController extends Controller
{
	const ROUTER_INDEX = 'admin_payment_gateway_setting';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
	const ROUTER_CONTROLLER = 'AdminPaymentGateway';

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function settingAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(PaymentGateway::class)->createQueryBuilder('p')->getQuery()->getOneOrNullResult();
		$form = $this->createForm(AdminPaymentGatewayType::class, $data);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();
			$util->setUpdateNotice();
		}

	    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':setting.html.twig', array(
			'form' => $form->createView()
		));
	}
}
