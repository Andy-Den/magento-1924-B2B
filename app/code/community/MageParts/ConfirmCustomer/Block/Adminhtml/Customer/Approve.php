<?php

class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Approve extends Mage_Adminhtml_Block_Widget_Form_Container
{

	public function __construct()
	{
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'confirmcustomer';
		$this->_controller = 'adminhtml_customer';
		$this->_mode = 'approve';

		$this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('adminhtml/customer/edit/', array('id' => Mage::registry('current_customer')->getId())) . '\')');
	}

	public function getHeaderText()
	{
		return Mage::helper('fvets_salesrep')->__("Approve '%s'", $this->escapeHtml(Mage::registry('current_customer')->getName()));
	}

}
