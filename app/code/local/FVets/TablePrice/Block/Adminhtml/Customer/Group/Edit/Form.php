<?php

if(Mage::helper('core')->isModuleEnabled('FVets_Customer')){
	class FVets_TablePrice_Block_Adminhtml_Customer_Group_Edit_Form_tmp extends FVets_Customer_Block_Adminhtml_Customer_Group_Edit_Form {}
} else {
	class FVets_TablePrice_Block_Adminhtml_Customer_Group_Edit_Form_tmp extends Mage_Adminhtml_Block_Customer_Group_Edit_Form {}
}


class FVets_TablePrice_Block_Adminhtml_Customer_Group_Edit_Form extends FVets_TablePrice_Block_Adminhtml_Customer_Group_Edit_Form_tmp
{
	/**
	 * Prepare form for render
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		$form = $this->getForm();
		$fieldset = $form->getElement('base_fieldset');

		$fieldset->addField('id_tabela', 'text',
			array(
				'name'  => 'id_tabela',
				'label' => Mage::helper('customer')->__('ID Tabela'),
				'title' => Mage::helper('customer')->__('ID Tabela'),
				'class' => 'required-entry',
				'required' => false
			)
		);

		$fieldset->addField('multiple_table', 'select',
			array(
				'name'  => 'multiple_table',
				'label' => Mage::helper('fvets_tableprice')->__('Has multiple table'),
				'title' => Mage::helper('fvets_tableprice')->__('Has multiple table'),
				'class' => 'required-entry',
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
			)
		);

		$customerGroup = Mage::registry('current_group');
		$form->addValues($customerGroup->getData());


	}
}
