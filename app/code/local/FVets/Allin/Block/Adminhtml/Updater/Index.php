<?php

class FVets_Allin_Block_Adminhtml_Updater_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct()
	{
		$this->_blockGroup = 'fvets_allin';
		$this->_controller = 'adminhtml_updater_customers';
		$this->_headerText = Mage::helper('fvets_allin')->__('Updater');
		//$this->_addButtonLabel = Mage::helper('fvets_allin')->__('New');
		parent::__construct();
	}

	protected function _prepareLayout()
	{
		$this->_removeButton('add');

		$accountId = $this->getRequest()->getParam('account_switcher');
		if ($accountId) {
			$this->_addButton('synschronize', array(
				'label' => $this->__('Synschronize with AllIn'),
				'onclick' => "setLocation('" . $this->getUrl('*/*/updateremote') . "?accounttoupdate=" . $accountId . "')"
			));
		}
		return parent::_prepareLayout();
	}

}
