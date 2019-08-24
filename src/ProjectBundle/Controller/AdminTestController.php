<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Category;
use ProjectBundle\Entity\Brand;
use ProjectBundle\Entity\Location;
use ProjectBundle\Entity\Salon;
use ProjectBundle\Entity\ProfessionalVideo;
use ProjectBundle\Entity\VideoCategory;

use ProjectBundle\Entity\Style;
use ProjectBundle\Entity\StyleImage;
use ProjectBundle\Entity\StyleCategory;
use ProjectBundle\Entity\PaymentGateway;
use ProjectBundle\Entity\Showroom;
use ProjectBundle\Entity\ProductCategory;
use ProjectBundle\Entity\ProductCategoryTranslation;

use ProjectBundle\Form\Type\Product\ProductSearchType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminTestController extends Controller
{
	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function productAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();
		$cateogry_repo = $em->getRepository(ProductCategory::class);
		$category_id = $request->get('product_category_id');

		$locale = $request->getLocale();
		$product_repository = $this->getDoctrine()->getRepository(Product::class);
		$form = $this->createForm(ProductSearchType::class);
		$form->handleRequest($request);
		$data = $form->getData();

		$limitPages = $this->container->getParameter('max_per_page_latest_product');
		$limitPerPage = (isset($data['limitPerPage'])) ? $data['limitPerPage'] : $limitPages;

		if($category_id){
			$arr_cat_id = array();
			$pr_category = $cateogry_repo->find($category_id);
			$children = $cateogry_repo->children($pr_category, false, null, 'asc', true);
			foreach ($children as $chi) {
				$arr_cat_id[] = $chi->getId();
			}
			$data['product_category_id'] = $arr_cat_id;
		}

		$query = $product_repository->findAllActiveData($data, $locale);
		$paginated = $util->setPaginatedOnPagerfanta($query, $limitPerPage);

		//Product Category
		$product_category_root_id = $this->container->getParameter('product_category_root_id');
		$query = $cateogry_repo->findDataByRootId($product_category_root_id)->getQuery();
		$options = $util->getHtmlTreeViewOptions();
		$category_tree = $cateogry_repo->buildTree($query->getArrayResult(), $options);
		$obj_category_tree = $query->getResult();


		return $this->render('ProjectBundle:AdminTest:product.html.twig', array(
			'paginated'=>$paginated,
			'form' =>$form->createView(),
			'category_tree' => $category_tree,
			'obj_category_tree' => $obj_category_tree
		));
	}

	/**
	 * @Secure(roles="ROLE_EDITOR")
	 */
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$em = $this->getDoctrine()->getManager();

		//------------Nested with trans------------//
		$repo = $em->getRepository(ProductCategory::class);

		//Get data
		// $vegetables = $repo->createQueryBuilder('c')
		// 	->join('c.translations', 'ct')
		// 	->where('ct.title = :title')
		//     ->setParameter('title', 'Vegetables')
		// 	->getQuery()
		// 	->getOneOrNullResult();

		//Add data
		// $tomato = new ProductCategory();
		// $tomato->translate('en')->setTitle('ee Fresh Fresh Tomato');
		// $tomato->translate('th')->setTitle('มะเขือเทศสดๆ');
		// $tomato->setParent($vegetables);
		// $em->persist($tomato);
		// $tomato->mergeNewTranslations();
		// $em->flush();

		//Update data
		// $tomato = $repo->find(48);
		// $tomato->translate('en')->setTitle('ee Fresh Fresh Tomato');
		// $tomato->translate('th')->setTitle('มะเขือเทศสดๆ');
		// $tomato->mergeNewTranslations();
		// $em->flush();

		// Add root products
		// $products = new ProductCategory();
		// $products->translate('en')->setTitle('Products');
		// $products->translate('th')->setTitle('สินค้า');
		// $em->persist($products);
		// $products->mergeNewTranslations();
		// $em->flush();

		// $food = new ProductCategory();
		// // $food->setTitle('Food');
    	// $food->translate('en')->setTitle('Food');
		// $food->translate('th')->setTitle('อาหาร');
		//
		// $fruits = new ProductCategory();
		// // $fruits->setTitle('Fruits');
		// $fruits->translate('en')->setTitle('Fruits');
		// $fruits->translate('th')->setTitle('ผลไม้');
		// $fruits->setParent($food);
		//
		// $vegetables = new ProductCategory();
		// // $vegetables->setTitle('Vegetables');
		// $vegetables->translate('en')->setTitle('Vegetables');
		// $vegetables->translate('th')->setTitle('ผัก');
		// $vegetables->setParent($food);
		//
		// $carrots = new ProductCategory();
		// // $carrots->setTitle('Carrots');
		// $carrots->translate('en')->setTitle('Carrots');
		// $carrots->translate('th')->setTitle('แครอท');
		// $carrots->setParent($vegetables);
		// $em->persist($food);
		// $em->persist($fruits);
		// $em->persist($vegetables);
		// $em->persist($carrots);
		// $food->mergeNewTranslations();
		// $fruits->mergeNewTranslations();
		// $vegetables->mergeNewTranslations();
		// $carrots->mergeNewTranslations();
		// $em->flush();


		## find tree with translation ##
		// $locale = $request->getLocale();
		// $food = $repo->createQueryBuilder('c')
		// 	->join('c.translations', 'ct')
		// 	// ->select('c', 'ct')
		// 	->where('ct.title = :title')
 		//     ->setParameter('title', 'Food')
		// 	// ->andWhere("ct.locale = '$locale'")
		// 	->getQuery()
		// 	// ->getArrayResult();
		// 	->getOneOrNullResult();
		//
		// $vegetables = $repo->createQueryBuilder('c')
		// 	->join('c.translations', 'ct')
		// 	->where('ct.title = :title')
 		//     ->setParameter('title', 'Vegetables')
		// 	->getQuery()
		// 	->getOneOrNullResult();

		// // add potato
		// $potato = new ProductCategory();
		// $potato->translate('en')->setTitle('Potato');
		// $potato->translate('th')->setTitle('มัน');
		// $potato->setParent($vegetables);
		// $em->persist($potato);
		// $potato->mergeNewTranslations();
		// $em->flush();

		// echo 'Number of child : ' . $repo->childCount($food) . "<br/>";
		// $children = $repo->children($food);
		// foreach ($children as $chi) {
		// 	// echo $chi->getTitle()." ";
		//
		// 	$path = $repo->getPath($chi);
		// 	foreach ($path as $p) {
		// 		echo $p->getTitle()." -> ";
		// 		// echo $p->getId();
		// 		// if($p->getId()==35){
		// 			// $repo->moveUp($p, 1); //move up
		// 		// }
		// 	}
		// 	echo '<br/>';
		// }
		## end find tree with translation ##


		// $food = $repo->findOneByTitle('Food');
		// $vegetables = new ProductCategory();
		// $vegetables->setTitle('Vegetables');
		// $vegetables->setParent($food);
		// $em->persist($vegetables);
		// $em->flush();

		// $vegetables = $repo->findOneByTitle('Vegetables');
		// $potato = new ProductCategory();
		// $potato->setTitle('Onions');
		// $potato->setParent($vegetables);
		// $em->persist($potato);
		// $em->flush();
		// $carrots = $repo->findOneByTitle('Carrots');
		// $carrots = new ProductCategory();
		// $carrots->setTitle('Carrots');
		// $carrots->setParent($vegetables);
		// $em->persist($carrots);
		// $em->flush();

		// Inserting node in different positions
		// $food = new ProductCategory();
		// $food->setTitle('Food');
		// $fruits = new ProductCategory();
		// $fruits->setTitle('Fruits');
		// $vegetables = new ProductCategory();
		// $vegetables->setTitle('Vegetables');
		// $carrots = new ProductCategory();
		// $carrots->setTitle('Carrots');
		// $repo->persistAsFirstChild($food)
		//     ->persistAsFirstChildOf($fruits, $food)
		//     ->persistAsLastChildOf($vegetables, $food)
		//     ->persistAsNextSiblingOf($carrots, $fruits);
		// $em->flush();

		// Moving
		// $carrots = $repo->findOneByTitle('Onions');
		// move it up by one position, or top set 'true'
		// $repo->moveUp($carrots, 1);
		// move it down to the end set 'true' or one set '1'
		// $repo->moveDown($carrots, true);

		//Using repository functions
		//children
		// $food = $repo->findOneByTitle('Food');
		// echo $food->getTitle();
		// echo ' children : total_child = ';
		// echo $repo->childCount($food);
		// echo ', direct_child = ';
		// echo $repo->childCount($food, true /*direct*/);
		// echo ' <br/> child of '.$food->getTitle(). ' : ';
		// $children = $repo->children($food);
		// foreach ($children as $chi) {
		// 	echo $chi->getTitle()." ";
		// }
		// echo ' <br/> sort the children : ';
		// // will sort the children by title
		// $children = $repo->children($food, false, 'title');
		// foreach ($children as $chi) {
		// 	echo $chi->getTitle()." ";
		// }
		// echo ' <br/> ';
		// //path
		// $carrots = $repo->findOneByTitle('Carrots');
		// echo $carrots->getTitle()." path".' : ';
		// $path = $repo->getPath($carrots);
		// foreach ($path as $p) {
		// 	echo $p->getTitle()." ";
		// }

		// verification and recovery of tree
		// $repo->verify();
		// // can return TRUE if tree is valid, or array of errors found on tree
		// $repo->recover();
		// $em->flush(); // important: flush recovered nodes
		// // if tree has errors it will try to fix all tree nodes

		// UNSAFE: be sure to backup before running this method when necessary, if you can use $em->remove($node);
		// which would cascade to children
		// single node removal
		// $vegies = $repo->findOneByTitle('Vegetables');
		// $repo->removeFromTree($vegies);
		// $em->clear(); // clear cached nodes
		// it will remove this node from tree and reparent all children

		// reordering the tree
		// $food = $repo->findOneByTitle('Food');
		// $repo->reorder($food, 'title');
		// it will reorder all "Food" tree node left-right values by the title

		// Retrieving the whole tree as an array
		// $arrayTree = $repo->childrenHierarchy();
		// $htmlTree1 = $repo->childrenHierarchy(
		//     $food, /* starting from food nodes */
		//     false /* true: load all children, false: only direct */
		// );
		// $htmlTree2 = $repo->childrenHierarchy(
		//     // $food, /* starting from food nodes */
		// 	null, /* null, starting from root nodes */
		//     false, /* true: load all children, false: only direct */
		//     array(
		//         'decorate' => true,
		//         'representationField' => 'slug',
		//         'html' => true
		//     )
		// );
		// $options = array(
		//     'decorate' => true,
		//     'rootOpen' => '<ul>',
		//     'rootClose' => '</ul>',
		//     'childOpen' => '<li>',
		//     'childClose' => '</li>',
		//     'nodeDecorator' => function($node) {
		//         return '<a href="/page/'.$node['title'].'">'.$node['title'].'</a>';
		//     }
		// );
		// $htmlTree3 = $repo->childrenHierarchy(
		//     null, /* starting from root nodes */
		//     false, /* true: load all children, false: only direct */
		//     $options
		// );

		// Generate your own node list
		// $root_id = 9;
		// $query = $repo->findDataByRootId($root_id)->getQuery();
		// $options = array('decorate' => true);
		// $tree = $repo->buildTree($query->getArrayResult(), $options);

		// Building trees from your entities
		// $em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');
		// $repo = $em->getRepository(ProductCategory::class);
		// $tree = $repo->createQueryBuilder('c')->getQuery()
		//     ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
		//     ->getResult('tree');
		//------------End Nested------------//



		//------------Gedmo Translation (removed)------------//
		// $repo = $em->getRepository(ProductCategory::class);
		// $food = $repo->findOneByTitle('Food');
		//
		// $dessert = new ProductCategory();
		// $dessert->setTitle('Dessert'); // assumes default locale is "en"
		// $dessert->setParent($food);
		// // $dessert->addTranslation(new ProductCategoryTranslation('en', 'title', 'Dessert'));
		// $dessert->addTranslation(new ProductCategoryTranslation('th', 'title', 'ของหวาน'));
		//
		// $em->persist($dessert);
		// $em->flush();
		//------------End Gedmo Translation (removed)------------//


		// find fastest showroom distance
		// $pw = $em->getRepository(Showroom::class)->getFastestDistanceByLatLng('13.723418599999999', '100.4762319')
		// $pw = $em->getRepository(Showroom::class)->getFastestDistanceByProductAndLatLng(39, '13.723418599999999', '100.4762319')
		// 	->setMaxResults(3)
		// 	->getQuery()
		// 	->getArrayResult();
		// print_r($pw);
		// exit;


		// find one record
		// $pw = $em->getRepository(PaymentGateway::class)->createQueryBuilder('p')->getQuery()->getOneOrNullResult();
		// $a = $pw->getGateway();

		// Save PaymentGateway
		// $pg = new PaymentGateway();
		// $arrGW = array('BT','CRDT','COD');
		// $pg->setGateway($arrGW);
		// $em->persist($pg);
		// $em->flush();
		// Get PaymentGateway
		// $payment_gateway = $this->getDoctrine()->getRepository(PaymentGateway::class)->find(1);
		// $pw = $payment_gateway->getGateway();

		// // Saving Related
		// $category = new Category();
    	// $category->setTitle('Computer Peripherals');
		//
		// $brand = new Brand();
    	// $brand->setTitle('Razer');
		//
		// $product = new Product();
	    // $product->setTitle('Monitor');
	    // $product->setPrice(199.99);
	    // $product->setDescription('Ergonomic and stylish!');
		//
		// // relate this product to the category
    	// $product->setCategory($category);
		// $product->setBrand($brand);
		//
		// $em->persist($category);
    	// $em->persist($brand);
		// $em->persist($product);
    	// $em->flush();
		//
		// #Fetching Related
		// $productId = 4;
		// $product = $this->getDoctrine()
        // ->getRepository(Product::class)
        // ->find($productId);
    	// $category = $product->getCategory();
		// echo $category->getTitle();
		//
		// $categoryId=1;
		// $category = $this->getDoctrine()
        // ->getRepository(Category::class)
        // ->find($categoryId);
    	// $products = $category->getProducts();
		// foreach ($products as $product) {
		// 	echo $product->getTitle();
		// 	echo '<br/>';
		// }
		//
		// // Joining Related Records
		// $productId = 4;
		// $product = $em
		// 	->createQuery(
        // 'SELECT p, c FROM ProjectBundle:Product p
        // JOIN p.category c
        // WHERE p.id = :id')
		// 	->setParameter('id', $productId)
		// 	->getSingleResult();
		// $category = $product->getCategory();
		// echo $category->getTitle();
		//
		// $productId = 4;
		// $product = $this->getDoctrine()
        // ->getRepository(Product::class)
        // ->findOneByIdJoinedToEquipment($productId);
		// if($product){
		// 	$category = $product->getCategory();
		// 	echo $category->getTitle();
		// }else{
		// 	echo 'nodata';
		// }
		//
		// $catId = 2;
		// $category = $this->getDoctrine()
        // ->getRepository(Category::class)
        // ->findOneByIdJoinedToProducts($catId);
		// if($category){
		// 	$category->getId();
		// 	$products = $category->getProducts();
		// 	foreach ($products as $product) {
		// 		echo $product->getId();
		// 		echo '<br/>';
		// 	}
		// }else{
		// 	echo 'nodata';
		// }

		// // ************************************************//
		// // One to Many
		//
		// $location = new Location();
    	// $location->setTitle('Phayathai');
		//
		// $salon = new Salon();
	    // $salon->setTitle('P1');
	    // $salon->setDescription('Salon phayathai station!');
		//
		// // relate this salon to the location
    	// $salon->setLocation($location);
		//
		// $em->persist($location);
		// $em->persist($salon);
    	// $em->flush();
		//
		// // ************************************************//
		// // One to Many
		//
		// $video_category = new VideoCategory();
    	// $video_category->setTitle('Main Video');
		//
		// $professional_video = new ProfessionalVideo();
	    // $professional_video->setTitle('Video 1');
	    // $professional_video->setDescription('Hello world!');
		// $professional_video->setEmbed('<image src="yes"/>');
		//
		// // relate this salon to the location
    	// $professional_video->setVideoCategory($video_category);
		//
		// $em->persist($video_category);
		// $em->persist($professional_video);
    	// $em->flush();
		//
		// // ************************************************//
		// // One to Many
		//
		// $style_category = new StyleCategory();
    	// $style_category->setTitle('Longhair');
		//
		// $style = new Style();
	    // $style->setTitle('Style 1');
	    // $style->setDescription('Hello world!');
		// $style->setImage('<image src="/s1.jpg"/>');
		//
		// $style_image1 = new StyleImage();
    	// $style_image1->setImage('<image src="/i1.jpg"/>');
		// $style_image1->setStyle($style);
		//
		// $style_image2 = new StyleImage();
    	// $style_image2->setImage('<image src="/i2.jpg"/>');
		// $style_image2->setStyle($style);
		//
		// $em->persist($style);
		// $em->persist($style_image1);
		// $em->persist($style_image2);
    	// $em->flush();

		// // ************************************************//
		// // Many to Many
		//
		// // Create Style and Add related
		// $style = new Style();
    	// $style->setTitle('Style 4');
		// // Get style_cat data
		// $style_cat_id = 1;
		// $style_category = $em->getRepository(StyleCategory::class)->find($style_cat_id);
		// // Related
		// $style->addStyleCategories($style_category);
		// $em->persist($style);
    	// $em->flush();
		//
		// // Create StyleCategory and Add related
		// $style_category = new StyleCategory();
	    // $style_category->setTitle('Mama');
	    // $style_category->setDescription('Hi Mama!');
		// // Get style_cat data
		// $style_id = 1;
		// $style = $em->getRepository(Style::class)->find($style_id);
		// // Related
		// $style_category->addStyles($style);
		// $em->persist($style_category);
    	// $em->flush();
		//
		// // Get data and Add related
		// $style_cat_id = 2;
		// $style_category = $em->getRepository(StyleCategory::class)->find($style_cat_id);
		// $style_id = 4;
		// $style = $em->getRepository(Style::class)->find($style_id);
		// // Related
		// $style_category->addStyles($style);
		// $em->persist($style_category);
    	// $em->flush();
		//
		// // Remove related
		// $style_cat_id = 2;
		// $style_category = $em->getRepository(StyleCategory::class)->find($style_cat_id);
		// $style_id = 4;
		// $style = $em->getRepository(Style::class)->find($style_id);
		// // Related
		// $style->removeCategories($style_category); //remove from style
		// //$style_category->removeStyles($style); //remove from style_category
		// $em->persist($style_category);
    	// $em->flush();
		//
		// // Get related data from style_category
		// $style_cat_id = 1;
		// $style_category = $em->getRepository(StyleCategory::class)->find($style_cat_id);
		// if($style_category){
		// 	$styles = $style_category->getStyles();
		// 	if($styles){
		// 		foreach ($styles as $style) {
		// 			echo $style->getTitle();
		// 			echo '<br/>';
		// 		}
		// 	}
		// }
		//
		// // Get related data from style
		// $style_id = 4;
		// $style = $em->getRepository(Style::class)->find($style_id);
		// if($style){
		// 	$style_categories = $style->getStyleCategories();
		// 	if($style_categories){
		// 		foreach ($style_categories as $style_category) {
		// 			echo $style_category->getTitle();
		// 			echo '<br/>';
		// 		}
		// 	}
		// }

		//************************************************//
		// Many to Many

		// Create Product and Add related Salon
		// $product = new Product();
    	// $product->setTitle('New Product 1');
		// // Get style_cat data
		// $salon_id = 1;
		// $salon = $em->getRepository(Salon::class)->find($salon_id);
		// if($salon){
		// 	// Related
		// 	$product->addSalons($salon);
		// }
		// $em->persist($product);
    	// $em->flush();

		// Create Salon and Add related Product
		// $salon = new Salon();
	    // $salon->setTitle('Salon A');
	    // $salon->setDescription('Hello world');
		// // Get style_cat data
		// $product_id = 5;
		// $product = $em->getRepository(Product::class)->find($product_id);
		// if($product){
		// 	// Related
		// 	$salon->addProducts($product);
		// }
		// $em->persist($salon);
		// $em->flush();

		// Get data and Add related
		// $product_id = 6;
		// $product = $em->getRepository(Product::class)->find($product_id);
		// $salon_id = 2;
		// $salon = $em->getRepository(Salon::class)->find($salon_id);
		// // Related
		// $product->addSalons($salon);
		// // $salon->addProducts($product);
		// $em->persist($product);
    	// $em->flush();

		// Remove related
		// $salon_id = 1;
		// $salon = $em->getRepository(Salon::class)->find($salon_id);
		// $product_id = 6;
		// $product = $em->getRepository(Product::class)->find($product_id);
		// // Related
		// $product->removeSalons($salon); //remove from product
		// //$salon->removeProducts($product); //remove from salon
		// $em->persist($product);
    	// $em->flush();

		// Get related data from salon
		// $salon_id = 5;
		// $salon = $em->getRepository(Salon::class)->find($salon_id);
		// if($salon){
		// 	$products = $salon->getProducts();
		// 	if($products){
		// 		foreach ($products as $product) {
		// 			echo $product->getTitle();
		// 			echo '<br/>';
		// 		}
		// 	}
		// }

		// Get related data from product
		// $product_id = 5;
		// $product = $em->getRepository(Product::class)->find($product_id);
		// if($product){
		// 	$salons = $product->getSalons();
		// 	if($salons){
		// 		foreach ($salons as $salon) {
		// 			echo $salon->getTitle();
		// 			echo '<br/>';
		// 		}
		// 	}
		// }

		//************************************************//
		/*
		//insert object
		$product = new Product();
		$product->setName('Keyboard');
		$product->setPrice(19.99);
		$product->setDescription('Ergonomic and stylish!');
		$em->persist($product);
		$em->flush();
		exit;
		*/

		/*
		//find object
		$productId =1;
		$product = $this->getDoctrine()
		    ->getRepository(Product::class)
		    ->find($productId);
		if (!$product) {
		    throw $this->createNotFoundException(
		        'No product found for id '.$productId
		    );
		}
		echo $product->getId();
		echo $product->getName();
		echo $product->getPrice();
		echo $product->getDescription();
		*/

		/*
		$repository = $this->getDoctrine()->getRepository(Product::class);
		//repository object find all
		$products = $repository->findAll();
		print_r($products);
		*/

		/*
		// query for a single product matching the given name and price
		$repository = $this->getDoctrine()->getRepository(Product::class);
		$product = $repository->findOneBy(
		    array('name' => 'Keyboard', 'price' => 19.99)
		);
		print_r($product);

		// query for multiple products matching the given name, ordered by price
		$products = $repository->findBy(
		    array('name' => 'Keyboard'),
		    array('price' => 'ASC')
		);
		print_r($products);
		*/

		//updating an object
		/*
		$productId=1;
		$product = $em->getRepository(Product::class)->find($productId);
		if (!$product) {
		    throw $this->createNotFoundException(
		        'No product found for id '.$productId
		    );
		}
		$product->setName('New product name!');
		$em->flush();
		exit;
		*/

		//Deleting an Object
		/*
		$productId=3;
		$product = $em->getRepository(Product::class)->find($productId);
		$em->remove($product);
		$em->flush();
		*/

		//Querying for Objects
		/*
		$repository = $this->getDoctrine()->getRepository(Product::class);
		$product = $repository->find(1);
		$product = $repository->findOneByName('Keyboard');
		*/

		//Querying for Objects with DQL
		/*
		$query = $em->createQuery(
		    'SELECT p
		    FROM ProjectBundle:Product p
		    WHERE p.price > :price
		    ORDER BY p.price ASC'
		)->setParameter('price', 10.99);
		$products = $query->getResult();
		//$products = $query->setMaxResults(1)->getOneOrNullResult();
		print_r($products);
		exit;
		*/

		//Querying for Objects Using Doctrine's Query Builder
		/*
		$repository = $this->getDoctrine()->getRepository(Product::class);
		$query = $repository->createQueryBuilder('p')
		    ->where('p.price > :price')
		    ->setParameter('price', '10.99')
		    ->orderBy('p.price', 'ASC')
		    ->getQuery();
		$products = $query->getResult();
		print_r($products);
		exit;
		*/


		//Create custom Repository Classes
		// $products = $this->getDoctrine()
		//     ->getRepository(Product::class)
		//     ->findAllOrderedByName();
		// print_r($products);
		// exit;



		// //** Working with QueryBuilder Expr **//
		// $repository = $this->getDoctrine()->getRepository(User::class);
		// $qb = $repository->createQueryBuilder('u');
		// $qb->where($qb->expr()->andX(
		//        $qb->expr()->notLike('u.roles', ':user_roles')
		//     ))
		//     ->setParameter('user_roles', '%ROLE_CUSTOMER%')
		//     ->orderBy('u.roles', 'ASC');
		// $datatable = $qb->getQuery()->getResult();

			// //** Working with QueryBuilder **//
			// $repository = $this->getDoctrine()->getRepository(User::class);
		// $qb = $repository->createQueryBuilder('u');
		// $qb->where('u.roles NOT LIKE :user_roles')
		//     ->setParameter('user_roles', '%ROLE_CUSTOMER%')
		//     ->orderBy('u.roles', 'ASC');
		// $datatable = $qb->getQuery()->getResult();

			// //** Doctrine Query Language (DQL) **//
		// $em = $this->getDoctrine()->getManager();
		// $query = $em->createQuery(
		//     'SELECT u
		//     FROM ProjectBundle:User u
		//     WHERE u.roles NOT LIKE :user_roles
		//     ORDER BY u.roles ASC'
		// )->setParameter('user_roles', '%ROLE_CUSTOMER%');
		// $datatable = $query->getResult();

		//find by relation user or filed
		// $access_tokens = $em->getRepository(AccessToken::class)->findByUser($user);


		//************************************************//

	    /*
	    //Creating A Client (Authorization Request)
	    $clientManager = $this->get('fos_oauth_server.client_manager.default');
	    $client = $clientManager->createClient();
	    $client->setRedirectUris(array('http://www.example.com'));
	    $client->setAllowedGrantTypes(array('token', 'authorization_code', 'password', 'refresh_token', 'client_credentials'));
	    $clientManager->updateClient($client);
	    */

	    /*
	    //Basic Auth
	    //User: Bearer
	    //Pass: Token (class_key 2)
	    //Authorization:Bearer ZDY4MzM3ZmFlMWM4YTdiZjY2ZDVmNTJkMzAwODkwMWRkZjAz...
	    //Protected Resource
	    $acctoken = '';
	    $response = $this->call(
	        'GET',
	        '/api/v1/users',
	        $acctoken,
	        json_encode($request->request->all())
	    );
	    $status = json_decode($response->getBody());
	    print_r($status->datas);
	    exit;
	    */

			/*
			//use http
	    $request = $this->get('request_stack')->getCurrentRequest();
			$shceme_http = $request->getSchemeAndHttpHost();
	    $shceme_http = str_replace("https://", "http://", $shceme_http);
	    $base_uri = $shceme_http.$this->container->get('router')->getContext()->getBaseUrl().'/api/v1/';

	    $method = 'GET';
	    $uri = 'public/users';
	    $postData = '';
	    $client = new Client(['base_uri' => $base_uri]);
	    $response = $client->request(
	           $method,
	           $uri,
	           [
	               'headers' => [
	                   'Content-Type' => 'application/x-www-form-urlencoded',
	                   'Accept' => 'application/json',
	                   #'Authorization' => 'Bearer '.$auth
	               ],
	               'body' => $postData, //'debug' => true,
	               'verify' => false
	           ]
	    );
	    $status = json_decode($response->getBody());
	    if($status->status){
	      $status->status;
	    }
		*/

		/*
		// Test send mail
		$message = (new \Swift_Message('Test message 2'))
		->setFrom(array('no-reply@marathon.co.th' => 'no-reply@marathon.co.th'))
		->setTo(array('num@zap-interactive.com'))
		->setBody(
			$this->renderView(
				'ProjectBundle:'.$this->container->getParameter('view_main').':_email_test.html.twig',
				array()
			),
			'text/html'
		);
		$this->get('mailer')->send($message);
		*/

		return $this->render('ProjectBundle:AdminTest:index.html.twig', array());
	}
}
