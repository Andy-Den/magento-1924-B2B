<?php

class FVets_Salesrep_Block_Adminhtml_Customer_Edit_Tab_Integrarep extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Initialize form object
	 *
	 * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses
	 */
	public function initForm()
	{
		/* @var $customer Mage_Customer_Model_Customer */
		$customer = Mage::registry('current_customer');

		$form = new Varien_Data_Form();
		$fieldset = $form->addFieldset('integrarep_fieldset', array(
				'legend'    => Mage::helper('customer')->__("Edit integrarep informations"))
		);

		/** @var $addressForm Mage_Customer_Model_Form */
		$integrarepForm = Mage::getModel('customer/form');
		$integrarepForm
			->setEntity($customer)
			->setFormCode('adminhtml_customer_integrarep')
			->initDefaultValues();



		$attributes = $integrarepForm->getAttributes();
		foreach ($attributes as $attribute) {
			/* @var $attribute Mage_Eav_Model_Entity_Attribute */
			$attribute->setFrontendLabel(Mage::helper('customer')->__($attribute->getFrontend()->getLabel()));
			$attribute->unsIsVisible();
		}

		$this->_setFieldset($attributes, $fieldset, array());

		$form->setValues($customer->getData());
		$this->assign('customer', $customer);
		$this->setForm($form);

		return $this;
	}
}