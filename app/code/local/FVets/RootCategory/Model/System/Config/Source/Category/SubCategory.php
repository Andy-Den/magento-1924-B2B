<?php

class FVets_RootCategory_Model_System_Config_Source_Category_SubCategory
{
    public function toOptionArray($addEmpty = true, $level = 1)
    {

		$group =  Mage::getModel('core/store')->load(Mage::getSingleton('adminhtml/config_data')->getStore())->getGroup();

		if (!$group)
		{
			$website = Mage::getModel('core/website')->load(Mage::getSingleton('adminhtml/config_data')->getWebsite(), 'code');
			$group = Mage::getModel('core/store_group')->load($website->getDefaultGroupId());
		}

		$category = Mage::getModel('catalog/category')->load($group->getRootCategoryId());

		$options = array();
		$options[] = array(
			'label' => $category->getName(),
			'value' => $category->getId()
		);

		$this->getChildren($options, $category->getId(), $category->getLevel()+1);


        return $options;
    }

	private function getChildren(&$options, $root, $level)
	{
		$collection = Mage::getResourceModel('catalog/category_collection');
		$collection->addAttributeToSelect('name')
			->addLevelFilter($level)
			->addFieldToFilter('parent_id', array('eq'=>$root))
			->load();

		foreach ($collection as $category) {
			if($category->getData('level') == $level)
			{
				$dash = null;
				for ($x = 0; $x < $level; $x++)
				{
					$dash .= '- ';
				}
				$options[] = array(
					'label' => $dash . $category->getName(),
					'value' => $category->getId()
				);

				$this->getChildren($options, $category->getId(), $category->getLevel()+1);
			}
		}

		return $options;
	}
}
