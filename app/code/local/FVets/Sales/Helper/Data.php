<?php

class FVets_Sales_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function sendCustomerXBrandXProductErrorEmail($customer, $product)
	{
		try {
			$emailTemplate = Mage::getModel('core/email_template');
			$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
			$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
			$emailTemplate->setTemplateSubject(Mage::app()->getWebsite()->getName() . ' - Produto sem representante vinculado para o cliente ' . Mage::getSingleton('customer/session')->getCustomer()->getName());

			$category = Mage::getModel('catalog/category')
				->getCollection()
				->addAttributeToFilter('entity_id', array('in' => $product->getCategoryIds()))
				->addAttributeToFilter('level', '2')
				->getFirstItem();


			$message = '<strong>Categoria:</strong>';
			$message .= '<table border="1">';
			$message .= '<tr>';
			foreach ($category->getData() as $key => $value) {
				$message .= '<th>';
				$message .= $key;
				$message .= '</th>';
			}
			$message .= '</tr>';
			$message .= '<tr>';
			foreach ($category->getData() as $key => $value) {
				$message .= '<td>';
				if (!is_array($value) && !is_object($value))
					$message .= $value;
				$message .= '</td>';
			}
			$message .= '</tr>';
			$message .= '</table><br /><br />';

			$message .= '<strong>Cliente:</strong>';
			$message .= '<table border="1">';
			$message .= '<tr>';
			foreach ($customer->getData() as $key => $value) {
				$message .= '<th>';
				$message .= $key;
				$message .= '</th>';
			}
			$message .= '</tr>';
			$message .= '<tr>';
			foreach ($customer->getData() as $key => $value) {
				$message .= '<td>';
				if (!is_array($value) && !is_object($value))
					$message .= $value;
				$message .= '</td>';
			}
			$message .= '</tr>';
			$message .= '</table><br /><br />';

			$message .= '<strong>Produto:</strong>';
			$message .= '<table border="1">';
			$message .= '<tr>';
			foreach ($product->getData() as $key => $value) {
				$message .= '<th>';
				$message .= $key;
				$message .= '</th>';
			}
			$message .= '</tr>';
			$message .= '<tr>';
			foreach ($product->getData() as $key => $value) {
				$message .= '<td>';
				if (!is_array($value) && !is_object($value))
					$message .= $value;
				$message .= '</td>';
			}
			$message .= '</tr>';
			$message .= '</table>';

			$emailTemplate->setTemplateText($message);

			$emailTemplate->send('douglas@4vets.com.br', 'Douglas', array());
		} catch(Exception $e)
		{

		}
	}

	public function getPrevOrder($current_order = null)
	{
		if (!isset($current_order))
			$current_order = Mage::registry('current_order');

		$order = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->addFieldToFilter('created_at', array('lt' => $current_order->getCreatedAt()))
			->addAttributeToSort('created_at', 'DESC')
			->setPageSize(1);

		return $order->getFirstItem();
	}

	public function getNextOrder($current_order = null)
	{
		if (!isset($current_order))
			$current_order = Mage::registry('current_order');

		$order = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->addFieldToFilter('created_at', array('gt' => $current_order->getCreatedAt()))
			->addAttributeToSort('created_at', 'DESC')
			->setPageSize(1);

		return $order->getFirstItem();
	}

	public function getOrderCommentsBlockHtml($order)
	{
		$comments = $order->getStatusHistoryCollection();
		$resultComment = '<div>';
		foreach ($comments as $comment) {
			if ($comment->getComment()) {
				$resultComment .= $comment->getComment();
			}
		}
		$resultComment .= '</div>';
		return $resultComment;
	}
}