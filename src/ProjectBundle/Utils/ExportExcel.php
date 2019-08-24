<?php

namespace ProjectBundle\Utils;

#use Liuggio\ExcelBundle\Factory;

use ProjectBundle\Utils\Collections;
use Symfony\Component\Translation\TranslatorInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Color;
// use PhpOffice\PhpSpreadsheet\Style\Conditional;
// use PhpOffice\PhpSpreadsheet\Style\Font;

use ProjectBundle\Entity\CustomerOrder;
use ProjectBundle\Entity\CustomerOrderItem;

use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;

class ExportExcel
{
	private $kernel;
	private $index;
	private $spreadsheet;
	private $translator;

	#protected $phpexcel;
	#protected $phpExcelObject;

	public function __construct($kernel, TranslatorInterface $translator)
	{
		$this->container = $kernel->getContainer();
		$this->spreadsheet = new Spreadsheet();
		$this->index = 0;
		$this->translator = $translator;

		#$this->phpexcel = $phpexcel;
		#$this->phpExcelObject = $this->phpexcel->createPHPExcelObject();
		#$this->index = 0;
	}

	public function getExcelObjectSubscriber()
	{
		$this->spreadsheet->getProperties()->setCreator("Num")
		->setLastModifiedBy("Num")
		->setTitle("Subscriber")
		->setSubject("Subscriber")
		->setDescription("Subscriber")
		->setKeywords("Subscriber")
		->setCategory("Subscriber");

		$this->spreadsheet->setActiveSheetIndex($this->index)
			->setCellValue('A1', 'No.')
			->setCellValue('B1', 'Name')
			->setCellValue('C1', 'Email')
			->setCellValue('D1', 'Created At');
		$this->excelCellColor('A1:D1','523112');
		$this->excelFontColor('A1:D1','FFFFFF');
		$this->excelColunmSize('A','D');
	}
	public function exportSubscriberData($subscribers)
	{
		$i=2; //row number
		$no=1; //order index
		if(!$subscribers){
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		foreach ($subscribers as $subscriber){
			$this->setExcelRowSubscriber($i, $subscriber, $no);

			if(($no%2)==0){
				$this->excelCellColor('A'.$i.':D'.$i, 'EEEEEE');
			}
			$i++;
			$no++;
		}//endfor
	}
	protected function setExcelRowSubscriber($i, $subscriber, $no)
	{
		$id = $subscriber->getId();
		$createdat = $subscriber->getCreatedAt();
		$data_name = $subscriber->getName();
		$data_email = $subscriber->getEmail();
		$data_createdat = $createdat->format('d F Y');

		$this->spreadsheet->setActiveSheetIndex($this->index)
			->setCellValue('A'.$i, $no)
			->setCellValue('B'.$i, $data_name)
			->setCellValue('C'.$i, $data_email)
			->setCellValue('D'.$i, $data_createdat);
	}

	public function getExcelObjectMember()
	{
		$this->spreadsheet->getProperties()->setCreator("Num")
		->setLastModifiedBy("Num")
		->setTitle("Member")
		->setSubject("Member")
		->setDescription("Member")
		->setKeywords("Member")
		->setCategory("Member");
		$this->spreadsheet->setActiveSheetIndex($this->index)
			->setCellValue('A1', 'No.')
			->setCellValue('B1', 'Name')
			->setCellValue('C1', 'PhoneNumber')
			->setCellValue('D1', 'Email')
			->setCellValue('E1', 'Gender')
			->setCellValue('F1', 'Birth date')
			->setCellValue('G1', 'Role')
			->setCellValue('H1', 'Last login');
		$this->excelCellColor('A1:H1','523112');
		$this->excelFontColor('A1:H1','FFFFFF');
		$this->excelColunmSize('A','H');

	}

	public function exportMemberData($members)
	{
		$i=2; //row number
		$order_no=1; //order index
		if(!$members){
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		foreach ($members as $member){
			$this->setExcelRowMember($i, $member, $order_no);

			if(($order_no%2)==0){
				$this->excelCellColor('A'.$i.':S'.$i, 'EEEEEE');
			}
			$i++;
			$order_no++;
		}//endfor
	}

	protected function setExcelRowMember($i, $member, $order_no)
	{
		$user_id = $member->getId();

		$data_gender = Collections::wordGender($member->getGender());
		$data_birthdate = $member->getBirthDate();
		if($data_birthdate){
			$data_birthdate = $data_birthdate->format('d F Y');
		}
		$data_lastlogin = $member->getLastLogin();
		if($data_lastlogin){
			$data_lastlogin = $data_lastlogin->format('d/m/Y H:i:s');
		}

		$this->spreadsheet->setActiveSheetIndex($this->index)
			->setCellValue('A'.$i, $order_no)
			->setCellValue('B'.$i, $member->getFirstName().' '.$member->getLastName())
			->setCellValue('C'.$i, $member->getPhoneNumber())
			->setCellValue('D'.$i, $member->getEmail())
			->setCellValue('E'.$i, $data_gender)
			->setCellValue('F'.$i, $data_birthdate)
			->setCellValue('G'.$i, Collections::getCustomerPlan($member))
			->setCellValue('H'.$i, $data_lastlogin);
	}

	public function getExcelResponse()
	{
		$writer = $this->phpexcel->createWriter($this->spreadsheet, 'Excel5');
		$response = $this->phpexcel->createStreamedResponse($writer);
		return $response;
	}

	public function saveExcelFile($source_path, $file_name)
	{
		$writer = $this->phpexcel->createWriter($this->spreadsheet, 'Excel5');
		$writer->save($source_path.$file_name);
		return $file_name;
	}

	public function excelCellColor($cells, $color)
	{
		// $this->spreadsheet->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
		// 	'type' => PHPExcel_Style_Fill::FILL_SOLID,
		// 	'startcolor' => array(
		// 		'rgb' => $color
		// 	),
		// ));

		$this->spreadsheet->getActiveSheet()->getStyle($cells)->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()->setARGB($color);

	}
	public function excelFontColor($cells, $color)
	{
		$this->spreadsheet->getActiveSheet()->getStyle($cells)
		->getFont()->getColor()->setARGB($color);
	}
	function excelColunmSize($columnStart,$columnEnd){
		foreach(range($columnStart,$columnEnd) as $columnID) {
    		$this->spreadsheet->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
		}
	}
	function excelAccountingFormat($column){
		$this->spreadsheet->getActiveSheet()->getStyle($column)
		->getNumberFormat()->setFormatCode('_(""* #,##0.00_);_(""* \(#,##0.00\);_(""* "-"??_);_(@_)');
	}




	# for spreadsheet
	public function saveOutputExcel($filename='export.xlsx')
	{
		// // save export file
		$source_path = $this->container->getParameter('source_data_export_path');
		$writer = new Xlsx($this->spreadsheet);
		$writer->save($source_path.$filename);
	}

	public function streamOutputExcel($filename='export.xlsx')
	{
		// // redirect output to client browser
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$writer = new Xlsx($this->spreadsheet);
		$writer->save('php://output');
		exit;

		// // https://phpspreadsheet.readthedocs.io/en/develop/topics/recipes/
		// $writer = IOFactory::createWriter($this->spreadsheet, 'Xls');
		// $writer->save('php://output');
	}
	public function getHeaderExcelOrder(){

		$this->spreadsheet->setActiveSheetIndex($this->index)
				->setCellValue('A1', 'No')
			  	->setCellValue('B1', 'Order number')
			  	->setCellValue('C1', 'Order Date')
			  	->setCellValue('D1', 'Customer Name')
				->setCellValue('E1', 'Email')
				->setCellValue('F1', 'Phone')
			  	->setCellValue('G1', 'Status')
			  	->setCellValue('H1', 'Method')
			  	->setCellValue('I1', 'Shipment')
			  	->setCellValue('J1', 'Delivery Address')
				->setCellValue('K1', 'Product Name')
				->setCellValue('L1', 'Price')
				->setCellValue('M1', 'Quantity')
				->setCellValue('N1', 'Amount')
				->setCellValue('O1', 'subTotal')
				->setCellValue('P1', 'Discount Code')
				->setCellValue('Q1', 'Discount Amount')
				->setCellValue('R1', 'Total');
				//->setCellValue('Q1', 'Amount');

		$this->excelCellColor('A1:R1','523112');
		$this->excelFontColor('A1:R1','FFFFFF');
		$this->excelColunmSize('A','R');
		$this->excelAccountingFormat('R');
		$this->excelAccountingFormat('Q');
		$this->excelAccountingFormat('N');
		$this->excelAccountingFormat('L');
		$this->excelAccountingFormat('O');



	// 	$this->spreadsheet->getActiveSheet()->getStyle('B3:B7')->getFill()
    // ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    // ->getStartColor()->setARGB('FFFF0000');
	}
	public function getHeaderExcelOrderItems(){

		$this->spreadsheet->setActiveSheetIndex($this->index)
				->setCellValue('A4', 'No')
				->setCellValue('B4', 'Product Name')
				->setCellValue('C4', 'Price')
				->setCellValue('D4', 'Quantity')
				->setCellValue('E4', 'Total');
				$this->excelCellColor('A4:E4','523112');
				$this->excelFontColor('A4:E4','FFFFFF');
	}
	public function setDataExcelOrder($orders)
	{
		$i=2; //row number
		$order_no=1; //order index
		$start = 2;


		if(!$orders){
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		$repo_order_item = $this->container->get('doctrine')->getRepository(CustomerOrderItem::class);

		foreach ($orders as $order){

			$orderNumber = $order->getOrderNumber();

			$order_date = $order->getOrderDate()->format('d F Y');
			$status = '=IF('.$order->getPaid().'=1,"paid","unpaid")';

			$customer_name = $order->getUser()->getFirstName()." ".$order->getUser()->getLastName();
			$phone = $order->getUser()->getPhoneNumber();
			$email = $order->getUser()->getEmail();

			$payment = $order->getPaymentOption();
			$shipDate = ($order->getShipDate()) ? $order->getShipDate()->format('d F Y') : null;
			// $shipDate= $order->getShipDate()->format('d F Y');
			$shippingCost = $order->getShippingCost();
			$DiscountCode = $order->getDiscountCode();
			$DiscountAmount	= $order->getDiscountAmount();

			$sub_totalPrice = $order->getSubTotal();
			$totalPrice = $order->getTotalPrice();

			$this->spreadsheet->setActiveSheetIndex($this->index)
				  ->setCellValue('A'.$i, $order_no)
				  ->setCellValue('B'.$i, $orderNumber)
				  ->setCellValue('C'.$i, $order_date)
				  ->setCellValue('D'.$i, $customer_name)
				  ->setCellValue('E'.$i, $email)
				  ->setCellValue('F'.$i, $phone)
				  ->setCellValue('G'.$i, $status)
				  ->setCellValue('H'.$i, $payment)
				  ->setCellValue('I'.$i, $shipDate)

				  ->setCellValue('O'.$i, $sub_totalPrice)
				  ->setCellValue('P'.$i, $DiscountCode)
				  ->setCellValue('Q'.$i, $DiscountAmount)
				  ->setCellValue('R'.$i, $totalPrice);


				foreach ($order->getCustomerOrderDeliverys() as $deliveryAddress){
					 $address = $deliveryAddress->getAddress();
					 $district = $deliveryAddress->getDistrict();
					 $province=$deliveryAddress->getProvince();
					 //$country = $deliveryAddress->getCountry();
					 $postCode = $deliveryAddress->getPostCode();

					 $full_address = $address.",".$district.",".$province.",".$postCode;
					 $this->spreadsheet->setActiveSheetIndex($this->index)->setCellValue('J'.$i,$full_address);
				}

				$order_items = $repo_order_item->findByCustomerOrder($order);
				if($order_items){
					foreach ($order_items as $items) {
						$itemName = $items->getProductTitle();
						$itemPrice = $items->getPrice();
						$itemQuantity = $items->getQuantity();
						$itemAmount = $items->getAmount();

					  	$this->spreadsheet->setActiveSheetIndex($this->index)
							->setCellValue('K'.$i, $itemName)
							->setCellValue('L'.$i, $itemPrice)
							->setCellValue('M'.$i, $itemQuantity)
							->setCellValue('N'.$i, $itemAmount);
						$i++;
					}
				}

				 $i++;
				 $order_no++;

		}//endfor
		 $this->spreadsheet->getActiveSheet($this->index)->setCellValue('Q'.$i,'Total');
		 $this->spreadsheet->getActiveSheet($this->index)->setCellValue('R'.$i,'=SUM(R'.$start.':R'.$i.')');

	}
	public function setDataExcelOrderAndItems($orders,$payment_bank_transfer)
	{
		$i=2; //row number order
		$order_no=1; //order index
		$j = 5;//row number item
		$item_no = 1;//item index
		if(!$orders){
			throw $this->createNotFoundException('This data doesn\'t exist');
		}


		foreach ($orders as $order){
				$status = '=IF('.$order->getPaid().'=1,"paid","unpaid")';
			$this->spreadsheet->setActiveSheetIndex(0)
				  ->setCellValue('A'.$i, $order_no)
				  ->setCellValue('B'.$i, $order->getOrderNumber())
				  ->setCellValue('C'.$i, $order->getOrderDate()->format('d F Y'))
				  ->setCellValue('D'.$i, $order->getUser()->getFirstName()." ".$order->getUser()->getLastName())
				  ->setCellValue('E'.$i, $status)
				  ->setCellValue('F'.$i, $order->getPaymentOption())
				  ->setCellValue('G'.$i, $order->getShipDate()->format('d F Y'))
				  ->setCellValue('H'.$i, $order->getTotalPrice());

				 $i++;
				 $order_no++;



			  $this->spreadsheet->createSheet();
			  $this->spreadsheet->setActiveSheetIndex(1)->setTitle('product');

			foreach ($order->getCustomerOrderItems() as $items){
			$this->spreadsheet->setActiveSheetIndex(1)
					 ->setCellValue('A'.$j, $item_no)
					 ->setCellValue('B'.$j, $items->getProductTitle())
					 ->setCellValue('C'.$j, $items->getPrice())
					 ->setCellValue('D'.$j, $items->getQuantity())
					 ->setCellValue('E'.$j, $items->getAmount());
					 $j++;
					 $item_no++;
			 }
		}//endfor

		if ($payment_bank_transfer){
		// 	foreach ($payment_bank_transfer as $payment){
		//
		// 		$this->spreadsheet->setActiveSheetIndex($this->index)
		// 			  ->setCellValue('A'.$j, $item_no)
		// 			  ->setCellValue('B'.$j, $order->getOrderNumber())
		// 			  ->setCellValue('C'.$j, $order->getOrderDate()->format('d F Y'))
		// 			  ->setCellValue('D'.$j, $order->getUser()->getFirstName()." ".$order->getUser()->getLastName())
		// 			  ->setCellValue('E'.$j, $status)
		// 			  ->setCellValue('F'.$j, $order->getPaymentOption())
		// 			  ->setCellValue('G'.$j, $order->getShipDate()->format('d F Y'))
		// 			  ->setCellValue('H'.$j, $order->getTotalPrice());
		// 			 $j++;
		// 			 $item_no++;
		// 	}//endfor
		 }
	}
	public function getHeaderExcelRequestService(){

		$this->spreadsheet->setActiveSheetIndex($this->index)
				->setCellValue('A1', 'No')
				->setCellValue('B1', 'Product name')
				->setCellValue('C1', 'Product Model')
				->setCellValue('D1', 'Serail Number')
				->setCellValue('E1', 'Product Warranty Number')
				->setCellValue('F1', 'Product Type')
				->setCellValue('G1', 'Detail')
				->setCellValue('H1', 'Phone')
				->setCellValue('I1', 'Email')
				->setCellValue('J1', 'Address')
				->setCellValue('K1', 'Create date');

				$this->excelCellColor('A1:K1','523112');
				$this->excelFontColor('A1:K1','FFFFFF');
				$this->excelColunmSize('A','K');
	}
	public function setDataExcelRequestService($requestService)
	{
		$i=2; //row number

		$start = 1;


		if(!$requestService){
			throw $this->createNotFoundException('This data doesn\'t exist');
		}

		foreach ($requestService as $customer_product_fix){
			$customer_name = $customer_product_fix->getFirstName()." ".$customer_product_fix->getLastName();
			$product_name = $customer_product_fix->getProductTitle();
			$product_model = $customer_product_fix->getProductModel();
			$serail_number = $customer_product_fix->getProductSerialNumber();
			$productWarrantyNumber = $customer_product_fix->getProductWarrantyNumber();
			$productTextType =$customer_product_fix->getProductTextType();
			$detail = $customer_product_fix->getRequestDetail();
			$phone = $customer_product_fix->getPhone();
			$email = $customer_product_fix->getEmail();
			$address = $customer_product_fix->getAddress()." ".$customer_product_fix->getSubDistrict()." ".$customer_product_fix->getDistrict()." ".$customer_product_fix->getProvince()." ";
			$address .= $customer_product_fix->getPostCode();
			$create_request = $customer_product_fix->getCreatedAt();

			$this->spreadsheet->setActiveSheetIndex($this->index)
				  ->setCellValue('A'.$i, $start)
				  ->setCellValue('B'.$i, $product_name)
				  ->setCellValue('C'.$i, $product_model)
				  ->setCellValue('D'.$i, $serail_number)
				  ->setCellValue('E'.$i, $productWarrantyNumber)
				  ->setCellValue('F'.$i, $productTextType)
				  ->setCellValue('G'.$i, $detail)
				  ->setCellValue('H'.$i, $phone)
				  ->setCellValue('I'.$i, $email)
				  ->setCellValue('J'.$i, $address)
				  ->setCellValue('K'.$i, $create_request);
			 $i++;
			 $start++;
		}//endfor
	}
}
