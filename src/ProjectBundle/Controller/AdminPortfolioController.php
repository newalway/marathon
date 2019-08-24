<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Portfolio;
use ProjectBundle\Entity\PortfolioImage;

use ProjectBundle\Form\Type\AdminProductType;
use ProjectBundle\Form\Type\AdminPortfolioType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminPortfolioController extends Controller
{

	const ROUTER_INDEX = 'admin_portfolio';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  	const ROUTER_PREFIX = 'portfolio';
	const ROUTER_CONTROLLER = 'AdminPortfolio';


	protected function getQuerySearchData($arr_query_data)
	{
		$repository = $this->getDoctrine()->getRepository(Portfolio::class);
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
			}catch(\Exception $e) {
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
		$gallery_images=array();

		$data = new Portfolio();
		$date = new \DateTime();
		$data->setPublicDate($date);

		$form = $this->createForm(AdminPortfolioType::class, $data, array('allow_extra_fields'=>true));
	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'gallery_images'=>$gallery_images
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function createAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		//get image_gallery
		$image_path = $request->get('img_path');

		$data = new Portfolio();
		$gallery_images = array();
		$form = $this->createForm(AdminPortfolioType::class, $data, array('allow_extra_fields'=>true));
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->persist($data);

			//save image_gallery
			if(is_array($image_path) && count($image_path)>0){
				foreach ($image_path as $image_uri) {
					if($image_uri){
						$gallery_image = new PortfolioImage();
						$gallery_image->setImage($image_uri);
						$gallery_image->setPortfolio($data);
						$em->persist($gallery_image);
					}
				}
			}
			$em->flush();

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
	  	}

	  	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			  'form'=>$form->createView(),
			  'gallery_images'=>$gallery_images
		));
	}
	/**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function editAction(Request $request)
  {
	$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$data = $this->getDoctrine()->getRepository(Portfolio::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }
		$gallery_images = $data;
		 // dump($data);
		 // exit;
		$form = $this->createForm(AdminPortfolioType::class, $data, array('allow_extra_fields'=>true));
	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'gallery_images'=>$gallery_images
		));
  }

  /**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function updateAction(Request $request)
  {
	$util = $this->container->get('utilities');
	$em = $this->getDoctrine()->getManager();
		//get image_gallery
		$image_path = $request->get('img_path');
		//get delete image_gallery
		$del_img_gallery_ids = $request->get('del_img_gallery');

		$data = $em->getRepository(Portfolio::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }
		$gallery_images = $data->getImage();

	$form = $this->createForm(AdminPortfolioType::class, $data, array('allow_extra_fields'=>true));
	$form->handleRequest($request);

	if ($form->isSubmitted() && $form->isValid()) {

			//remove main image
			if($request->get('removefileimage')==1){
				$data->removeImage();
			}

			//save image_gallery

			if(is_array($image_path) && count($image_path)>0){
				foreach ($image_path as $image_uri) {
					if($image_uri){
						$gallery_image = new PortfolioImage();
						$gallery_image->setImage($image_uri);
						$gallery_image->setPortfolio($data);
						$em->persist($gallery_image);
					}
				}
			}

			//delete image_gallery
			if(is_array($del_img_gallery_ids) && count($del_img_gallery_ids)>0){
				foreach ($del_img_gallery_ids as $del_img_id) {
					$del_style_image = $em->getRepository(PortfolioImage::class)->find($del_img_id);
					$em->remove($del_style_image);
				}
			}

			$em->flush();

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
	}
	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'gallery_images'=>$gallery_images
		));
  }

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
  public function deleteAction(Request $request)
  {
	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

	$data = $em->getRepository(Portfolio::class)->find($request->get('id'));
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
				$data = $em->getRepository(Portfolio::class)->find($data_id);
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
				$data = $em->getRepository(Portfolio::class)->find($data_id);
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
				$data = $em->getRepository(Portfolio::class)->find($data_id);
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

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
	public function sortAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$arr_query_data = $util->prepare_query_data($request);
		$datas = $this->getQuerySearchData($arr_query_data)->getQuery()->getResult();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':sort.html.twig', array('datas' =>$datas));
	}

	/**
  * @Secure(roles="ROLE_EDITOR")
  */
	public function sort_prosessAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$i=0;
		$sorted = $request->get('sort');
		if ($sorted) {
			foreach ($sorted as $data_id) {
				$data = $em->getRepository(Portfolio::class)->find($data_id);
				if ($data) {
					$i=$i+1;
					$data->setPosition($i);
				}
			}
			try {
				$em->flush();
				$status='complete';
			} catch(\Exception $e) {
				$status='error';
			}
			return new Response($status);
		}
	}
}
