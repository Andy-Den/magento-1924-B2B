<?php
class FVets_BlockLinks_Block_Widget	extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{

	protected $storeAvailableCategories = array();

	protected function _construct() {
		parent::_construct();
	}

	public function getTemplate()
	{
		if (!$this->hasData('template')) {
			$this->setData('template', "blocklinks/home/full.phtml");
		}
		return $this->_getData('template');
	}

	/**
	 * Return html
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		return parent::_toHtml();
	}

	public function getCategories($limit = 5) {
		$category = $this->getCurrentCategory();

		/*Returns comma separated ids*/
		$subcats = $category->getChildren();

		$categories = array();

		$count = 0;
		foreach(explode(',',$subcats) as $subCatid)
		{
			$_category = Mage::getModel('catalog/category')->load($subCatid);
			if($_category->getIsActive())
			{
				$categories[] = $_category;
			}
			if ($limit && ++$count == $limit) {
				break;
			}
		}

		return $categories;

	}


	/**
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCurrentCategory()
	{
		if ($categoryId = $this->getCategoryId()) {
			return Mage::getModel('catalog/category')->load($categoryId);
		}
		if (Mage::getSingleton('catalog/layer')) {
			return Mage::getSingleton('catalog/layer')->getCurrentCategory();
		}
		return false;
	}

	/**
	 * @return int
	 */
	public function getCategoryId()
	{
		$id = $this->_getData('category_id');
		if (null !== $id && strstr($id, 'category/')) { // category id from widget
			$id = str_replace('category/', '', $id);
		}
		return $id;
	}

	public function getAvailableStoreCategories()
	{
		if (count($this->storeAvailableCategories) <= 0)
		{
			$this->storeAvailableCategories[] = Mage::app()->getStore()->getRootCategoryId();
			$this->getTreeCategories(Mage::app()->getStore()->getRootCategoryId(), $this->storeAvailableCategories);

		}

		return $this->storeAvailableCategories;
	}

	function getTreeCategories($parentId, &$return) {
		$allCats = Mage::getModel('catalog/category')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('is_active','1')
			//->addAttributeToFilter('include_in_menu','1')
			->addAttributeToFilter('parent_id',array('eq' => $parentId));

		foreach ($allCats as $category)
		{
			$return[] = $category->getId();

			$subcats = $category->getChildren();
			if($subcats != ''){
				$this->getTreeCategories($category->getId(), $return);
			}
		}
	}

}