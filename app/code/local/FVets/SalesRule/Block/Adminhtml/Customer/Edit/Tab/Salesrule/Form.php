<?php

class FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule_Form extends Mage_Adminhtml_Block_Widget_Form
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

		$fieldset = $form->addFieldset('promo_form_fieldset', array(
				'legend'    => Mage::helper('customer')->__("Promos"))
		);

		/** @var $addressForm Mage_Customer_Model_Form */
		$promoForm = Mage::getModel('customer/form');
		$promoForm
			->setEntity($customer)
			->setFormCode('adminhtml_customer_promo_form')
			->initDefaultValues();

		$attributes = $promoForm->getAttributes();
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