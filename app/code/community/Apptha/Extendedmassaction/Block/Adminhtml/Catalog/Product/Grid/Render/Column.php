
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

class Apptha_Extendedmassaction_Block_Adminhtml_Catalog_Product_Grid_Render_Column extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Render for collections
	 * 
	 * @param Varien_Object
	 * @return  mixed
	 */
	
	public function render(Varien_Object $row)
	{
			return $this->_getValue($row,$this->getColumn()->getData('index'));
	}
	
	/**
	 *Function to get values for admin grid
	 *  
	 *@param Varien_object $row,$index
	 *@return mixed
	 */
	
	protected function _getValue(Varien_Object $row,$index)
	{	
		$model = Mage::getModel('catalog/product');
		$product=$model->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($row->getEntityId());
		$attribute_details = Mage::getSingleton("eav/config")->getAttribute("catalog_product",$index);
		$media=$attribute_details->getData('frontend_input');

		 $filter_attributes=array(
				'country_of_manufacture',
				'page_layout',
				'tax_class_id',
		);
		  if($index == in_array($index, $filter_attributes) ){
		  	
		  	$label = $model->getResource()->getAttribute($index)->getSource()->getOptionText($product->getData($index));
		  	return $label;
		 } 
		
	 	 else  if($index==$this->columnIndex($index)){ 
	 	 	
	 	 	return $this->getDatas($row,$this->getColumn()->getData('index'));
		 }
		else if($media=='media_image'){
			$product = Mage::getModel('catalog/product')->load($row->getEntityId());
			try{
				
				$url = Mage::helper('catalog/image')->init($product, $index)->resize(300);
			
			}catch(Exception $e){
				
				return	$url = $product->getSkinUrl('images/placeholder/thumbnail.jpg');
			}
			
			$frontLink = $product->getProductUrl();
			return $value ="<img src=". $url." width='100px'/>";
			
		}
		 else if($media=='multiselect'){
			
			$value = $this->getMultiSelectValues($row,$index);
		}
		 else if($media=='boolean'){
		 	$value= $product->getData($index);
		 	if($value==1)
		 		return "Yes";
		 	else 	
		 		return "No";
		}
		else if($media=='select'){
				
			$model = Mage::getModel('catalog/product');
		$product=$model->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($row->getEntityId());
		$label = $model->getResource()->getAttribute($index)->getSource()->getOptionText($product->getData($index));
		
		return $label;
		} 
		else if($media=='weee'){
		
		 $product=$product->getData($index);
		if(!$product==''){
			 echo '<table>
					<th>Country  </th>
					<th>Tax </th>';
			 	
			 foreach ($product as $tax){
			 	
			 	echo '<tr>
							<td>'.$this->getCountryLabel($tax['country']).'</td>
							<td>'.$tax['value'].'</td>
						  </tr>';
			 }echo '</table>';
			}
		}
		else{
			
			return $product->getData($index);
		 }
		
	}
	
	/**
	 * Function to get country front end text
	 * 
	 * @param String $country
	 * @return String
	 */
	protected function getCountryLabel($country){
		
		$values = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
		
		foreach ($values as $countries){
			if($countries["value"] == $country):return $countries["label"];endif;
		}
		
	}
	/**
	 *Function to filter column index in admin grid
	 * 
	 * @param String $index
	 * @return bool
	 */
	 public function columnIndex($index) {
	
		$filter_index=array(
				'quick',
				'thumbnail',
				'category_list',
				'qty',
				'type_id',
		);
		return in_array($index, $filter_index);
	} 
	
	/**
	 * Function to get datas for custom column
	 * 
	 * @param Varien_Object $row,$index
	 * @return string
	 */
	
	protected function getDatas(Varien_Object $row,$index){
	
	switch ($index) {
			case 'qty':
				$product = Mage::getModel('catalog/product')->load($row->getEntityId());
				$product_type = $product->getTypeId();
				$value = (int)$row->getQty();
				if($product_type == 'simple'){
					if($value==0){
						$value = '<div style="color:#FFF; padding-left:10px;font-weight:bold;background:#F55804;border-radius:8px;width:75%">'
								.$value.'</div>';}
				}
				break;

			case 'quick':
				$value = '<a href="'.$row->getProductUrl().'" target="'._blank.'">'
							.'<img src="'.$this->getSkinUrl('images/apptha/quick-link.png').'" />'
						 .'</a>';
				break;

			case 'thumbnail':
				$product = Mage::getModel('catalog/product')->load($row->getEntityId());
				try{
					$url = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(300);
						
				}catch(Exception $e){
					$url = $this->getSkinUrl('images/placeholder/thumbnail.jpg');
				}
				$frontLink = $product->getProductUrl();
				$value ='<a href="'.$frontLink.'" target="'._blank.'">'. "<img src=". $url." width='100px'/>".'</a>';
				break;
			
			case 'category_list':
				$value = $this->getCatagories($row);
				break;
				 
			case 'type_id':
				$model = Mage::getModel('catalog/product');
				$product=$model->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($row->getEntityId());
				$options= Mage::getModel('catalog/product_type')->getOptionArray();
				foreach ($options as $key=>$values){
					if($product->getData('type_id')==$key){
						echo $values;
					}
				} 
		}
		
		return $value;
	}
	
	/**
	 * Function to get labels of multiselect options type
	 * 
	 * @param $row
	 * @param $index
	 */
	public function getMultiSelectValues($row,$index){
		
		$model = Mage::getModel('catalog/product');
		$product=$model->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($row->getEntityId());
		$label =(array)$model->getResource()->getAttribute($index)->getSource()->getOptionText($product->getData($index));
		foreach ($label as $labels){
			
			echo $labels;
			echo "<br>";
		}
		
	}
	/**
	 *Function to get catagories of product
	 * 
	 * @param  int $product_id
	 * @return string
	 */
	protected function getCatagories($product_id){

		$categories = $product_id->getCategoryIds();
		$allCategory = '<ul class="list catagories">';
		foreach($categories as $key => $category)
		{
			$loadCategory = Mage::getModel('catalog/category')->load($category);
			$allCategory.= '<li>'.$loadCategory->getName().'</li>';
		}

		$allCategory .= '</ul>';

		return $allCategory;
	} 

}