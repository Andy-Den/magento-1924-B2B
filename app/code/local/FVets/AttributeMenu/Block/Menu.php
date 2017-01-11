<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/24/15
 * Time: 10:57 AM
 */
class FVets_AttributeMenu_Block_Menu extends Mage_Page_Block_Html_Topmenu
{

	public function _construct()
	{
		parent::_construct();
	}

	public function getCacheKeyInfo()
	{
		$params = Mage::app()->getRequest()->getParams();
		$paramsKey = '';
		foreach ($params as $key => $value)
		{
			if (!is_array($value))
			{
				$paramsKey = $paramsKey . ($key . $value);
			}
		}
		return array(
			'fvets_attributemenu_block_menu',
			$paramsKey,
			Mage::app()->getStore()->getCode(),
			Mage::getSingleton("customer/session")->isLoggedIn()
		);
	}

	public function returnAttributeValues()
	{
		$attributeMenu = Mage::getModel('fvets_attributemenu/attributemenu')
			->getCollection()
			->addStoreFilter(Mage::app()->getStore()->getStoreId())
			->getFirstItem();

		$attribute = Mage::helper('fvets_attributemenu')->getAttribute();

		$values = $attributeMenu->getValue();

		$values = Mage::helper('fvets_attributemenu')->getAttributeOptionsByAttributeIdAndOptionsId($attribute->getId(), $values);

		$result = array();
		if ($values)
		{
			foreach ($values as $value)
			{
				$result[$value->getOptionId()] = $value->getValue();
			}
		}

		return $result;
	}
}