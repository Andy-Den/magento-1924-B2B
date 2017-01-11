<?php

/**
 * Order items default renderrer
 */


class FVets_Salesrep_Block_Adminhtml_Sales_Order_View_Items_Renderer_Default
	extends Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default
{


	protected function _prepareLayout()
	{
		$onchange = "submitAndReloadArea($('sales_order_item_{item_id}').parentNode, '".$this->getSalesrepSubmitUrl()."')";
		$select = $this->getLayout()->createBlock('widgets/adminhtml_select')
			->setData(array(
				'label'   		=> Mage::helper('sales')->__('Save Salesrep'),
				'class'   		=> 'save',
				'onchange' 		=> $onchange,
				'element_name'	=> 'salesrep'
			))
			->setOptions(
				$this->getCustomerSalesrepOptions()
			);
		$this->setChild('submit_select', $select);
		return parent::_prepareLayout();
	}

	public function getSalesrepSubmitUrl()
	{
		return $this->getUrl('*/*/saveItemSalesrep', array('order_id'=>$this->getOrder()->getId(), 'item_id' => '{item_id}'));
	}

	private function getCustomerSalesrepOptions()
	{
		$salesrep = explode(',', Mage::getModel('customer/customer')->load(Mage::registry('sales_order')->getCustomerId())->getFvetsSalesrep());

		$options = array($this->__('Please select'));
		foreach ($salesrep as $rep)
		{
			$rep = Mage::getModel('fvets_salesrep/salesrep')->load($rep);
			if ($rep->getStatus() == '1')
			{
				$options[$rep->getId()] = $rep->getName();
			}
		}
		return $options;
	}

	public function getSalesrepItemSelectHtml($item)
	{
		$return = str_replace('{item_id}', $item->getId(), $this->getChildHtml('submit_select'));

		$return = str_replace('{'.$item->getSalesrepId().'}', 'selected="selected"', $return);

		return $return;
	}
}