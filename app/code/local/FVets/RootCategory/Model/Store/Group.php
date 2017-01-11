<?php

class FVets_RootCategory_Model_Store_Group extends Mage_Core_Model_Store_Group
{
	const ROOT_CATEGORY_PATH = 'general/categories/menu_root_category';

	public function getRootCategoryId()
	{
		if (Mage::getStoreConfig(self::ROOT_CATEGORY_PATH))
		{
			return Mage::getStoreConfig(self::ROOT_CATEGORY_PATH);
		}

		return $this->_getData('root_category_id');
	}
}