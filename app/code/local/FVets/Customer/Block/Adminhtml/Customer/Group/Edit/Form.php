<?php

class FVets_Customer_Block_Adminhtml_Customer_Group_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form
{
	/**
	 * Prepare form for render
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$form = $this->getForm();
		$fieldset = $form->getElement('base_fieldset');

		$fieldset->addField('website_id', 'select',
			array(
				'name'  => 'website_id',
				'label' => Mage::helper('customer')->__('Website'),
				'title' => Mage::helper('customer')->__('Website'),
				'class' => 'required-entry',
				'required' => true,
				'values' => Mage::getSingleton('core/resource_website_collection')->toOptionHash()
			)
		);

		$customerGroup = Mage::registry('current_group');
		$form->addValues($customerGroup->getData());


	}
}
