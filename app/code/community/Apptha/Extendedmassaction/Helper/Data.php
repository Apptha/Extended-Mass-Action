<?PHP

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Extendedmassaction
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */

class Apptha_Extendedmassaction_Helper_Data extends Mage_Core_Helper_Abstract {
	

	/**
	 * Function to load and decode values from data base
	 * 
	 * @return mixed
	 */
	public function loadAttributeData(){
	
		if(empty($this->_attribute)) {
				
			$model = Mage::getModel('extendedmassaction/extendedmassaction');
			$this->_attribute = json_decode($model->load('enabled_attributes','key')->getValue(),true);
			
		}
		return $this->_attribute;
		
	}	

	/**
	 * Function to check  whether the index is the Grid column
	 * 
	 * @param String $index
	 * @return boolean
	 */
	 public function columnIndex($index) {
		$filter_index=array(
				'quick',
				'thumbnail',
				'category_list',
				'qty',
				'type_id'
		);
		return in_array($index, $filter_index);
	} 
		
	/**
	 * Function to set custom attribute code and label
	 * 
	 * @return array
	 */
	 public function customAttributeValue(){
		
	 	$custom_attribute=array(
				'category_list'=>'Category List',
				'quick'=>'Quick Preview',
				'qty'=>'Qty',
				'thumbnail'=>'Thumbnail',
	 			'type_id'=>'Type'
	 		
		);
		return  $custom_attribute;
	}
	
	/**
	 * Function to get customly added attribute code
	 * 
	 * @param String $custom_attribute_code
	 * @return String
	 */
	 public function customAttributeFrontendLabel($custom_attribute_code){
		
	 	$custom_attribute=$this->customAttributeValue();
		
		return $custom_attribute[$custom_attribute_code];
	}
	
	/**
	 * Function to get the attribute front end label 
	 * 
	 * @param  String $attribute_code
	 * @return String
	 */	
	 public function getAttributeLable($attribute_code) {
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		
			foreach ($attributes as $attribute){
				
				if($attribute->getAttributeCode()==$attribute_code){
					
					$attribute_lable= $attribute->getFrontendLabel($attribute->getAttributeCode());
				}
				
			}
		return $attribute_lable;
	}
	
	
	/**
	 *Function to check whether the attribute code is options type
	 * 
	 * @param String $attribute_code
	 * @return boolean
	 */
	  public function getOptionsFormat($attribute_code) {
		$filter_attributes=array(
				'status',
				'visibility',
		);
	
		return in_array($attribute_code, $filter_attributes);
	}
	
	/**
	 * Function to get options text of selected attribute code
	 * 
	 * @param String $attribute_code
	 * @return array
	 */
	public function getOptions($attribute_code) {
		
		$label=array();
		$option_value=array();
		$config    = Mage::getModel('eav/config');
		$attribute = $config->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute_code);
		$values    = $attribute->setStoreId($storeId)->getSource()->getAllOptions();
		$values=array_filter($values);
		
		if($attribute_code=='country_of_manufacture')
		{
			$values = Mage::getResourceModel('directory/country_collection')
			->loadData()->toOptionArray(false);
		}
		
		  foreach($values as  $value)
		{
			$label[]= $value['label'];
			$option_value[]= $value['value'];
			
		}  
		
		$dropdown=array_combine($option_value,$label);
		$dropdown=array_filter($dropdown);
		
		return $dropdown;	
	}
	/**
	 * Function to get the domain key
	 *
	 * Return domain key
	 * @return string
	 */
	
	public function domainKey($tkey)
	{
		$message = "EM-EMAMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
		$stringLength = strlen($tkey);
		for($i = 0; $i < $stringLength; $i++) {
			$keyArray[] = $tkey[$i];
		}
		$encMessage = "";
		$kPos = 0;
		$charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		$strLen = strlen($charsStr);
		for($i = 0; $i < $strLen; $i++) {
			$charsArray[] = $charsStr[$i];
		}
		$lenMessage = strlen($message);
		$count = count($keyArray);
		for($i = 0; $i < $lenMessage; $i++) {
			$char   = substr($message, $i, 1);
			$offset = $this->getOffset($keyArray[$kPos], $char);
			$encMessage .= $charsArray[$offset];
			$kPos++;
			 
			if ($kPos >= $count) {
				$kPos = 0;
			}
		}
		return $encMessage;
	}
	/**
	 * Function to get the offset 
	 *
	 * Return offset key
	 * @return string
	 */
	 
	public function getOffset($start, $end)
	{
		$charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		$strLen = strlen($charsStr);
		for ($i = 0; $i < $strLen; $i++) {
			$charsArray[] = $charsStr[$i];
		}
		for ($i = count($charsArray) - 1; $i >= 0; $i--) {
			$lookupObj[ord($charsArray[$i])] = $i;
		}
		$sNum   = $lookupObj[ord($start)];
		$eNum   = $lookupObj[ord($end)];
		$offset = $eNum - $sNum;
		if ($offset < 0) {
			$offset = count($charsArray) + ($offset);
		}
		return $offset;
	}
	/**
	 * Function to generate license key
	 *
	 * Return license key
	 * @return string
	 */
	 
	public function genenrateOscdomain()
	{
		$subfolder = $matches = '';
		$strDomainName = Mage::app()->getFrontController()->getRequest()->getHttpHost();
		preg_match("/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
		preg_match("/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder);
		preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder[2], $matches);
		if (isset($matches['domain']))
		{
			$customerurl = $matches['domain'];
		} else {
			$customerurl = "";
		}
		$customerurl = str_replace("www.", "", $customerurl);
		$customerurl = str_replace(".", "D", $customerurl);
		$customerurl = strtoupper($customerurl);
		if (isset($matches['domain']))
		{
			$response = $this->domainKey($customerurl);
		} else {
			$response = "";
		}
		return $response;
	}
}