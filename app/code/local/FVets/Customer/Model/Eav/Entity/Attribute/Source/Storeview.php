<?php
class FVets_Customer_Model_Eav_Entity_Attribute_Source_Storeview extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	/**
	 * Retrieve all options array
	 *
	 * @return array
	 */
	public function getAllOptions()
	{

		$param = (Mage::app()->getRequest()->getParam('id')) ? Mage::app()->getRequest()->getParam('id') : Mage::app()->getRequest()->getParam('customer_id') ;

		$customer =  Mage::getModel("customer/customer")->load($param);

		$this->_options = array();

		$allStores = Mage::app()->getStores();
		foreach ($allStores as $_eachStoreId => $val)
		{
			if($val->getData('website_id') == $customer->getData('website_id'))
			{
				$_storeCode = Mage::app()->getStore($_eachStoreId)->getCode();
				$_storeName = Mage::app()->getStore($_eachStoreId)->getName();
				$_storeId = Mage::app()->getStore($_eachStoreId)->getId();

				$this->_options[] = array(
					'label' => $_storeName,
					'value' => $_storeId
				);
			}
		}

		return $this->_options;
	}

	/**
	 * Retrieve option array
	 *
	 * @return array
	 */
	public function getOptionArray()
	{
		$_options = array();
		foreach ($this->getAllOptions() as $option) {
			$_options[$option["value"]] = $option["label"];
		}
		return $_options;
	}

	/**
	 * Get a text for option value
	 *
	 * @param string|integer $value
	 * @return string
	 */
	public function getOptionText($value)
	{
		$options = $this->getAllOptions();
		foreach ($options as $option) {
			if ($option["value"] == $value) {
				return $option["label"];
			}
		}
		return false;
	}

	/**
	 * Retrieve Column(s) for Flat
	 *
	 * @return array
	 */
	public function getFlatColums()
	{
		$columns = array();
		$columns[$this->getAttribute()->getAttributeCode()] = array(
			"type"      => "tinyint(1)",
			"unsigned"  => false,
			"is_null"   => true,
			"default"   => null,
			"extra"     => null
		);

		return $columns;
	}

	/**
	 * Retrieve Indexes(s) for Flat
	 *
	 * @return array
	 */
	public function getFlatIndexes()
	{
		$indexes = array();

		$index = "IDX_" . strtoupper($this->getAttribute()->getAttributeCode());
		$indexes[$index] = array(
			"type"      => "index",
			"fields"    => array($this->getAttribute()->getAttributeCode())
		);

		return $indexes;
	}

	/**
	 * Retrieve Select For Flat Attribute update
	 *
	 * @param int $store
	 * @return Varien_Db_Select|null
	 */
	public function getFlatUpdateSelect($store)
	{
		return Mage::getResourceModel("eav/entity_attribute")
			->getFlatUpdateSelect($this->getAttribute(), $store);
	}
}