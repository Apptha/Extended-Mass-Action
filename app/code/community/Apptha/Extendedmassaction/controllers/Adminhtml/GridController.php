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
class Apptha_Extendedmassaction_Adminhtml_GridController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
	{
	
		$id = (int)$this->getRequest()->getPost('id');
		$name = $this->getRequest()->getPost('name');
		$index = $this->getRequest()->getPost('index');
		$model = Mage::getModel('catalog/product');
		$model_qty = Mage::getModel('cataloginventory/stock_item');
		$value = $model->load($id);
		$value_qty = $model_qty->loadByProduct($id);
		
		$product_sku = $value->getIdBySku($name);
		$product_type = $value->getTypeId();
		$type_list = array('grouped','bundle','configurable');
		$product_price = array('grouped','bundle');
		if($index == 'qty'){
			if(in_array($product_type,$type_list)){
				$msg_qty='error';
			}elseif(!is_numeric($name)) {
				$msg = 'error';
				
			}elseif($name==0){
				$name = '<div style="color:#FFF; padding-left:10px;font-weight:bold;background:#F55804;border-radius:8px;width:75%">'
						.$name;
				$value_qty->setData($index,$name);
			}else{
				$name= floor($name);
				if($name==0){
					$name = '<div style="color:#FFF; padding-left:10px;font-weight:bold;background:#F55804;border-radius:8px;width:75%">'
							.$name;
					$value_qty->setData($index,$name);
				}else{
					$value_qty->setData($index,$name);
				}
			$value_qty->save();
			}
		}elseif($index == 'price'){
			if(in_array($product_type,$product_price)){
				$msg_price='error';
			}elseif(!is_numeric($name) || $name <0 ) {
				$msg = 'error';
			
			}else{
				$value->setData($index,$name);
			}
		}elseif($index == 'sku'){
			if ($product_sku){
				$msg_sku = 'error';
			}
			else{
				$value->setData($index,$name);
			}
		}
		else{
			$value->setData($index,$name);
		}
		$value->save();
		
		$edit_list = array('status','visibility');
		if(in_array($index, $edit_list)){
			$label = $model->getResource()->getAttribute($index)->getSource()->getOptionText($name);
			
			if($label==null){
				$lab_val= '';
				$jsonData = json_encode(array('name'=>$lab_val));
			}else{
				$jsonData = json_encode(array('name'=>$label));
			}
		}else{
			$response=array('name'=>$name,'status'=>$msg,'msg_qty'=>$msg_qty,'msg_sku'=>$msg_sku,'msg_price'=>$msg_price);
			$jsonData = json_encode($response);
		}
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($jsonData);
	
	

	}
	
	
	/*
	 * Form Action
	 * 
	 * 
	 */
	public function columnAction() {
			$columns= $this->getRequest()->getPost('columns');
			$encode=json_encode($columns);
			$model = Mage::getModel('extendedmassaction/extendedmassaction');
			$hasData = $model->load('enabled_attributes','key');
			if($hasData->getId()){
				$hasData->setValue($encode);
				$hasData->save();
			}else{
				$model->setKey('enabled_attributes')
						->setValue($encode);
				$model->save();
			}
			$this->_getSession()->addSuccess($this->__('The column option has been updated'));
			$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId)); 
	}
	

}


?>