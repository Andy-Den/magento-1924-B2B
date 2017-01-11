<?php

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class FVets_Salesrep_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
	/**
	 * Save item salesrep
	 */
	public function saveItemSalesrepAction()
	{
		if ($order = $this->_initOrder()) {
			try {
				$response = false;
				$salesrep_id = $this->getRequest()->getPost('salesrep');
				$item_id = $this->getRequest()->getParam('item_id');

				Mage::getModel('sales/order_item')->load($item_id)
					->setSalesrepId($salesrep_id)
					->save();

				$response = array(
					'error'     => false,
					'message'   => $this->__('Representante alterado.')
				);

			}
			catch (Mage_Core_Exception $e) {
				$response = array(
					'error'     => true,
					'message'   => $e->getMessage(),
				);
			}
			catch (Exception $e) {
				$response = array(
					'error'     => true,
					'message'   => $this->__('Cannot change item salesrep.')
				);
			}

			if (is_array($response)) {
				$response = Mage::helper('core')->jsonEncode($response);
				$this->getResponse()->setBody($response);
			}
		}
	}

	/**
	 * Notify Salesrep
	 */
	public function salesrepEmailAction()
	{

		if ($order = $this->_initOrder())
		{
			$order->setSalesrepEmail(0);

			Mage::helper('fvets_salesrep')->sendRepComissionEmail(null, $order);
		}

		$this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
	}
}