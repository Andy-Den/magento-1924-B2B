<?php

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class FVets_Tableprice_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
	/**
	 * Save item tableprice
	 */
	public function saveItemTablepriceAction()
	{
		if ($order = $this->_initOrder()) {
			try {
				$response = false;
				$tableprice = $this->getRequest()->getPost('tableprice');//($this->getRequest()->getPost('tableprice') == 0) ? NULL : $this->getRequest()->getPost('tableprice');
				$item_id = $this->getRequest()->getParam('item_id');

				Mage::getModel('sales/order_item')->load($item_id)
					->setTableprice($tableprice)
					->save();

				$response = array(
					'error'     => false,
					'message'   => $this->__('Tabela de preço alterada.')
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
					'message'   => $this->__('Cannot change item tableprice.')
				);
			}

			if (is_array($response)) {
				$response = Mage::helper('core')->jsonEncode($response);
				$this->getResponse()->setBody($response);
			}
		}
	}
}