<?php

class FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule_Premier extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Initialize form
	 *
	 * @return FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule
	 */
	public function _prepareForm()
	{
		/* @var $customer Mage_Customer_Model_Customer */
		$customer = Mage::registry('current_customer');

		$form = new Varien_Data_Form();

		$fieldset = $form->addFieldset('premier_fieldset', array(
				'legend'    => Mage::helper('customer')->__("Premier Commercial Policy"))
		);

		/** @var $addressForm Mage_Customer_Model_Form */
		$premierForm = Mage::getModel('customer/form');
		$premierForm
			->setEntity($customer)
			->setFormCode('adminhtml_customer_premier')
			->initDefaultValues();

		$attributes = $premierForm->getAttributes();
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