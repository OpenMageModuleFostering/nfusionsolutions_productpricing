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
 * ProductPrice default helper
 *
 * @category    Nfusionsolutions
 * @package     Nfusionsolutions_ProductPricing
 * @author      Ultimate Module Creator
 */
class Nfusionsolutions_ProductPricing_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function getBaseCurrency(){
		$baseCurrency =  Mage::app()->getStore()->getBaseCurrencyCode();
		return $baseCurrency;
    }
	
    public function getToken(){
		$token = Mage::getStoreConfig('nfusionsolutions_productpricing/productprice/api_token');
        return $token;
    }
	
    public function getTenantAlias(){
		$tenantAlias = Mage::getStoreConfig('nfusionsolutions_productpricing/productprice/tenant_alias');
        return $tenantAlias;
    }
	
    public function getSalesChannel(){
		$salesChannel = Mage::getStoreConfig('nfusionsolutions_productpricing/productprice/sales_channel');
        return $salesChannel;
    }
	
	public function getUrl(){ 
		$baseCurrency = $this->getBaseCurrency();
		$token = $this->getToken();
		$tenantAlias = $this->getTenantAlias();
		$salesChannel = $this->getSalesChannel();		
        return $url_call = 'https://'.$tenantAlias.'.nfusioncatalog.com/service/price/PricesByChannel?currency='.$baseCurrency.'&channel='.$salesChannel.'&withretailtiers=true&token='.$token;		
    }
	
}
