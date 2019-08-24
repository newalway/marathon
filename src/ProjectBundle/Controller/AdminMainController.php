<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminMainController extends Controller
{
	const ROUTER_CONTROLLER = 'AdminMain';

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$session = $request->getSession();

		try {
      $acctoken = $util->getAccessTokenAfterLogin();
    } catch(\Exception $e) {
      //no access token or expire redirect to generate_token
      return $this->redirectToRoute('admin_user_generate_token');
    }

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array());
	}
}
