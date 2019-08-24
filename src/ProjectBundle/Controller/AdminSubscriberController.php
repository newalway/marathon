<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Subscriber;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Finder\Finder;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminSubscriberController extends Controller
{
	const ROUTER_INDEX = 'admin_subscriber';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  	const ROUTER_PREFIX = 'subscriber';
	const ROUTER_CONTROLLER = 'AdminSubscriber';

	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Subscriber::class);
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
	public function subscriber_excel_dowloadAction(request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$arr_query_data = $util->prepare_query_data($request);
		$data = $this->getQuerySearchData($arr_query_data)->getQuery()->getResult();

		$export_excel = $this->container->get('exportexcel');
		$export_excel->getExcelObjectSubscriber();
		$export_excel->exportSubscriberData($data);

		$name_post_fix = date('dMy');
		$file_name = 'Subscriber '.$name_post_fix.'.xlsx';
		$export_excel->saveOutputExcel($file_name);
		$response =  $export_excel->streamOutputExcel($file_name);

		return $response;
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Subscriber::class)->find($request->get('id'));
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
				$data = $em->getRepository(Subscriber::class)->find($data_id);
				if ($data) {
					try {
						$em->remove($data);
				    $em->flush();
						$flg_del = true;
					} catch(\Doctrine\DBAL\DBALException $e) {
						$util->setCustomeFlashMessage('warning', $msg="Can't delete ".$data->getEmail());
					}
				}
			}
			if ($flg_del) {
				$util->setRemoveNotice();
			}
		}
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}
}
