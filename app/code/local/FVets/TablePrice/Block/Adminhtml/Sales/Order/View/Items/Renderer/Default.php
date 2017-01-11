<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 *
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Order items default renderrer
 */
if(Mage::helper('core')->isModuleEnabled('FVets_Sales')){
	class FVets_TablePrice_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default_Tmp extends FVets_Salesrep_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default {}
} else {
	class FVets_TablePrice_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default_Tmp extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default {}
}

class FVets_TablePrice_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default
	extends FVets_TablePrice_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default_Tmp
{

	private $_customer = null;
	private $_group = null;

	protected function _prepareLayout()
	{
		$onchange = "submitAndReloadArea($('sales_order_item_{item_id}').parentNode, '".$this->getTablepriceSubmitUrl()."')";
		$select = $this->getLayout()->createBlock('widgets/adminhtml_select')
			->setData(array(
				'label'   		=> Mage::helper('sales')->__('Save Tableprice'),
				'class'   		=> 'save',
				'onchange' 		=> $onchange,
				'element_name'	=> 'tableprice'
			))
			->setOptions(
				$this->getTablepriceOptions()
			);
		$this->setChild('submit_tableprice_select', $select);
		return parent::_prepareLayout();
	}

	public function getTablepriceSubmitUrl()
	{
		return $this->getUrl('*/*/saveItemTableprice', array('order_id'=>$this->getOrder()->getId(), 'item_id' => '{item_id}'));
	}

	private function getTablepriceOptions()
	{

		$options  = array(
			$this->__('Please select')
		);

		if ($this->getGroup()->getMultipleTable())
		{
			foreach (Mage::getModel('fvets_tableprice/tableprice')->getCollection()
				->addFieldToFilter('customer_group_id', $this->getGroup()->getId()) as $tableprice)
			{
				$options[$tableprice->getIdErp()] = $tableprice->getName();
			}
		}
		else
		{
			$options[$this->getGroup()->getIdTabela()] = $this->getGroup()->getIdTabela();
		}

		return $options;

		/*return Mage::getModel('fvets_tableprice/tableprice')->getCollection()
					->toOptionArray();*/

	}

	public function getTablepriceItemSelectHtml($item)
	{
		$return = str_replace('{item_id}', $item->getId(), $this->getChildHtml('submit_tableprice_select'));

		$return = str_replace('{'.$item->getTableprice().'}', 'selected="selected"', $return);

		return $return;
	}

	protected function getCustomer()
	{
		if (!isset($this->_customer))
		{
			$this->_customer = Mage::getModel('customer/customer')->load($this->getOrder()->getCustomerId());
		}
		return $this->_customer;
	}

	protected function getGroup()
	{
		if (!isset($this->_group))
		{
			$this->_group = Mage::getModel('customer/group')->load($this->getCustomer()->getGroupId());
		}
		return $this->_group;
	}
}