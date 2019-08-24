<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Contact;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Finder\Finder;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminContactController extends Controller
{
	const ROUTER_INDEX = 'admin_contact';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  	const ROUTER_PREFIX = 'contact';
	const ROUTER_CONTROLLER = 'AdminContact';

	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Contact::class);
    	$query = $repository->findAllData($arr_query_data);
		return $query;
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
	public function viewAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$util->setCkAuthorized();
    	$data = $em->getRepository(Contact::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		if($data->getStatus() == 4){
			$data->setStatus(3);
			$em->flush();
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':view.html.twig', array(
			'data' => $data
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Contact::class)->find($request->get('id'));
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
				$data = $em->getRepository(Contact::class)->find($data_id);
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

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function _getunreadAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
    	$data = $em->getRepository(Contact::class)->countBy(['status'=>4]);
		if($data){
			$count_text='<small class="label pull-right bg-yellow">'.$data.'</small>';
	    }else{
			$count_text='';
	    }
    	return new Response($count_text);
	}
}
