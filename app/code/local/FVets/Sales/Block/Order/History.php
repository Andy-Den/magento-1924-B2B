<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order history block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FVets_Sales_Block_Order_History extends Mage_Core_Block_Template
{

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('sales/order/history.phtml');

		$isRepresentante = $this->isRepresentante();

		if (!$isRepresentante) {

		$orders = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
			->setOrder('created_at', 'desc');

		} else {
//			$idRep = Mage::getSingleton('customer/session')->getCustomer()->getFvetsSalesrep();
//
//			$repClientsCollection = Mage::getModel('customer/customer')
//				->getCollection()
//				->addAttributeToSelect('entity_id')
//				->addFieldToFilter('fvets_salesrep', $idRep);
			$idCustomerParam = $this->getRequest()->getParam('user');

			if (isset($idCustomerParam) && $idCustomerParam != -1) {
				$orders = Mage::getResourceModel('sales/order_collection')
					->addFieldToSelect('*')
					->addFieldToFilter('customer_id', $idCustomerParam)
					->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
					//->setOrder('created_at', 'desc');
					->setOrder('customer_firstname', 'asc');
			} else {
				$orders = $this->getOrdersClientsForThisRep();
			}
		}

		$this->setOrders($orders);

		Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Orders'));
	}

	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$pager = $this->getLayout()->createBlock('page/html_pager', 'sales.order.history.pager')
			->setCollection($this->getOrders());
		$this->setChild('pager', $pager);
		$this->getOrders()->load();
		return $this;
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getViewUrl($order)
	{
		return $this->getUrl('*/*/view', array('order_id' => $order->getId()));
	}

	public function getTrackUrl($order)
	{
		return $this->getUrl('*/*/track', array('order_id' => $order->getId()));
	}

	public function getReorderUrl($order)
	{
		return $this->getUrl('*/*/reorder', array('order_id' => $order->getId()));
	}

	public function getBackUrl()
	{
		return $this->getUrl('customer/account/');
	}

	public function isRepresentante() {
		$tipoPessoa = Mage::getSingleton('customer/session')->getCustomer()->getTipopessoa();
		if (isset($tipoPessoa) && $tipoPessoa == 'RC') {
			return true;
		} else {
			return false;
		}
	}

	public function getCustomersForThisRep() {
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		$repClientsCollection = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToSelect('entity_id')
			->addAttributeToSelect('firstname')
			->addAttributeToSelect('lastname')
			->addFieldToFilter('fvets_salesrep', $customer->getFvetsSalesrep())
			->addFieldToFilter('website_id', $customer->getWebsiteId());

		$customers = array();
		foreach($repClientsCollection as $customer)
		{
			$customers[] = $customer->getId();
		}

		return $customers;
	}

	public function getOrdersClientsForThisRep() {
		$repClients = $this->getCustomersForThisRep();

		$order = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('customer_id', array('in' => $repClients))
			->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
			->setOrder('customer_firstname', 'asc');

		return $order;
	}
}
