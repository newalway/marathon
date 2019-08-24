<?php
namespace ProjectBundle\Command;
#use Symfony\Component\Console\Command\Command;

#Getting Services from the Service Container
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ProjectBundle\Entity\Product;
use ProjectBundle\Entity\Sku;
use ProjectBundle\Entity\Variant;
use ProjectBundle\Entity\VariantOption;
use ProjectBundle\Entity\SettingOption;

class LowStockReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
	        // the name of the command (the part after "bin/console")
	        ->setName('app:low-stock-report')

	        // the short description shown while running "php bin/console list"
	        ->setDescription('Creates low-stock-report and send email.')
    	;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $this->getContainer()->getParameter('locale');
		$util = $this->getContainer()->get('utilities');
		$product_util = $this->getContainer()->get('app.product');
		$em = $this->getContainer()->get('doctrine')->getEntityManager();

		$repo_setting = $em->getRepository(SettingOption::class);
        $setting_option = $repo_setting->findOneByOptionName('low_stock_report_status');
        $low_stock_report_status = $setting_option->getOptionValue();
		$setting_option = $repo_setting->findOneByOptionName('low_stock_report_min_qty');
        $low_stock_report_min_qty = intval($setting_option->getOptionValue());

		if($low_stock_report_status=='true'){

			$arr_product_low_stock = array();
			$no_low_stock = 0;

			$repository = $em->getRepository(Product::class);
	    	$arr_products = $repository->findAllActiveData(false, $locale)
						->getQuery()
						->getResult();

			if($arr_products){

				foreach ($arr_products as $arr_product) {
					$product = $arr_product[0];
					$varient_count = $arr_product['v_count'];

					if($varient_count>0) {
						//varient product
						$skus = $em->getRepository(Sku::class)->getActiveIsInventoryPolicyByProduct($product, $low_stock_report_min_qty)
							->getQuery()
							->getResult();
						if($skus){
							foreach ($skus as $sku) {
								$tmp_sku_variant_option = $product_util->getArrSkuVariantOption($sku);
								$str_varient = implode(" · ", $tmp_sku_variant_option);
								$arr_product_low_stock[$no_low_stock]['id'] = $product->getId();
								$arr_product_low_stock[$no_low_stock]['sku'] = $sku->getSku();
								$arr_product_low_stock[$no_low_stock]['title'] = $product->getTitle();
								$arr_product_low_stock[$no_low_stock]['varient'] = $str_varient;
								$arr_product_low_stock[$no_low_stock]['qty'] = $sku->getInventoryQuantity();
								$arr_product_low_stock[$no_low_stock]['image_s'] = $sku->getImageSmall();
								$no_low_stock++;
							}
						}

					}else {
						//no varient product
						$policy = $product->getInventoryPolicyStatus();
						if($policy==1){
							$inventory_quantity = $product->getInventoryQuantity();
							if($inventory_quantity <= $low_stock_report_min_qty){
								$arr_product_low_stock[$no_low_stock]['id'] = $product->getId();
								$arr_product_low_stock[$no_low_stock]['sku'] = $product->getSku();
								$arr_product_low_stock[$no_low_stock]['title'] = $product->getTitle();
								$arr_product_low_stock[$no_low_stock]['varient'] = "";
								$arr_product_low_stock[$no_low_stock]['qty'] = $inventory_quantity;
								$arr_product_low_stock[$no_low_stock]['image_s'] = $product->getImageSmall();
								$no_low_stock++;
							}
						}
					}
				}

				if($no_low_stock>0)
				{
					$response = $util->sendMailLowStockReport($arr_product_low_stock, $no_low_stock);
					$output->writeln([
					'There are '.$no_low_stock. ' products in low stock'
					]);
					$output->writeln($response['message']);
				}
			}
		}


    }

}
