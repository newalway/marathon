<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\ProductCategory;
use ProjectBundle\Entity\ProductCategoryTranslation;

use ProjectBundle\Form\Type\AdminProductCategoryType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Intl\Locale;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminProductCategoryController extends Controller
{
	const ROUTER_INDEX = 'admin_product_category';
	const ROUTER_ADD = self::ROUTER_INDEX.'_new';
	const ROUTER_EDIT = self::ROUTER_INDEX.'_edit';
	const ROUTER_PREFIX = 'product_category';
	const ROUTER_CONTROLLER = 'AdminProductCategory';

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
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);
		$product_category_root_id = $this->container->getParameter('product_category_root_id');

// Add root products
// $products = new ProductCategory();
// $products->translate('en')->setTitle('Products');
// $products->translate('th')->setTitle('สินค้า');
// $em->persist($products);
// $products->mergeNewTranslations();
// $em->flush();

// // find and update
// $fruits = $repo->find(31);
// $orange = new ProductCategory();
// $orange->translate('en')->setTitle('Orange');
// $orange->translate('th')->setTitle('ส้ม');
// $orange->setParent($fruits);
// $em->persist($orange);
// $orange->mergeNewTranslations();
// $em->flush();

// // Add new root
// $clothes = new ProductCategory();
// $clothes->translate('en')->setTitle('Clothes');
// $clothes->translate('th')->setTitle('เสื้อผ้า');
// $tshirt = new ProductCategory();
// $tshirt->translate('en')->setTitle('T-Shirt');
// $tshirt->translate('th')->setTitle('เสื้อยืด');
// $tshirt->setParent($clothes);
// $em->persist($clothes);
// $em->persist($tshirt);
// $clothes->mergeNewTranslations();
// $tshirt->mergeNewTranslations();
// $em->flush();

		// $options = $util->getHtmlTreeViewOptions();

		$query = $repo->findDataByRootId($product_category_root_id)->getQuery();
		$options = $util->getAdminTreeViewWithImgOptions();
		$tree = $repo->buildTree($query->getArrayResult(), $options);

		$util->setBackToUrl();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':index.html.twig', array(
			'tree' => $tree
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function newAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$product_category_root_id = $this->container->getParameter('product_category_root_id');

		// entities tree
		// $em = $this->getDoctrine()->getManager();
		// $em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');
		// $repo = $em->getRepository(ProductCategory::class);
		// $tree = $repo->createQueryBuilder('node')->getQuery()
    	// 	->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
    	// 	->getResult('tree');

		// get children from node
		// $product_category_node = $repo->find($product_category_root_id);
		// $arrayTree = $repo->childrenHierarchy($product_category_node);

		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		// $query = $repo->findChildrenByParentId(31)->getQuery();
		$query = $repo->findDataByRootId($product_category_root_id)->getQuery();
		$options = $util->getInputSelectTreeViewOptions();
		$option_tree = $repo->buildTree($query->getArrayResult(), $options);

		$form = $this->createForm(AdminProductCategoryType::class, new ProductCategory(), array('allow_extra_fields'=>true) );
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'option_tree'=>$option_tree
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function createAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$data = new ProductCategory();
		$form = $this->createForm(AdminProductCategoryType::class, $data, array('allow_extra_fields'=>true) );
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// $data = $form->getData();
			$request_data = $request->request->get('product_cat');
	  		$moveto = $request_data['moveto'];
			if(isset($request_data['parent'])){

				$parent_id = $request_data['parent'];
				$parent_node = $repo->find($parent_id);

				if($moveto=="firstchild"){
	   				$repo->persistAsFirstChildOf($data, $parent_node);
		   		}elseif($moveto=="lastchild"){
	   				$repo->persistAsLastChildOf($data, $parent_node);
		   		}elseif($moveto=="nextsibling"){
	   				$repo->persistAsNextSiblingOf($data, $parent_node);
		   		}elseif($moveto=="prevsibling"){
	   				$repo->persistAsPrevSiblingOf($data, $parent_node);
		   		}

			}else{
				//first child
				$product_category_root_id = $this->container->getParameter('product_category_root_id');
				$parent_node = $repo->find($product_category_root_id);
				$repo->persistAsFirstChildOf($data, $parent_node);
			}

			$em->flush();

			$product_util = $this->container->get('app.product');
			//save image size s,m,l
			$product_util->saveProductCategoryImageSize($data, null);

			$util->setCreateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}

		$product_category_root_id = $this->container->getParameter('product_category_root_id');
		$query = $repo->findDataByRootId($product_category_root_id)->getQuery();
		$options = $util->getInputSelectTreeViewOptions();
		$option_tree = $repo->buildTree($query->getArrayResult(), $options);

		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':new.html.twig', array(
			'form'=>$form->createView(),
			'option_tree'=>$option_tree
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function editAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$data = $this->getDoctrine()->getRepository(ProductCategory::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminProductCategoryType::class, $data);
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

		$data = $em->getRepository(ProductCategory::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$ori_image = $data->getImage();
		$form = $this->createForm(AdminProductCategoryType::class, $data, array('allow_extra_fields'=>true) );
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// $data = $form->getData();

			if($request->get('removefileimage')==1){
				$data->removeImage();
			}

			//updateSlug
			$oldSlug = $data->getSlug();
			$data->generateSlug();
			$newSlug = $data->getSlug();
			if ($oldSlug !== $newSlug) {
				$data->setSlug($newSlug);
			}

			$em->flush();

			$product_util = $this->container->get('app.product');
			//save image size s,m,l
			$product_util->saveProductCategoryImageSize($data, $ori_image);

			$util->setUpdateNotice();
			$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
			return $this->redirect($redirect_uri);
		}
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':edit.html.twig', array(
			'form'=>$form->createview()
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function move_positionAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$data = $this->getDoctrine()->getRepository(ProductCategory::class)->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$product_category_root_id = $this->container->getParameter('product_category_root_id');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$query = $repo->findDataByRootId($product_category_root_id)->getQuery();
		$options = $util->getInputSelectTreeViewOptions();
		$option_tree = $repo->buildTree($query->getArrayResult(), $options);

		$form = $this->createForm(AdminProductCategoryType::class, $data, array('allow_extra_fields'=>true) );
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':move_position.html.twig', array(
			'form'=>$form->createview(),
			'option_tree'=>$option_tree,
			'data'=>$data
		));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function move_position_updateAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$data = $repo->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$form = $this->createForm(AdminProductCategoryType::class, $data, array('allow_extra_fields'=>true));

		$request_data = $request->request->get('product_cat');
        $parent_id = $request_data['parent'];
  		$moveto = $request_data['moveto'];

		$parent_node = $repo->find($parent_id);
		if($moveto=="firstchild"){
			$repo->persistAsFirstChildOf($data, $parent_node);
   		}elseif($moveto=="lastchild"){
			$repo->persistAsLastChildOf($data, $parent_node);
   		}elseif($moveto=="nextsibling"){
			$repo->persistAsNextSiblingOf($data, $parent_node);
   		}elseif($moveto=="prevsibling"){
			$repo->persistAsPrevSiblingOf($data, $parent_node);
		}

		$em->flush();

		$util->setUpdateNotice();
		$redirect_uri = $util->getRedirectUriSaveBtn($form, $data, self::ROUTER_INDEX, self::ROUTER_ADD, self::ROUTER_EDIT);
		return $this->redirect($redirect_uri);
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function move_up_positionAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$data = $repo->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$repo->moveUp($data, 1);

		$util->setUpdateNotice();
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function move_down_positionAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$data = $repo->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$repo->moveDown($data, 1);

		$util->setUpdateNotice();
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}

	/**
	* @Secure(roles="ROLE_EDITOR")
	*/
	public function deleteAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository(ProductCategory::class);

		$data = $repo->find($request->get('id'));
		if (!$data) { throw $this->createNotFoundException('No data found'); }

		$repo->removeFromTree($data);
		$em->clear(); // clear cached nodes

		$util->setRemoveNotice();
		return $this->redirect($util->getBackToUrl(self::ROUTER_INDEX));
	}
}
