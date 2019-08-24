<?php

namespace ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjectBundle\Entity\Pages;

use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Brand;


use ProjectBundle\Form\Type\AdminImportExcelType;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

use JMS\SecurityExtraBundle\Annotation\Secure;
use GuzzleHttp\Client;

class AdminImportExcelController extends Controller
{
  const ROUTER_INDEX = 'admin_import_excel';
	const ROUTER_CONTROLLER = 'AdminImportExcel';

	/**
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function indexAction(Request $request)
	{
		$util = $this->container->get('utilities');
		$util->setCkAuthorized();
		$form = $this->createForm(AdminImportExcelType::class);
		$form->handleRequest($request);

		$em = $this->getDoctrine()->getManager();
		//$repository = $this->getDoctrine()->getRepository(Pages::class);
		if ($form->isSubmitted() && $form->isValid()) {
			$file = $form['file']->getData();
			$type = $file->getMimeType();
			$mime_type = $this->container->getParameter('source_path_spreadsheet_mime_type');
			// dump($mime_type);
			// exit;
			$import_excel = $this->container->get('importexcel');
			
			if(in_array($type,$mime_type)){
				$excels = $import_excel->importExcelFile($file);
			}else{
				$excels= false;
			}

			// dump($excels);
			// exit;
				if($excels){
					foreach($excels as $index => $rows) {
						$product = new Product();
						// this is where you do your database stuff
						if($index !=0 && !is_null($rows)){
							$import_excel->validateRow($rows);


							//$last_key = key(array_slice( $rows, -1, 1, TRUE ));// last arr key is publich publish_date
							$rows[14] = $import_excel->excelDateFormat($rows[14]);// end arr is publich publish_date value

							$product_title_en = $rows[0];
							$product_title_th = $rows[1];
							$product_shortDesc_en = $rows[2];
							$product_shortDesc_th = $rows[3];
							$product_description_en = $rows[4];
							$product_description_th = $rows[5];
							$product_price = $rows[6];
							$product_compare_at_price = $rows[7];
							$product_sku = $rows[8];
							$inventory_policy_status = $rows[9];
							$inventory_quantity = $rows[10];
							$weight  = $rows[11];
							$weight_unit = $rows[12];
							$status  = $rows[13];
							$publish_date = $rows[14];
							$brand_id = $rows[15];


							$brand = $this->getDoctrine()->getRepository(Brand::class)->find($brand_id);

							$product->setCompareAtPrice($product_compare_at_price);
							$product->setPublishDate(\DateTime::createFromFormat('Y-m-d',$publish_date));
							$product->setInventoryPolicyStatus($inventory_policy_status);


							if ($inventory_policy_status != 0) {
								$product->setInventoryQuantity($inventory_quantity);
							}

							$product->setWeight($weight);
							$product->setWeightUnit($weight_unit);
							$product->setStatus($status);
							$product->setSku($product_sku);
							$product->setPrice($product_price);
							$product->setBrand($brand);



							$product->translate('en')->setTitle($product_title_en);
							$product->translate('en')->setShortDesc($product_shortDesc_en);
							$product->translate('en')->setDescription($product_description_en);

							$product->translate('th')->setTitle($product_title_th);
							$product->translate('th')->setShortDesc($product_shortDesc_th);
							$product->translate('th')->setDescription($product_description_th);

							$em->persist($product);
							$product->mergeNewTranslations();

						}/// end if in foreach
					}/// end  foreach
					$em->flush($product);
					$this->get('session')->getFlashBag()->add('notice', 'Data Import successfull.');
					$util->setBackToUrl();
				}elseif($excels == false){
					$this->get('session')->getFlashBag()->add('error', 'Apcept file .xls or excel format only!!');
					$util->setBackToUrl();
				}
		}

    	$util->setBackToUrl();
		return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':import.html.twig', array(
			'form' =>$form->createView(),
		));
	}

	// /**
    //  * @Secure(roles="ROLE_ADMIN")
    //  */
    // public function insertAction(Request $request)
    // {
  	//   $util = $this->container->get('utilities');
  	//   $util->setCkAuthorized();
  	//   $form = $this->createForm(AdminImportExcelType::class);
  	//   $form->handleRequest($request);
    //
  	//   $em = $this->getDoctrine()->getManager();
  	//   //$repository = $this->getDoctrine()->getRepository(Pages::class);
  	//   if ($form->isSubmitted() && $form->isValid()) {
  	// 	  $file = $form['file']->getData();
  	// 	  $import_excel = $this->container->get('importexcel');
  	// 	  $excels = $import_excel->importExcelFile($file);
    //
  	// 		  if($excels){
  	// 			  foreach($excels as $index => $rows) {
  	// 				  $product = new Product();
  	// 				  // this is where you do your database stuff
  	// 				  if($index !=0 && !is_null($rows)){
  	// 					  $import_excel->validateRow($rows);
    //
    //
  	// 					  //$last_key = key(array_slice( $rows, -1, 1, TRUE ));// last arr key is publich publish_date
  	// 					  $rows[14] = $import_excel->excelDateFormat($rows[14]);// end arr is publich publish_date value
    //
  	// 					  $product_title_en = $rows[0];
  	// 					  $product_title_th = $rows[1];
  	// 					  $product_shortDesc_en = $rows[2];
  	// 					  $product_shortDesc_th = $rows[3];
  	// 					  $product_description_en = $rows[4];
  	// 					  $product_description_th = $rows[5];
  	// 					  $product_price = $rows[6];
  	// 					  $product_compare_at_price = $rows[7];
  	// 					  $product_sku = $rows[8];
  	// 					  $inventory_policy_status = $rows[9];
  	// 					  $inventory_quantity = $rows[10];
  	// 					  $weight  = $rows[11];
  	// 					  $weight_unit = $rows[12];
  	// 					  $status  = $rows[13];
  	// 					  $publish_date = $rows[14];
  	// 					  $brand_id = $rows[15];
    //
    //
  	// 					  $brand = $this->getDoctrine()->getRepository(Brand::class)->find($brand_id);
    //
  	// 					  $product->setCompareAtPrice($product_compare_at_price);
  	// 					  $product->setPublishDate(\DateTime::createFromFormat('Y-m-d',$publish_date));
  	// 					  $product->setInventoryPolicyStatus($inventory_policy_status);
    //
    //
  	// 					  if ($inventory_policy_status != 0) {
  	// 						  $product->setInventoryQuantity($inventory_quantity);
  	// 					  }
    //
  	// 					  $product->setWeight($weight);
  	// 					  $product->setWeightUnit($weight_unit);
  	// 					  $product->setStatus($status);
  	// 					  $product->setSku($product_sku);
  	// 					  $product->setPrice($product_price);
  	// 					  $product->setBrand($brand);
    //
    //
    //
  	// 					  $product->translate('en')->setTitle($product_title_en);
  	// 					  $product->translate('en')->setShortDesc($product_shortDesc_en);
  	// 					  $product->translate('en')->setDescription($product_description_en);
    //
  	// 					  $product->translate('th')->setTitle($product_title_th);
  	// 					  $product->translate('th')->setShortDesc($product_shortDesc_th);
  	// 					  $product->translate('th')->setDescription($product_description_th);
    //
  	// 					  $em->persist($product);
  	// 					  $product->mergeNewTranslations();
    //
  	// 				  }/// end if in foreach
  	// 			  }/// end  foreach
  	// 			  $em->flush($product);
  	// 			  $this->get('session')->getFlashBag()->add('notice', 'Data Import successfull.');
  	// 			  $util->setBackToUrl();
  	// 		  }elseif($excels == false){
  	// 			  $this->get('session')->getFlashBag()->add('error', 'Apcept file .xls or excel format only!!');
  	// 			  $util->setBackToUrl();
  	// 		  }
    //
  	// 	  // 	elseif($count_excel_rows <= 1){
  	// 	  // 		throw new Exception("Data in row must be more than 1");
  	// 	  // 	}/// end  if
  	// 	  // }catch (\Doctrine\ORM\ORMException $e) {
  	// 	  // 	//$util->setBackToUrl();
  	// 	  // 	$this->get('session')->getFlashBag()->add('error', $e->getMessage(),"\n");
  	// 	  // 	$util->setBackToUrl();
  	// 	  // 	//echo 'Caught exception: ',  $e->getMessage(), "\n";
  	// 	  // 	// /exit;
  	// 	  // }
  	// 	  // catch (Exception $e) {
  	// 	  // 	//$util->setBackToUrl();
  	// 	  // 	$this->get('session')->getFlashBag()->add('error', $e->getMessage(),"\n");
  	// 	  // 	$util->setBackToUrl();
  	// 	  // 	//echo 'Caught exception: ',  $e->getMessage(), "\n";
  	// 	  // 	// /exit;
  	// 	  // }
    //
    //
  	//   }
    //
  	//   $util->setBackToUrl();
  	//   return $this->render('ProjectBundle:'.self::ROUTER_CONTROLLER.':import.html.twig', array(
  	// 	  'form' =>$form->createView(),
  	//   ));
    // }
}
