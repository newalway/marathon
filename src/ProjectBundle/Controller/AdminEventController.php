<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ProjectBundle\Entity\EventCategory;
use ProjectBundle\Entity\Event;
use ProjectBundle\Form\Type\AdminEventType;
use ProjectBundle\Form\Type\AdminSearchEvenType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminEventController extends Controller
{
	const ROUTER_INDEX = 'admin_event';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
	const ROUTER_CONTROLLER = 'AdminEvent';

	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Event::class);
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

		$form_search = $this->createForm(AdminSearchEvenType::class, null, array('allow_extra_fields'=>true));
		$form_search->handleRequest($request);

		$arr_query_data = $request->query->get('admin_search_even');
		$query = $this->getQuerySearchData($arr_query_data);

		$paginated = $util->setPaginatedOnPagerfanta($query);

		$util->setBackToUrl();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'paginated' =>$paginated,
			'form_search' =>$form_search->createView(),
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function newAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$form = $this->createForm(AdminEventType::class, new Event());
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView()
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function createAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = new Event();
		$form = $this->createForm(AdminEventType::class, $data);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$form_data = $form->getData();
			$video = $form_data->getVideo();
			$video_type = $util->videoType($video);
			if($video_type != 'unknown'){
				if($video_type == 'youtube'){
					// Youtube Json format
					$json_url = $util->youtube_json_url($video);
					$youtube_video_id = $util->youtube_video_id($video);
					$oembed_json = json_decode($util->curl_get($json_url));

					if($oembed_json){
						$data->setVideoId($youtube_video_id);
						$data->setVideoEmbed($oembed_json->html);
						$data->setVideoWidth($oembed_json->width);
						$data->getVideoHeight($oembed_json->height);
						$data->setVideoDuration(null);
						$data->setThumbnailUrl($oembed_json->thumbnail_url);
						$data->setThumbnailWidth($oembed_json->thumbnail_width);
						$data->setThumbnailHeight($oembed_json->thumbnail_height);
						$data->setThumbnailUrlPlayButton($oembed_json->thumbnail_url);
						// if(empty($form_data->getTitle())){
						// 	$data->setTitle($oembed_json->title);
						// }
					}else{
						$util->setCustomeFlashMessage('warning', $msg="The URL for this video is not valid.");
						$redirect_uri = $this->generateUrl(self::ROUTER_ADD);
					}
				}
			}

			$em->persist($data);
			$em->flush();

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView()
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function editAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$data = $this->getDoctrine()->getRepository(Event::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminEventType::class, $data);
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview()
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function updateAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Event::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }
		$current_video = $data->getVideo();

		$form = $this->createForm(AdminEventType::class, $data);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$form_data = $form->getData();
			$video = $form_data->getVideo();
			if (strcmp($current_video, $video) !== 0) {
				$video_type = $util->videoType($video);
				if($video_type == 'youtube'){
					// Youtube Json format
					$json_url = $util->youtube_json_url($video);
					$youtube_video_id = $util->youtube_video_id($video);
					$oembed_json = json_decode($util->curl_get($json_url));
					if($oembed_json){
						$data->setVideoId($youtube_video_id);
						$data->setVideoEmbed($oembed_json->html);
						$data->setVideoWidth($oembed_json->width);
						$data->getVideoHeight($oembed_json->height);
						$data->setVideoDuration(null);
						$data->setThumbnailUrl($oembed_json->thumbnail_url);
						$data->setThumbnailWidth($oembed_json->thumbnail_width);
						$data->setThumbnailHeight($oembed_json->thumbnail_height);
						$data->setThumbnailUrlPlayButton($oembed_json->thumbnail_url);

					}else{
						$util->setCustomeFlashMessage('warning', $msg="The URL for this video is not valid.");
						$redirect_uri = $this->generateUrl(self::ROUTER_EDIT,array('id'=>$data->getId()));
						return $this->redirect($redirect_uri);
					}
				}
			}

			if($request->get('removefileimage')==1){
				$data->removeImage();
			}

			$em->flush();

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array('form'=>$form->createview()));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository(Event::class)->find($request->get('id'));
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
				$data = $em->getRepository(Event::class)->find($data_id);
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
	* @Secure(roles="ROLE_ADMIN")
	*/
	public function group_enableAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$data_ids = $request->get('data_ids');
		if ($data_ids) {
			$flg_pub = false;
			foreach ($data_ids as $data_id) {
				$data = $em->getRepository(Event::class)->find($data_id);
				if ($data) {
					$data->setStatus(1);
					$flg_pub = true;
				}
			}
			try {
				$em->flush();
			} catch(\Exception $e) {
				$util->setCustomeFlashMessage('warning', $msg="Can't enable ");
			}
			if($flg_pub){
				$util->setCustomeFlashMessage('notice', $msg="Published ");
			}
		}
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

	/**
	* @Secure(roles="ROLE_ADMIN")
	*/
	public function group_disableAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$data_ids = $request->get('data_ids');
		if ($data_ids) {
			$flg_pub = false;
			foreach ($data_ids as $data_id) {
				$data = $em->getRepository(Event::class)->find($data_id);
				if ($data) {
					$data->setStatus(0);
					$flg_pub = true;
				}
			}
			try {
				$em->flush();
			} catch(\Exception $e) {
				$util->setCustomeFlashMessage('warning', $msg="Can't disable ");
			}
			if ($flg_pub) {
				$util->setCustomeFlashMessage('notice', $msg="Unpublished ");
			}
		}
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}
}
