<?php
class FVets_BlockLinks_Block_Widget_Onepagecatalog extends FVets_BlockLinks_Block_Widget implements Mage_Widget_Block_Interface
{

	protected function _construct() {
		parent::_construct();
	}

	public function getTemplate()
	{
		if (in_array($this->getCategoryId(), $this->getAvailableStoreCategories()))
		{
			if (!$this->hasData('template')) {
				$this->setData('template', $this->getData('my_template'));
			}
			return $this->_getData('template');
		}

		return false;
	}

	public function getCategories($parent = null)
	{
		$categories = parent::getCategories(0);

		if (isset($parent))
		{
			foreach ($categories as $category)
			{

				$block = $this->getLayout()->createBlock(
					'FVets_BlockLinks_Block_Widget_Onepagecatalog_Category',
					$category->getName() . '_' . $category->getId(),
					array('template' => $this->getListTemplate())
				);

				//define o modo de exibição
				$block->setOnePageCatalogMode('list');

				$block->setCategoryId($category->getId());

				$this->getLayout()->getBlock($parent)->append($block);
			}
		}

		return $categories;
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




}