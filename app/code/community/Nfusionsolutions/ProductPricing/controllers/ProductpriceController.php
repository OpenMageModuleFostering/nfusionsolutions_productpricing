<?php
/**
 * Nfusionsolutions_ProductPricing extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Nfusionsolutions
 * @package        Nfusionsolutions_ProductPricing
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * ProductPrice front contrller
 *
 * @category    Nfusionsolutions
 * @package     Nfusionsolutions_ProductPricing
 * @author      Ultimate Module Creator
 */
class Nfusionsolutions_ProductPricing_ProductpriceController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction(){ 
		
		//Check if script is run by hosted server itself
		if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
		die("cannot run!");
		} 
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_decimal');
		$time_start = microtime(true);
		
		$_helper = Mage::helper('nfusionsolutions_productpricing'); 
		 
		$url_call = $_helper->getUrl();
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url_call);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json','Cache-Control: no-cache','Pragma: no-cache'));  
		$result = curl_exec($ch); 
		curl_close($ch); 
		
		//Decode the API response
		$rateData = json_decode($result);
		//echo '<pre>';
		//print_r($rateData);die;
		if(count($rateData) > 0){
			$proCount = array();
			//$action = Mage::getSingleton('catalog/resource_product_action');
			foreach($rateData as $k=>$v){
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
				$enitity_id = '';
				if($v->Ask != ''){
					$select    = $read->select()->from('catalog_product_entity', array('entity_id'))->where('sku=?', $v->SKU);
					$rowArray  = $read->fetchRow($select);   //return row
					$enitity_id = $rowArray['entity_id'];
				}				
				if($enitity_id != ''){
					$proCount[$enitity_id] = $enitity_id;
					//Update product price
					
					//$query = "UPDATE {$table} SET value = $v->Ask WHERE entity_id = {$enitity_id} AND attribute_id in (75,80)";		
					//$write->query($query);
					$data = array("value" => $v->Ask);
					$where = "entity_id = {$enitity_id} AND attribute_id in (75,80)"; 	 
					$write->update("catalog_product_entity_decimal", $data, $where);					
					//Check tier exist or not 
					if(isset($v->RetailTiers)){	
						//delete all tier price 
						$write->delete("catalog_product_entity_tier_price", "entity_id=$enitity_id");
								
						//Reset tier price 		
						foreach($v->RetailTiers as $tier_k => $tier_v){								
							//insert tier price 		
							$write->insert(
								"catalog_product_entity_tier_price",
								array("value_id" => NULL,"entity_id" => $enitity_id,"all_groups" => '1',"customer_group_id" => '0',
								"qty" => $tier_v->Quantity,"value" => $tier_v->Ask,"website_id" => '0')
							);
						}
					}   
						
				}
				
			}
			//if(isset($_GET['reindex']) && $_GET['reindex'] == 'yes'){
			if(1){
				$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price');
				$process->reindexAll();
			}
		}
		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start); 
		//echo '<br/><b>Total Execution Time:</b> '.$execution_time.' Sec. for <b>'.count($proCount).'</b> products.';
    }
	
}
