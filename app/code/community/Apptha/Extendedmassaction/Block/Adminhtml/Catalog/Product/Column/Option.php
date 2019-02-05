<?php

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

class Apptha_Extendedmassaction_Block_Adminhtml_Catalog_Product_Column_option extends Mage_Adminhtml_Block_Template
{
	public function _construct(){
		$this->loadAttributeData();
		$this->setPostUrl(Mage::helper("adminhtml")->getUrl('extendedmassaction/adminhtml_grid/column'));
		
	}
	
	
	
	/**
	 * Function to Check if the attribute id visible
	 *
	 * @return bool
	 */
	public function isAttributeVisible(){

		$model = Mage::helper('extendedmassaction');
		$attribute= $model->loadAttributeData(); 
		return $attribute;
	}

	/**
	 * Function to get customly added attribute code
	 * 
	 * @return Array
	 */
	 public function customAttributeDatas(){
	
		$model = Mage::helper('extendedmassaction');
		$custom_attribute= $model->customAttributeValue(); 
		return $custom_attribute;
	}
	

    /**
     * Function to Load Attribute data
     * 
     * @return Apptha_Extendedmassaction_Helper_Data
     */
	 protected function loadAttributeData(){

		$model = Mage::helper('extendedmassaction');
		return $model;
	} 
	
	/**
	 *Function to get the attributes collections
	 * 
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getAttributes(){

		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		
		return $attributes;
	}
	
	/**
	 *Function to check if the attribute id excluded
	 * 
	 * @param String $attribute
	 * @return bool
	 */
	public function isExcludedAttributes($attribute) {
		
		$exclude_attributes=array(
				"msrp",
				"sku_type",
				"group_price",
				"links_purchased_separately",
				"custom_layout_update",
				"msrp_enabled",
				"links_title",
				"samples_title",
				"image_label",
				"thumbnail_label",
				"thumbnail",
				"shipment_type",
				"small_image_label",
				"description",
				"tier_price",
				"price_view",
				"gift_message_available",
				"manufacturer",
				"minimal_price",
				"short_description",
				"msrp_display_actual_price_type",
				"meta_title",
				"meta_keyword",
				"meta_description",
				"image",
				"small_image",
				"media_gallery",
				"gallery",
				"is_recurring",
				"recurring_profile",
				"options_container"
		);
		
		return in_array($attribute, $exclude_attributes);
	}	

	
}