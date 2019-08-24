<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Sku;
use ProjectBundle\Entity\Hashtag;
use ProjectBundle\Entity\Variant;
use ProjectBundle\Entity\VariantOption;

use ProjectBundle\Form\Type\AdminProductType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminProductController extends Controller
{
	const ROUTER_INDEX = 'admin_product';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
  	const ROUTER_PREFIX = 'product';
	const ROUTER_CONTROLLER = 'AdminProduct';

	protected function prepare_query_data($request) {
		$arr_query_data['search_status'] = ($request->query->get('search_status')) ? str_replace('string:', '', $request->query->get('search_status')) : 'all';
		$arr_query_data['q'] = trim($request->query->get('q'));
		return $arr_query_data;
	}

	protected function getQuerySearchData($arr_query_data, $locale) {
		$repository = $this->getDoctrine()->getRepository(Product::class);
    	$query = $repository->findAllDataJoined($arr_query_data, $locale);
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
    	$arr_query_data = $this->prepare_query_data($request);
		$query = $this->getQuerySearchData($arr_query_data, $request->getLocale());

		$paginated = $util->setPaginatedOnPagerfanta($query);

	    $util->setBackToUrl();
	    return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'paginated' =>$paginated,
			'arr_query_data'=>$arr_query_data
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

		$data = new Product();
		$current_date = new \DateTime();
		$data->setPublishDate($current_date); //set the default value
		$form = $this->createForm(AdminProductType::class, $data, array('allow_extra_fields'=>true));
    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'acctoken'=>$acctoken,
			'post_gallery_image_path'=>array()
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

		$data = new Product();
	    $form = $this->createForm(AdminProductType::class, $data, array('allow_extra_fields'=>true));
	    $form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
			//create product
			$em->persist($data);
	    	$em->flush();

			//product data
			$frm_product = $request->request->get('admin_product');

			//save product image size s,m,l
			$product_util->saveProductImageSize($data, null);

			//save image_gallery
			$product_util->saveProductImageGallery($data);

			//validate inventory with button "add" and "set"
			// $product_util->saveProductInventoryAdjustment($data, $frm_product);

			//weight grams
			$product_util->saveProductWeightGrams($data, $frm_product);

			//tags
			$product_util->saveProductHashtags($data, $frm_product);

			//variants
			$product_util->saveProductVariants($data, $frm_product);

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
    	}

		$image_path = $request->request->get('img_path');
		$post_gallery_image_path = ($image_path) ? $image_path : array() ;

    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'acctoken'=>$acctoken,
			'post_gallery_image_path'=>$post_gallery_image_path
		));
  	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function editAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');
		$util->setCkAuthorized();
		$acctoken = $util->getAccessToken();

		$data = $this->getDoctrine()->getRepository(Product::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$product_id = $data->getId();
		$form = $this->createForm(AdminProductType::class, $data, array('allow_extra_fields'=>true));

		// $have_variants = $product_util->isVariants($data);

    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken,
			'product'=>$data,
			'post_gallery_image_path'=>array()
		));
  	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function updateAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$product_util = $this->container->get('app.product');

    	$em = $this->getDoctrine()->getManager();
		$acctoken = $util->getAccessToken();

		$data = $em->getRepository(Product::class)->findOneById($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$product_id = $data->getId();
		$ori_image = $data->getImage();
	    $form = $this->createForm(AdminProductType::class, $data, array('allow_extra_fields'=>true));
	    $form->handleRequest($request);

	    if ($form->isSubmitted() && $form->isValid())
		{
			if($request->get('removefileimage')==1){
				$data->removeImage();
			}

// $data = $form->getData();
// var_dump($data['inventoryPolicyStatus']);
// exit;
			$em->flush();

			//product data
			$frm_product = $request->request->get('admin_product');

			//save product image size s,m,l
			$product_util->saveProductImageSize($data, $ori_image);

			//save image_gallery
			$product_util->saveProductImageGallery($data);

			//validate inventory with button "add" and "set"
			// $product_util->saveProductInventoryAdjustment($data, $frm_product);

			//weight grams
			$product_util->saveProductWeightGrams($data, $frm_product);

			//tags
			$product_util->saveProductHashtags($data, $frm_product);

			//update variants
			$product_util->updateProductVariants($data);

			//create variants
			$product_util->saveProductVariants($data, $frm_product);

			//save product inventory status
			$product_util->skuSaveProductInventoryStatus($data);

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
    	}

		$image_path = $request->request->get('img_path');
		$post_gallery_image_path = ($image_path) ? $image_path : array() ;

    	return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview(),
			'acctoken'=>$acctoken,
			'product'=>$data,
			'post_gallery_image_path'=>$post_gallery_image_path
		));
  	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function deleteAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

    	$data = $em->getRepository(Product::class)->find($request->get('id'));
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
				$data = $em->getRepository(Product::class)->find($data_id);
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
				$data = $em->getRepository(Product::class)->find($data_id);
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
				$data = $em->getRepository(Product::class)->find($data_id);
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
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function group_bestsellerAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$data_ids = $request->get('data_ids');
		if ($data_ids) {
			$flg_pub = false;
			foreach ($data_ids as $data_id) {
				$data = $em->getRepository(Product::class)->find($data_id);
				if ($data) {
					$data->setIsBestSeller(1);
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
	public function group_newAction(Request $request)
	{
    	$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$data_ids = $request->get('data_ids');
		if ($data_ids) {
			$flg_pub = false;
			foreach ($data_ids as $data_id) {
				$data = $em->getRepository(Product::class)->find($data_id);
				if ($data) {
					$data->setIsNew(1);
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
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function sortAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$arr_query_data = $util->prepare_query_data($request);
		$datas = $this->getQuerySearchData($arr_query_data, $request->getLocale())->getQuery()->getResult();
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
				$data = $em->getRepository(Product::class)->find($data_id);
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

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function sort_best_sellerAction(Request $request)
	{
		$datas = $this->getDoctrine()->getRepository(Product::class)->findProductBestSeller()->getQuery()->getResult();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':sort_best_seller.html.twig', array('datas' =>$datas));
	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function sort_best_seller_processAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		$i=0;
		$sorted = $request->get('sort');
		if ($sorted) {
			foreach ($sorted as $data_id) {
				$data = $em->getRepository(Product::class)->find($data_id);
				if ($data) {
					$i=$i+1;
					$data->setBestSellerPosition($i);
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
