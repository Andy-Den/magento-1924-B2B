<?php

/**
 * Salesrep default helper
 *
 * @category    FVets
 * @package     FVets_Salesrep
 */
class FVets_Salesrep_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getImageUrl($salesrep)
	{
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, array('_secure'=>true)) . 'fvets/salesrep/' . $salesrep->getId() . '.jpg';
	}

	function getSalesRepCustomers($id = null) {
		if (!isset($id)) {
			return;
		}

		$model = Mage::getModel('customer/customer');
		$collection = $model->getCollection()
			->addAttributeToSelect('firstname')
			->addAttributeToSelect('lastname')
			->addAttributeToSelect('cnpj')
			->addAttributeToSelect('cpf')
			->addAttributeToSelect('brands')
			->addAttributeToSelect('id_erp', 'id_erp')
			->addFieldToFilter('fvets_salesrep', array('finset' => array($id)));

		$collection->getSelect()->order('cast(id_erp as unsigned) ASC');


		return $collection;

	}

	function isLoggedUserRep()
	{
		if (Mage::getSingleton('customer/session')->getCustomer()->getTipopessoa() == 'RC') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * convert array to options
	 *
	 * @access public
	 * @param $options
	 * @return array
	 */
	public function convertOptions($options)
	{
		$converted = array();
		foreach ($options as $option) {
			if (isset($option['value']) && !is_array($option['value']) &&
				isset($option['label']) && !is_array($option['label'])) {
				$converted[$option['value']] = $option['label'];
			}
		}
		return $converted;
	}

	public function getSalesrepCategories()
	{

	}

	public function getSalesrepByCustomerAndProduct($customer, $product)
	{

		$rootCategory = Mage::getModel('catalog/category')
			->load(Mage::app()->getStore()->getRootCategoryId());

		if ($rootCategory->getLevel() == '2')
		{
			$category = $rootCategory;
		}
		else
		{
			$category = Mage::getModel('catalog/category')
				->getCollection()
				->addAttributeToFilter('entity_id', array('in' => $product->getCategoryIds()))
				->addAttributeToFilter('level', '2')
				->addFieldToFilter('path', array('like' => $rootCategory->getPath() . '/%'))
				->getFirstItem();

			if (!$category->getId())
			{
				return Mage::getModel('fvets_salesrep/salesrep');
			}
		}

		$salesrep = Mage::getModel('fvets_salesrep/salesrep')->getCollection()
			->addCategoryToFilter($category->getId())
			->addFieldToFilter('id', array('in' => explode(',', $customer->getFvetsSalesrep())))
			->getFirstItem();
		;

		return $salesrep;
	}

	public function sendRepComissionEmail($observer = null, $order = null)
	{
		try
		{
			if (!$order)
			{
				$order = $observer->getOrder();
			}

			($order->getStoreId()) ? $storeId = $order->getStoreId() : $storeId = Mage::app()->getStore()->getId();
			if (!Mage::getStoreConfig('fvets_salesrep/general/enabled', $storeId))
			{
				return $this;
			}

			//array(rep => value)
			$repsOrderValue = $this->calculateRepOrderData($order);

			foreach ($repsOrderValue as $repId => $repOrderValue)
			{
				$rep = Mage::getModel('fvets_salesrep/salesrep')->load($repId);

				if ($rep->getComission() > 0.00001)
				{
					$comissionValue = number_format($repOrderValue * ($rep->getComission() / 100), 2, ",", ".");
					$order->setComissionString("A sua comissão nesta venda foi de <strong>R$ $comissionValue</strong><br/>");
				} else
				{
					$order->setComissionString('');
				}

				$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
					->setIsSecureMode(true);
				$paymentBlock->getMethod()->setStore($storeId);
				$paymentBlockHtml = $paymentBlock->toHtml();

				$rep->sendRepComissionEmail($order, $paymentBlockHtml, $rep, $storeId);
			}

			$order->setSalesrepEmail(1);

			if (!$observer || !isset($observer))
			{
				$order->getResource()->saveAttribute($order, 'salesrep_email');
			}

		} catch (Exception $ex)
		{
			try
			{
				if (strpos($ex->getMessage(), 'no_rep_attached') !== false)
				{
					//seta flag de email não enviado
					//$order->setSalesrepEmail(0);
					//na verdade não precisa setar nesse caso já que o valor default serve;
				}
			} catch (Exception $ex)
			{
				//just let the flow continues;
			}
		}
	}

	private function calculateRepOrderData($order)
	{
		$repsValue = array();
		$items = $order->getItemsCollection();
		foreach ($items as $item) {
			$salesrepId = $item->getSalesrepId();
			//se não tem rep vinculado ao item, pule o item;
			if (!$salesrepId) {
				throw new Exception('no_rep_attached');
			}
			$value = $item->getRowTotal();
			$discount = $item->getDiscountAmount();
			if ($discount) {
				$value = $value - $discount;
			}
			$repsValue[$salesrepId] = ((isset($repsValue[$salesrepId]) ? $repsValue[$salesrepId] : 0) + $value);
		}

		return $repsValue;
	}

	function deleteResizedImages($salesrepId)
	{
		$path = Mage::getBaseDir('media') . DS . 'resized' . DS . 'fvets' . DS . 'salesrep' . DS;

		if ($handle = opendir($path))
		{
			//varrendo o diretório em busca das imagens cacheadas vinculadas ao salesrep;
			while (FALSE !== ($file = readdir($handle)))
			{
				if ($file == $salesrepId . '.jpg' || $this->startsWith($file, $salesrepId . '_')) {
					@unlink("$path" . $file);
				}
			}
		}
	}

	function startsWith($string, $contains) {
		return $contains === "" || strrpos($string, $contains, -strlen($string)) !== FALSE;
	}

	function canSellProduct($salesrep, $product)
	{
		$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrep);
		$product = Mage::getModel('catalog/product')->load($product);
		$salesrepBrands = $salesrep->getSelectedCategories();
		$productBrands = $product->getCategoryCollection();
		foreach ($productBrands as $productBrand) {
			if($productBrand->getLevel() != 2) {
				continue;
			}
			foreach ($salesrepBrands as $salesrepBrand) {
				if ($salesrepBrand->getId() == $productBrand->getId()) {
					return true;
				}
			}
		}
		return false;
	}
}