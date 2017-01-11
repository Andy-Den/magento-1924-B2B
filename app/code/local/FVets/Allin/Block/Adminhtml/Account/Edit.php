<?php

class FVets_Allin_Block_Adminhtml_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

	public function __construct()
	{
		$this->_objectId = 'id';
		$this->_blockGroup = 'fvets_allin';
		$this->_controller = 'adminhtml_account';

		$this->_updateButton('save', 'label', Mage::helper('fvets_allin')->__('Save'));
		$this->_updateButton('delete', 'label', Mage::helper('fvets_allin')->__('Delete'));

		parent::__construct();
	}

	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}


	public function getHeaderText()
	{
		if (Mage::registry('fvets_allin')->getId()) {
			return Mage::helper('fvets_allin')->__("Edit '%s'", $this->escapeHtml(Mage::registry('fvets_allin')->getName()));
		} else {
			return Mage::helper('fvets_allin')->__('New');
		}
	}

}
