<?php

class FVets_Salesrep_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{
	public function __construct()
	{
		parent::__construct();

		$order = $this->getOrder();

		if ($this->_isAllowedAction('emails') && !$order->isCanceled()) {
			$message = Mage::helper('sales')->__('Are you sure you want to send order email to rep?');
			$this->addButton('salesrep', array(
				'label'     => Mage::helper('sales')->__('Send Salesrep Email'),
				'onclick'   => "confirmSetLocation('{$message}', '{$this->getSalesrepEmailUrl()}')",
			));
		}
	}

	public function getSalesrepEmailUrl()
	{
		return $this->getUrl('*/*/salesrepEmail');
	}
}

?>