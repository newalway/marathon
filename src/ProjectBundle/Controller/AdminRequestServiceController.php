<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProjectBundle\Entity\RequestService;
use ProjectBundle\Form\Type\RequestServiceType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Doctrine\ORM\EntityManagerInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminRequestServiceController extends Controller
{
	const ROUTER_INDEX = 'admin_request_service';
    const ROUTER_CONTROLLER = 'AdminRequestService';

    protected function getQuerySearchData($arr_query_data)
    {
        $repository = $this->getDoctrine()->getRepository(RequestService::class);
        $query = $repository->findAllData($arr_query_data);
        return $query;
    }
    protected function prepare_query_data($request){
        $arr_data = $request->query->get('search_data');
        $arr_query_data['q'] = trim($arr_data['q']);
        return $arr_query_data;
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

        $arr_query_data = $this->prepare_query_data($request);
        $query = $this->getQuerySearchData($arr_query_data);
        $paginated = $util->setPaginatedOnPagerfanta($query);

        $util->setBackToUrl();
        return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
            'acctoken' => $acctoken,
            'paginated' =>$paginated,
            'arr_query_data'=>$arr_query_data
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
        $data = $em->getRepository(RequestService::class)->find($request->get('id'));
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
    public function requestServiceExcelAction(request $request){

        $util = $this->container->get('utilities');
        $em = $this->getDoctrine()->getManager();
        // $arr_query_data = $util->prepare_query_data($request);
        $arr_query_data = $this->prepare_query_data($request);
        //$members = $em->getRepository(User::class)->getAllMemberByData($arr_query_data)->getQuery()->getResult();
        $requestService = $this->getQuerySearchData($arr_query_data)->getQuery()->getResult();

        // dump($arr_query_data);
        // exit;

        $export_excel = $this->container->get('exportexcel');
        $export_excel->getHeaderExcelRequestService();
        $export_excel->setDataExcelRequestService($requestService);
        $name_post_fix = date('dMy-His');
        $file_name = 'request-survice-'.$name_post_fix.'.xlsx';
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

        $data = $em->getRepository(RequestService::class)->find($request->get('id'));
        if (!$data) { throw $this->createNotFoundException('No data found'); }

        $em->remove($data);
        $em->flush();

        $util->setRemoveNotice();
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
                $data = $em->getRepository(RequestService::class)->find($data_id);
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
		$data = $em->getRepository(RequestService::class)->countBy(['status'=>4]);
		if($data){
			$count_text='<small class="label pull-right bg-yellow">'.$data.'</small>';
		}else{
			$count_text='';
		}
		return new Response($count_text);
	}
}
