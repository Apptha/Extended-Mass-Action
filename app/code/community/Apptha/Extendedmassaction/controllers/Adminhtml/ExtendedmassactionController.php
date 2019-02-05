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

class Apptha_Extendedmassaction_Adminhtml_ExtendedmassactionController extends Mage_Adminhtml_Controller_Action

{
	/**
	 * Productcount
	 * @var Int
	 */
	protected $_productcount=0;
	
	/**
	 * ComplexProductType
	 * @var Int
	 */
	protected $_complexProductType=0;
	
	/**
	 * Function to Load Category id from Catagory Table
	 * 
	 * @param Int Category id
	 * @return Category Record From Catagory Table
	 */
	protected function getCategory($id){
		return Mage::getModel('catalog/category')->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($id);
	}
	
	/**
	 * Function to Load Product id from Product Table
	 * 
	 * @param Int Product id
	 * @return Product Record From Product Table
	 */
	protected function getProduct($id){
		
		return Mage::getModel('catalog/product')->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->load($id);
	}

	/**
	 * Function to Checks whether the Selected Product is already assigned to the Category
	 * 
	 * @param Array $productIds
	 * @param Array $products_position
	 * @return number
	 */
	  protected function isProductVisible($productIds,$products_position){
	  	
		 return count(array_intersect($productIds, array_keys($products_position)));
	  }
	
	/**
	 * Function to Check whether the Given Product Price is valid or not
	 * 
	 * @param Number $price
	 * @return boolean
	 */
	 protected  function isNumeric($price){
		$operator  = $price[0];
		$abs_price = substr($price, 1, -1);
		$symbol    = substr($price,-1);
		$operation = array('+','-');
		if(is_numeric($operator)) $abs_price =$price ;
		
		if(!is_numeric($operator) && $symbol!='%') $abs_price = substr($price,1);
		if((!(in_array($operator, $operation)) && !is_numeric($operator)) || ($symbol =='%' && is_numeric($operator)) || ($symbol!='%' && !is_numeric($symbol)) || !is_numeric($abs_price)){
			return false;
		}
		return true;
	}
	
	/**
	 * Function to Get Message
	 * 
	 * @param Number $nilProduct
	 * @param Number $ids
	 * @param Number $attribute
	 */
	protected function getMessage($nilProduct,$ids,$attribute)
	{
		if($nilProduct){
			$this->_getSession()->addNotice($this->__('Product %s should be 0 or greater',$attribute));
			$this->_getSession()->addError($this->__('Cannot apply percentage for %d record(s)',count($nilProduct)));
		}
		if($ids){
			$this->_getSession()->addNotice($this->__('Product %s should be 0 or greater',$attribute));
			$this->error(count($ids), 'Quantity');
		}
		if($this->_complexProductType && $attribute==='Quantity'){
			$this->_getSession()->addNotice($this->__('Total of %d Bundle(or)Grouped(or)Configurable Product(s) %s cannot be updated',$this->_complexProductType,$attribute));
		}
		elseif($this->_complexProductType){
			$this->_getSession()->addNotice($this->__('Total of %d Bundle(or)Grouped Product(s) %s cannot be updated',$this->_complexProductType,$attribute));
		}
		if($this->_productcount):return $this->success($this->_productcount);endif;
	}
	
	/**
	 * Function to Get Product Type
	 * 
	 * @param Number $id
	 * @return string
	 */
	protected function getProductType($id,$attribute){
		$type = array('bundle','grouped');
		if($attribute==='Quantity'){
			$type[2]='configurable';
		}
		$product      = $this->getProduct($id);
		$productType = $product->getTypeID();
		if(in_array($productType, $type))
		{
			$this->_complexProductType++;
	
			return 'ComplexProductType';
		}
	}
	
	/**
	 * Function to Set update price to the Product
	 * 
	 * @param Int $new_price
	 * @param Int $productPrice
	 * @param $operator
	 * @param $symbol
	 * @return string|boolean|number
	 */
	  protected function setPrice($new_price,$productPrice,$operator,$symbol){
		
		$percent = $new_price/100;
		if($symbol==null){
			
			$newprice=$new_price;
		}
		else{
			
			if($productPrice == 0){
				
				return 'zero';
			}
			$newprice = $productPrice * $percent;
		}
		if(is_numeric($operator)){
			$updatedPrice = $newprice;
		}
		if($operator == '+'){
			$updatedPrice = $productPrice + $newprice;
		}
		if($operator == '-'){
			$updatedPrice = $productPrice - $newprice;
		}
		if($updatedPrice < 0) return 'less than zero';
		else return $updatedPrice;
	}
	
	/**
	 * Function to Add Error Message
	 *
	 * @param Int $product_id
	 * @param String $attribute
	 * @return String
	 */
	protected function error($productCount,$attribute){
		if($productCount):
		return $this->_getSession()->addError(
				$this->__('Total of %d record(s) not Updated',$productCount)
		);
		else:
		return $this->_getSession()->addError(
				$this->__('An error occurred while updating the product(s) status.')
		);
		endif;
	}
	
	/**
	 * Function to Add Success Message
	 *
	 * @param Int $product_count
	 * @return String
	 */
	protected function success($product_count){
		return $this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been updated.', $product_count)
		);
	}
	
	/**
	 * Function to Assign selected Products to the Category
	 */
	 public function assignCategoryAction()
	 {
		$productIds = (array)$this->getRequest()->getParam('product');
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		$categoryId = $this->getRequest()->getParam('category');
		$ids = array();
		if (!empty($productIds))
		{
			//Load the Category
			$category 		   = $this->getCategory($categoryId);
			$products_position = $category->getProductsPosition();
			$visible 		   = $this->isProductVisible($productIds,$products_position);
				foreach ($productIds as $id)
				{
					if (in_array($id,array_keys($products_position)))
					{
						array_push($ids, $id);
					}
					else
					{
					$products_position[$id] = 1;//you can put any other position number instead of 1.
					}
				}
				$category->setPostedProducts($products_position);
				$category->save();
				if(!empty($ids)){
					$this->_getSession()->addError(
							$this->__('Total of %d record(s) already assigned to the Given Category',count($ids))
					);
				}
				if(count($productIds)-$visible):$this->success(count($productIds)-$visible);endif;
		}
		else
		{
			$this->error();
		}
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}
	
	/**
	 * Function to Remove Products from the Category
	 */
	public function removeCategoryAction()
	{
		$productIds = (array)$this->getRequest()->getParam('product');
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		$categoryId = $this->getRequest()->getParam('removecategory');
		$ids = array();
		if (!empty($productIds))
		{
			foreach ($productIds as $id) {                     //Load the Products
				$product = $this->getProduct($id);             //Getting all category ids
				$categories = $product->getCategoryIds();
				$key = array_search($categoryId, $categories); //Searching array key with value $categories and removing from array
				
				if($key === false){
					array_push($ids, $id);
				}
				else {   							 
					$this->_productcount++;
					unset($categories[$key]);
					$product->setCategoryIds($categories);
					$product->save();
				}
				
			}
			if(!empty($ids)){
				$this->_getSession()->addError(
						$this->__('Unable to Remove %d record(s)',count($ids))
				);
			}
			if($this->_productcount):$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been removed.', $this->_productcount)
			);endif;
		}
		else
		{
			$this->error();
		}
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}

	/**
	 * Function to Create specialPrice to the selected Products
	 */
	public function specialPriceAction()
	{
		$productIds   = (array)$this->getRequest()->getParam('product');
		$storeId      = (int)$this->getRequest()->getParam('store', 0);
		$updatePrice = $this->getRequest()->getParam('specialprice');
		$fromDate     = $this->getRequest()->getParam('from_date');
		$toDate       = $this->getRequest()->getParam('to_date');
		$ids = array();
		$nilOffer = array();
		if (!empty($productIds))
		{
			if(!$this->isNumeric($updatePrice))
			{
				$this->_getSession()->addError($this->__('Invalid Special Price'));
			}
			else 
			{
				$symbol   = substr($updatePrice,-1);
				$operator = $updatePrice[0];
				foreach ($productIds as $id) {
					$product      = $this->getProduct($id);
					$productSpecialPrice = $product->getSpecialPrice();
					$type = $this->getProductType($id,'SpecialPrice');
					if($type !== 'ComplexProductType')
					{		if($symbol == '%')
							{
								$update_price = substr($updatePrice, 1,-1);
								$specialprice = $this->setPrice($update_price,$productSpecialPrice,$operator,$symbol);
								if ($specialprice === 'zero'){
									array_push($nilOffer, $id);
									continue;
								}
								else if($specialprice === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($specialprice >= 0){
									$this->_productcount++;
									$product->setSpecialPrice($specialprice);
									$product->setSpecialFromDate($fromDate);
									$product->setSpecialFromDateIsFormated(true);
									$product->setSpecialToDate($toDate);
									$product->setSpecialToDateIsFormated(true);
								}
							}
							else
							{
								if(is_numeric($operator)){
									$update_price = $updatePrice;
								}
								else{
									$update_price = substr($updatePrice, 1);
								}
								$specialprice = $this->setPrice($update_price,$productSpecialPrice,$operator,null);
								if($specialprice === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($specialprice >= 0){
									$this->_productcount++;
									$product->setSpecialPrice($specialprice);
									$product->setSpecialFromDate($fromDate);
									$product->setSpecialFromDateIsFormated(true);
									$product->setSpecialToDate($toDate);
									$product->setSpecialToDateIsFormated(true);
								}
							}
							$product->save();
					}
				}
				$this->getMessage($nilOffer,$ids,'SpecialPrice');
			}
		}
		else
		{
			$this->error();
		}
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}
	

	/**
	 * Function to Update Price to the selected Products
	 */
	public function updatePriceAction()
	{
		$productIds   = (array)$this->getRequest()->getParam('product');
		$storeId      = (int)$this->getRequest()->getParam('store', 0);
		$updatePrice  = $this->getRequest()->getParam('updateprice');
		$ids = array();
		$nilProduct = array();
		if (!empty($productIds))
		{
			if(!$this->isNumeric($updatePrice))
			{
				$this->_getSession()->addError($this->__('Invalid Price'));
			}
			else
			{
				$symbol   = substr($updatePrice,-1);
				$operator = $updatePrice[0];
				foreach ($productIds as $id) {
					$product      = $this->getProduct($id);
					$productPrice = $product->getPrice();
					$type = $this->getProductType($id,'Price');
					if($type !== 'ComplexProductType')
					{		if($symbol == '%')
							{
								$update_price = substr($updatePrice, 1,-1);
								$price = $this->setPrice($update_price,$productPrice,$operator,$symbol);
								if ($price === 'zero'){
									array_push($nilProduct, $id);
									continue;
								}
								else if($price === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($price >= 0){
									$this->_productcount++;
									$product->setPrice($price);
								}
							}
							else
							{
								if(is_numeric($operator)){
									$update_price = $updatePrice;
								}
								else{
									$update_price = substr($updatePrice, 1);
								}
								$price = $this->setPrice($update_price,$productPrice,$operator,null);
								if($price === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($price >= 0){
									$this->_productcount++;
									$product->setPrice($price);
								}
							}
							$product->save();
					}
				}
				$this->getMessage($nilProduct,$ids,'Price');
			}
		}
		else
		{
			$this->error();
		}
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}
	
	/**
     * Function to Update the catalog stock item by product Id
     * 
     * @return  nothing     
     */
	public function updateQuantityAction()
	{
		$productIds     = (array)$this->getRequest()->getParam('product');
		$storeId        = (int)$this->getRequest()->getParam('store', 0);
		$updateQuantity = $this->getRequest()->getParam('updatequantity');
		$ids = array();
		$nilProduct = array();
		if (!empty($productIds))
		{
		if(!$this->isNumeric($updateQuantity))
			{
				$this->_getSession()->addError($this->__('Invalid Quantity'));
			}
			else
			{
				$symbol   = substr($updateQuantity,-1);
				$operator = $updateQuantity[0];
				foreach ($productIds as $id) {
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
					$productQuantity = $stockItem->getQty();
					$type = $this->getProductType($id,'Quantity');
					if($type !== 'ComplexProductType')
					{		if($symbol == '%')
							{
								$update_quantity = substr($updateQuantity, 1,-1);
								$quantity = $this->setPrice($update_quantity,$productQuantity,$operator,$symbol);
								if ($quantity === 'zero'){
									array_push($nilProduct, $id);
									continue;
								}
								else if($quantity === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($quantity >= 0){
									$this->_productcount++;
									$stockItem->setData('qty', $quantity);
									$stockItem->setData('is_in_stock', $quantity ? 1 : 0);
								}
							}
							else
							{
								if(is_numeric($operator)){
									$update_quantity = $updateQuantity;
								}
								else{
									$update_quantity = substr($updateQuantity, 1);
								}
								$quantity = $this->setPrice($update_quantity,$productQuantity,$operator,null);
								if($quantity === 'less than zero'){
									array_push($ids, $id);
								}
								elseif($quantity >= 0){
									$this->_productcount++;
									$stockItem->setData('qty', $quantity);
									$stockItem->setData('is_in_stock', $quantity ? 1 : 0);
								}
							}
							$stockItem->save();
					}
				}
				$this->getMessage($nilProduct,$ids,'Quantity');
			}
		}
		else
		{
			$this->error();
		}
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}
	
	
	/**
	 * Function to change stock availability by product Id
	 * 
	 * @return  nothing  
	 */
	public function stockStatusAction()
	{
		$productIds = (array)$this->getRequest()->getParam('product');
		$storeId    = (int)$this->getRequest()->getParam('store', 0);
		$status     = (int)$this->getRequest()->getParam('stockstatus');
		$ids = array();
		$outofstock = array();
		if (!empty($productIds))
		{
			foreach ($productIds as $id)
			{
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
					$quantity = $stockItem->getQty();
					$type = $this->getProductType($id);
					$product_stock = $stockItem->getData('is_in_stock');
					$stockItem->setData('manage_stock', 1);// should be 1 to make something out of stock
					if($status ==1) {
							if($product_stock == 1){
								array_push($ids, $id);
							}
							elseif($type === 'ComplexProductType')
							{
								$this->_productcount++;
								$stockItem->setData('is_in_stock', 1);
							}
								
							elseif($quantity <= 0){
								array_push($outofstock, $id);
							}
							else{
								$this->_productcount++;
								$stockItem->setData('is_in_stock', 1);
							}
					}
					if($status ==2){
						if($product_stock == 0){
							array_push($ids, $id);
						}
						else{
							$this->_productcount++;
							$stockItem->setData('is_in_stock', 0);
						}
					}
					$stockItem->save();
			}
			if($outofstock){
				$this->_getSession()->addNotice($this->__('Product Quantity should be 0 or greater'));
				$this->error(count($outofstock), 'Quantity');
			}
			if(!empty($ids)){
				$this->_getSession()->addError(
						$this->__('Total of %d record(s) already assigned to the given stock status',count($ids))
				);
			}
			if($this->_productcount):$this->success($this->_productcount);endif;
		}
		else
		{
			$this->error();
		}
	
		$this->_redirect("adminhtml/catalog_product", array('store'=> $storeId));
	}
	
	
}


?>

