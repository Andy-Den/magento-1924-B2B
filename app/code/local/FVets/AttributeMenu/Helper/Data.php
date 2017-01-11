<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu default helper
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
	protected $attribute;

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

	public function getAttribute()
	{
		if (!$this->attribute)
		{
			$attributeMenu = Mage::getModel('fvets_attributemenu/attributemenu')
				->getCollection()
				->addStoreFilter(Mage::app()->getStore()->getStoreId())
				->getFirstItem();
			$attribute = Mage::getModel('eav/entity_attribute')->load($attributeMenu->getAttribute());
			$this->attribute = $attribute;
		}
		return $this->attribute;
	}

	public function getAttributeValue()
	{
		$options = $this->getAttributeOptionsByAttributeIdAndOptionsId($this->getAttribute()->getId(), Mage::app()->getRequest()->getParam($this->getAttribute()->getAttributeCode()));
		foreach ($options as $option)
		{
			return $option;
		}

	}

	public function getAttributeValueByAttributeCodeAndOptionId($attributeCode, $optionId)
	{
		$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getStoreId())
			->setData($attributeCode, $optionId);
		return ($product->getAttributeText($attributeCode));
	}

	public function getAttributeOptionsByAttributeIdAndOptionsId($attributeId, $optionIds) {

		$options = null;
		if ($attributeId && $optionIds)
		{
			$options = Mage::getResourceModel('eav/entity_attribute_option_collection')
				->setAttributeFilter($attributeId)
				->addFieldToFilter('main_table.option_id', array('in' => explode(',', $optionIds)))
				->setPositionOrder('asc', true)
				->load();
		}

		return $options;
	}

	public function getOptionIdByAttributeValue($attributeCode, $attributeValue)
	{
		$attribute = Mage::getModel('eav/entity_attribute')->getCollection()
			->addFieldToFilter('attribute_code', $attributeCode)
			->getFirstItem();

		if (!$attribute) {
			return null;
		}

		$option = Mage::getResourceModel('eav/entity_attribute_option_collection')
			->addFieldToFilter('main_table.attribute_id', $attribute->getId())
			->addFieldToFilter('tsv.value', $attributeValue)
			->setStoreFilter(0)
			->getFirstItem();

		if (!$option || !$option->getOptionId()) {
			return null;
		}

		return $option->getOptionId();
	}

	public function formatKey($str)
	{
		$str = Mage::helper('catalog/product_url')->format($str);
		$urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		$urlKey = 'cat/'.$urlKey;
		return $urlKey;
	}

	public function filterCategoriesByProductAttribute($collection, $attributeId, $attributeValue)
	{
		$collection
			->getSelect()
			->join(array('ccp' => 'catalog_category_product'), 'e.entity_id = ccp.category_id', array())
			->join(array('cpev' => 'catalog_product_entity_varchar'), 'ccp.product_id = cpev.entity_id and cpev.attribute_id = ' . $attributeId . ' and find_in_set("' . $attributeValue . '", cpev.value)', array())
			->distinct();
		$collection->addAttributeToFilter('level', 2);
		return $collection;
	}

	/**
	 * Import rule:
	 */
	public function createRewriteRule($attributeMenu, $stores)
	{
		$values = Mage::helper('fvets_attributemenu')->getAttributeOptionsByAttributeIdAndOptionsId($attributeMenu->getAttribute(), $attributeMenu->getValue());

		foreach ($values as $value)
		{
			$fromUrl = $this->formatKey($value->getValue());
			$toUrl = 'attributemenu/index/index';

			// Create rewrite:
			/** @var Enterprise_UrlRewrite_Model_Redirect $rewrite */
			$rewrite = Mage::getModel('core/url_rewrite');

			// Check for existing rewrites:
			foreach($stores as $storeId)
			{
				// Attempt loading it first, to prevent duplicates:
				$rewrite = $rewrite->getCollection()
					->addFieldToFilter('store_id', $storeId)
					->addFieldToFilter('request_path', $fromUrl)
					->getFirstItem();

				$rewrite->setStoreId($storeId);
				$rewrite->setOptions(NULL);
				$rewrite->setIdPath(uniqid());
				$rewrite->setRequestPath($fromUrl);
				$rewrite->setIsSystem(0);
				$rewrite->setTargetPath($toUrl);

				$rewrite->save();
			}
		}
	}

	/**
	 * Delete rule:
	 */
	public function deleteRewriteRule($attributeMenu, $stores)
	{
		$values = Mage::helper('fvets_attributemenu')->getAttributeOptionsByAttributeIdAndOptionsId($attributeMenu->getAttribute(), $attributeMenu->getValue());

		foreach ($values as $value)
		{
			$fromUrl = $this->formatKey($value->getValue());

			// Create rewrite:
			/** @var Enterprise_UrlRewrite_Model_Redirect $rewrite */
			$rewrite = Mage::getModel('core/url_rewrite');

			// Check for existing rewrites:
			foreach($stores as $storeId)
			{
				// Attempt loading it first, to prevent duplicates:
				$rewrite = $rewrite->getCollection()
					->addFieldToFilter('store_id', $storeId)
					->addFieldToFilter('request_path', $fromUrl)
					->getFirstItem();

				$rewrite->delete();
			}
		}
	}

	public function getAttributeOptionValueByOptionId($optionId, $attributeCode) {
		$attribute = Mage::getModel('eav/entity_attribute')->getCollection()
			->addFieldToFilter('attribute_code', $attributeCode)
			->getFirstItem();

		if (!$attribute) {
			return null;
		}

		$option = Mage::getResourceModel('eav/entity_attribute_option_collection')
			->addFieldToFilter('main_table.attribute_id', $attribute->getId())
			->addFieldToFilter('tsv.option_id', $optionId)
			->setStoreFilter(0)
			->getFirstItem();

		return $option->getValue();
	}
}
