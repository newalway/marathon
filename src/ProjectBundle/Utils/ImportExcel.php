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


use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Color;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;

class ImportExcel
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

	public function importExcelFile($filename)
	{
		$valid = false;
		$types = array('Excel2007', 'Excel5');
			foreach ($types as $type) {
			$reader = PHPExcel_IOFactory::createReader($type);
				if ($reader->canRead($filename)) {
					$valid = true;
					break;
				}
			}

		if ($valid) {
		// TODO: load file
		// e.g. PHPExcel_IOFactory::load($file_path)
		// Tosses exception
		//$reader = PHPExcel_IOFactory::createReaderForFile($filename);

		// Need this otherwise dates and such are returned formatted
		/** @noinspection PhpUndefinedMethodInspection */
		$reader->setReadDataOnly(true);

		//$reader->enableMemoryOptimization(); //Call to undefined method

		// Just grab all the rows
		$wb = $reader->load($filename);
		$ws = $wb->getSheet(0);
		$rows = $ws->toArray();
			return  $rows;
		}else {
			return $valid;
		}

	}
	public function excelDateFormat($date){
		//excel send $date is a unix date format
		if(!empty($date)){
			if(PHPExcel_Shared_Date::isDateTimeFormatCode($date)){
				$newdate = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($date));
			}else{
				$newdate = (new \DateTime())->format('Y-m-d');
				//
			}

		}else{
			$newdate = (new \DateTime())->format('Y-m-d');
		}


		return $newdate;
	}

	public function validateRow($rowArr){
		// dump($rowArr);
		//
		//
		if(is_null($rowArr)){

		}


		return $rowArr;
	}


}
