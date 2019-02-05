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

class Apptha_Extendedmassaction_Model_Observer extends Varien_Event_Observer
{
	

	/**
	 * Function to Edit column to admin product grid
	 *
	 * @param Varien_Event_Observer $observer
	 * @return null
	 */
	public function prepareCollection(Varien_Event_Observer $observer)
	{	
		
	}
	
	/**
	 * Function to add column onload in admin product grid
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function addColumnOnLoad(Varien_Event_Observer $observer){
	
		$columns=array('name','sku','price','status','visibility');
		$encode=json_encode($columns);
		$model = Mage::getModel('extendedmassaction/extendedmassaction');
		$hasData = $model->load('enabled_attributes','key');
		if($hasData->getId()==''){
			$model->setKey('enabled_attributes')
			->setValue($encode);
			$model->save();
		}
			
	}
	/**
	 *Function to edit column in admin product grid
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function editColumn(Varien_Event_Observer $observer){
		$block = $observer->getBlock();

		$edit_list = array('name','price','qty','status','visibility','sku');

		foreach ($block->getColumns() as $_block){
			if(in_array($_block->getIndex(), $edit_list)){
				
					$_block->setIsEditiable(true);
			}
		}
	}
	
	/**
	 * Removes column to admin product grid
	 *
	 * @param Varien_Event_Observer $observer
	 */
	 public function removeColumn(Varien_Event_Observer $observer) {
		$block = $observer->getBlock();
		$block->removeColumn('type'); 
	}
	
	/**
	 *Function to add column to admin product grid
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function appendCustomColumn(Varien_Event_Observer $observer){
		
	
		$block = $observer->getBlock();
		$model = Mage::helper('extendedmassaction');
		$json_decode=$model->loadAttributeData();
		$filter_attributes=array(
				'country_of_manufacture',
				'custom_design',
				'tax_class_id',
		);
		$block->addColumn('action',
				array(
						'header'    => Mage::helper('catalog')->__('Action'),
						'width'     => '50px',
						'type'      => 'action',
						'getter'     => 'getId',
						'actions'   => array(
								array(
										'caption' => Mage::helper('catalog')->__('Edit'),
										'url'     => array(
												'base'=>'*/*/edit',
												'params'=>array('store'=>$block->getRequest()->getParam('store'))
										),
										'field'   => 'id'
								)
						),
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'stores',
				));
		$block->addColumn('entity_id',
				array(
						'header'=> Mage::helper('catalog')->__('ID'),
						'width' => '50px',
						'type'  => 'number',
						'index' => 'entity_id',
				));
		foreach ($json_decode as $attribute_code){
			
			$array=	array(
					'header'=> Mage::helper('catalog')->__($model->getAttributeLable($attribute_code)),
					'width' => '160px',
					'index'=>$attribute_code,
					'renderer' => 'extendedmassaction/adminhtml_catalog_product_grid_render_column',
			);
						$attribute_details = Mage::getSingleton("eav/config")->getAttribute("catalog_product",$attribute_code);
						$input = '';
						
						switch ($attribute_details->getData('frontend_input')){
							
							case 'select':
								if($attribute_code=='page_layout'){
									$block->addColumn($attribute_code,array_merge($array,
											array(
													'type'=>'options',
													'options' =>Mage::getModel('page/source_layout')->getOptions(),
											)));
								}
							
						else if($attribute_code=='country_of_manufacture'){
							
						$block->addColumn($attribute_code,array_merge($array,
											array(
									
									'type'=>'options',
									'index'=>$attribute_code,
									'options' =>$model->getOptions($attribute_code),
									
							))); 
						}
							else if($attribute_code==$model->getOptionsFormat($attribute_code)){
								
								$pop=array_pop($array);
								$block->addColumn($attribute_code,array_merge($array,
										array(
												'type'=>'options',
												'options' =>Mage::getModel('catalog/product_'.$attribute_code)->getOptionArray(),
										)));
							}
							else if($attribute_code=='type_id'){
								
								$block->addColumn($attribute_code,array_merge($array,array(
							  	'type'=>'options',
								'options'=>Mage::getModel('catalog/product_type')->getOptionArray())));  
							}
							else if($attribute_code=='custom_design'){
							
								$block->addColumn($attribute_code,array_merge($array,
										array('filter' => false)));
							}
							else{
								
								$block->addColumn($attribute_code,array_merge($array,array(
										'type'=>'options',
										'options'=>$model->getOptions($attribute_code),
										'renderer' => 'extendedmassaction/adminhtml_catalog_product_grid_render_column',)));
							}
							break;
							case 'multiselect':
								
									$block->addColumn($attribute_code,array_merge($array,array(
										'filter'=>false,
										'renderer' => 'extendedmassaction/adminhtml_catalog_product_grid_render_column',)));
									break;
							case 'text':
							case 'textarea':
							case 'weight':
								$block->addColumn($attribute_code,$array);
								break;
								
							case 'price':
								$block->addColumn($attribute_code,array_merge($array,
										array(
												'type'=>'number'
										)));
								break;
							
							case 'date':
								$block->addColumn($attribute_code,array_merge($array,
										array(
												'type'=>'date'
										)));
								break;
							case 'boolean':
								$block->addColumn($attribute_code,array_merge($array,
								array(
								'renderer' => 'extendedmassaction/adminhtml_catalog_product_grid_render_column',
									'type' => 'options',
									 'options' =>Mage::getModel( 'eav/entity_attribute_source_boolean')->getOptionArray(),
									)));
									break;
							case 'weee':
								$block->addColumn($attribute_code,array_merge($array,
										array('filter' => false,
												)));
								break;
							
								
							case 'media_image':
									$block->addColumn($attribute_code,array_merge($array,
									array('filter' => false,
										)));
									break;
						
						}
						if($attribute_code==$model->columnIndex($attribute_code)){
						
							$custom_attribute_array=array(
									'header'	 =>  Mage::helper('catalog')->__($model->customAttributeFrontendLabel($attribute_code)),
									'width'     => '90px',
									'renderer' => 'extendedmassaction/adminhtml_catalog_product_grid_render_column',
									'index'		 => $attribute_code,
							);
							if($attribute_code=='type_id')
								$block->addColumn($attribute_code,array_merge($custom_attribute_array,array(
										'type'=>'options',
										'options'=>Mage::getModel('catalog/product_type')->getOptionArray())));
						
							else if($attribute_code=='qty')
								$block->addColumn($attribute_code,array_merge($custom_attribute_array,array('type'=>'number')));
							
							else
								$block->addColumn($attribute_code,array_merge($custom_attribute_array,array('filter' => false)));
					}
		}
			
			
	}
	
	
	
   /**
    *Function to add massaction to admin product grid
	*
	* @param Varien_Event_Observer $observer
    *
    */
    public function addMassAction($observer)
    {
    	
    	$block      = $observer->getEvent()->getBlock()->getMassactionBlock();
		$categories = $this->getAllCategories();
		$statuses   = $this->getStockStatus();
		$_helper = Mage::helper('extendedmassaction');
		
		if(Mage::getStoreConfigFlag('extendedmassaction_options/messages/active'))
		{
		        $block->addItem('category', array(
		                    'label'=> 'Assign Category',
		                    'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/assignCategory"),
		                    'additional' => array(
		                            'visibility' => array(
		                                    'name'   => 'category',
		                                    'type'   => 'select',
		                                    'class'  => 'required-entry',
		                                    'label'  => Mage::helper('catalog')->__('Category'),
		                                    'values' => $categories
		                            )
		                    )
		            ));
		        $block->addItem('removecategory', array(
		                    'label'=> 'Remove Category',
		                    'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/removeCategory"),
		                    'additional' => array(
		                            'visibility' => array(
		                                    'name'  => 'removecategory',
		                                    'type'  => 'select',
		                                    'class' => 'required-entry',
		                                    'label' => Mage::helper('catalog')->__('Category'),
		                                    'values'=> $categories
		                            )
		                    )
		            ));
		        $block->addItem('updateprice', array(
		                    'label'=> 'Update Price',
		                    'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/updatePrice"),
		                    'additional' => array(
		                            'visibility' => array(
		                                  'name' => 'updateprice',
		                                  'type' => 'text',
		                                  'class'=> 'required-entry',
		                                  'label'=> Mage::helper('catalog')->__('By eg:(+10,-10,+10%,-10%)'),
		                            )
		                    )
		            ));
		        $block->addItem('specialprice', array(
		        		'label'=> 'Update Special Price',
		        		'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/specialPrice"),
		        		'additional' => array(
		        				'visibility' => array(
		        						'name'  => 'specialprice',
		        						'type'  => 'text',
		        						'class' => 'required-entry',
		        						'label' => Mage::helper('catalog')->__('By eg:(+10,-10,+10%,-10%)'),
		        				),array(
		        						'name'      => 'from_date',
		        						'type'      => 'date',
		        						'gmtoffset' => true,
		        						'format'    => '%d.%m.%Y',
		        						'image'     => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif',
		        						//'class'     => 'required-entry',
		        						'label'     => Mage::helper('catalog')->__('From Date'),
		        				),array(
		        						'name'      => 'to_date',
		        						'type'      => 'date',
		        						'gmtoffset' => true,
		        						'format'    => '%d.%m.%Y',
		        						'image'     => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/grid-cal.gif',
		        						//'class'     => 'required-entry',
		        						'label'     => Mage::helper('catalog')->__('To Date'),
		        				)
		        		)
		        ));
		        $block->addItem('updatequantity', array(
		        		'label'=> 'Update Quantity',
		        		'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/updateQuantity"),
		        		'additional' => array(
		        				'visibility' => array(
		        						'name'  => 'updatequantity',
		        						'type'  => 'text',
		        						'class' => 'required-entry',
		        						'label' => Mage::helper('catalog')->__('By eg:(+10,-10,+10%,-10%)'),
		        				)
		        		)
		        ));
		        $block->addItem('stockstatus', array(
		        		'label'=> 'Change Stock Status',
		        		'url'  => Mage::app()->getStore()->getUrl("extendedmassaction/adminhtml_extendedmassaction/stockStatus"),
		        		'additional' => array(
		        				'visibility' => array(
		        						'name'  => 'stockstatus',
		                                'type'  => 'select',
		                                'class' => 'required-entry',
		                                'label' => Mage::helper('catalog')->__('Status'),
		                                'values'=> $statuses
		        				)
		        		)
		        ));
		}
        
      
    }
    	
	 /**
     *Function to get all categories name and id
     *
     * @return array
     */

   	 protected function getAllCategories(){
   	 	$category = Mage::getModel('catalog/category')->load();
    	$ids = $category->getCollection()->getAllIds();
    	$data = array();
    	if (!empty($ids))
    	{
    		foreach ($ids as $id)
    		{
    			if($id == 1)continue;
    			$cat = Mage::getModel('catalog/category');
    			$cat->load($id);
    			$data['']='';
    			$data[$id] = $cat->getName();
    		}
        }
    	
    	return $data;
    }
    
    /**
     * Function to get stock status
     * 
     * @return array
     */
   	 protected function getStockStatus() {
    	
    	$stock = array(''=>'',1=>'In Stock',2=>'Out of Stock');
    	 
    	return $stock;
    }
}
