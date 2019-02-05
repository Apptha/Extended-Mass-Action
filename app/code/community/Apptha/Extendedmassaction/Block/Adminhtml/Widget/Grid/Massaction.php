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
class Apptha_Extendedmassaction_Block_Adminhtml_Widget_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{

	/**
	 * Sets Massaction template
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('apptha/extendedmassaction/widget/grid/massaction.phtml');
	}
	
	public function getJavaScript()
	{
		return 
		
		" var appthaGridMassaction = Class.create(varienGridMassaction, {});"
		." var {$this->getJsObjectName()} = new appthaGridMassaction('{$this->getHtmlId()}', "
		. "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
		. ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
		. "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
		. "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
			. ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
			. ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
			. "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
	}
}
