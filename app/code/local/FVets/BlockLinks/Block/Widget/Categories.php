<?php
class FVets_BlockLinks_Block_Widget_Categories extends FVets_BlockLinks_Block_Widget implements Mage_Widget_Block_Interface
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