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
class Apptha_Extendedmassaction_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	protected $_massactionBlockName = 'extendedmassaction/adminhtml_widget_grid_massaction';
	
	
    public function __construct()
    {
    	parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        $_helper = Mage::helper('extendedmassaction');
        if(Mage::getStoreConfigFlag('extendedmassaction_options/messages/active')){	
       		 $this->setTemplate('apptha/extendedmassaction/widget/grid.phtml');
        }
    }

    /**
     *Function to get store id
     *
     * @return int
     */
     protected function _getStore()
     {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
     }

    /**
     *Function to prepare collection for admin product grid
     *
     * @return array
     */
	 protected function _prepareCollection()
     {	
    	Mage::dispatchEvent('extendedmassaction_adminhtml_catalog_product_grid_prepare_collection', array('collection' => $this));
  		return parent::_prepareCollection(); 
      }

     /**
      *Function to add columns to admin product grid
      *
      * @return array
      */
	  protected function _prepareColumns()
   	  {
   	  	$_helper = Mage::helper('extendedmassaction');
   	  	if(Mage::getStoreConfigFlag('extendedmassaction_options/messages/active')){
    	Mage::dispatchEvent('extendedmassaction_adminhtml_catalog_product_grid_prepare_column', array('block' => $this));
        Mage::dispatchEvent('extendedmassaction_adminhtml_catalog_product_grid_prepare_column_after', array('block' => $this));
   	  	}else{
   	  		parent::_prepareColumns();
   	  	}
        return $this;
      }

    /**
     *Function to prepare mass action
     *
     * @return Extendedmassaction_Adminhtml_Widge
     */ 
	 protected function _prepareMassaction() {

        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
            $this->getMassactionBlock()->addItem('attributes', array(
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
            ));
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));

        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
  
}
