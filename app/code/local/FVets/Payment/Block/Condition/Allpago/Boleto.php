<?php
/**
 * Condition list block
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author 		Douglas Borella Ianitsky
 */
class FVets_Payment_Block_Condition_Allpago_Boleto extends Mage_Core_Block_Template
{

	protected $_checkoutSession	= null;
	protected $_quote				= null;
	protected $_store				= null;
	protected $_customer			= null;

	public function getConditions()
	{
		$collection = mage::getModel('fvets_payment/condition')->getCollection()
			->addFieldToFilter('payment_methods', array('finset' => array($this->getParentBlock()->getMethodCode())))
			->addFieldToFilter('price_range_begin', array('lteq' => $this->getGrandTotal()))
			->addFieldToFilter(
				array('price_range_end', 'price_range_end'),
				array(
					array('gteq' => $this->getGrandTotal()),
					array('price_range_end', 'eq' => '0')
				)
			)
			->addFieldToFilter('status', '1')
			->addStoreFilter($this->getStore(), false)
			->addCustomerFilter($this->getCustomer())
			->addCategoriesFilter(array_keys(Mage::helper('fvets_payment/category')->getQuoteCategories()), true)
			->excludeCategoriesFilter(array_keys(Mage::helper('fvets_payment/category')->getQuoteCategories()))
		;

		//Mage::getStoreConfig(FVets_Payment_Helper_Data::XML_PAYMENTCONDITION_ORDERBY);

		$collection->getSelect()->group('main_table.entity_id');

		$collection->getSelect()->order('main_table.price_range_begin ASC');

		/*$ids = array();
		foreach ($collection as $item)
		{
			$ids[] = $item->getId();
		}

		//Remove da collection todos os itens que nao podem aparecer. (Que puta explicaçao)
		$collection2 = mage::getModel('fvets_payment/condition')->getCollection()
			->addFieldToFilter('status', '1')
			->addStoreFilter($this->getStore(), false)
			->addCustomerFilter($this->getCustomer())
			->addCategoriesFilter(array_keys(Mage::helper('fvets_payment/category')->getQuoteCategories()), false)
			->addFieldToFilter('entity_id', array('nin' => $ids))
		;


		//Nao queria fazer as coisas dessa forma, mas nao tenho tempo para pensar muito, entao desculpe quem pegar esse codigo
		foreach ($collection2 as $key2 => $item)
		{
			foreach($collection as $key1 => $item2)
			{
				if ($item->getIdErp() == $item2->getIdErp())
				{
					$collection->removeItemByKey($key1);
				}
			}
		}

		//Nao queria fazer as coisas dessa forma, mas nao tenho tempo para pensar muito, entao desculpe quem pegar esse codigo
		foreach ($collection as $key1 => $item)
		{
			foreach($collection as $key2 => $item2)
			{
				if (($item->getIdErp() == $item2->getIdErp()) && ($key1 != $key2))
				{
					$collection->removeItemByKey($key1);
				}
			}
		}*/

		//echo $collection->getSelect()->__toString();

		if (Mage::getSingleton('checkout/session')->getIsSplitCheckout())
		{
			Mage::getSingleton('checkout/session')->setFvetsQuoteConditions($collection->getFirstItem()->getId());
		}

		/*echo $collection->getSelect()->__toString();
		die();*/

		return $collection;
	}

	protected function getCheckoutSession()
	{
		if (!$this->_checkoutSession)
		{
			$this->_checkoutSession = Mage::getModel('checkout/session');
		}

		return $this->_checkoutSession;
	}

	protected function getQuote()
	{
		if (!$this->_quote)
		{
			$this->_quote = $this->getCheckoutSession()->getQuote();
		}

		return $this->_quote;
	}

	protected function getGrandTotal()
	{
		return $this->getQuote()->getData('grand_total');
	}

	protected function getStore()
	{
		if (!$this->_store)
		{
			$this->_store = Mage::app()->getStore();
		}

		return $this->_store;
	}

	protected function getCustomer()
	{
		if (!$this->_customer)
		{
			$this->_customer = Mage::getSingleton('customer/session')->getCustomer();
		}

		return $this->_customer;
	}
}