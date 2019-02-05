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
class Apptha_Extendedmassaction_Block_Adminhtml_Catalog_Product extends Mage_Adminhtml_Block_Catalog_Product
{
	public function __construct()
	{

		$_helper = Mage::helper('extendedmassaction');
		parent::__construct();
		if(Mage::getStoreConfigFlag('extendedmassaction_options/messages/active') ) {
							
			$this->setTemplate('apptha/extendedmassaction/catalog/product.phtml');
		}
	}
	
	/**
	 *Function to prepare button and grid
	 *
	 * @return Apptha_Extendedmassaction_Block_Catalog_Product
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$_helper = Mage::helper('extendedmassaction');
		if(Mage::getStoreConfigFlag('extendedmassaction_options/messages/active')){
			$this->_addButton('column_option', array(
					'label'   => Mage::helper('catalog')->__('Show/Hide Column Option'),
					'onclick' => "showHideColumn()",
					'class'   => 'add'
			));
			
			$this->getLayout()->getBlock('head')->addJs('extendedmassaction/jquery-1.11.1.js');
			$this->getLayout()->getBlock('head')->addCss('apptha/extendedmassaction/boxes.css');
		}
		
		
		return $this ;
	}
}