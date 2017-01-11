<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/20/15
 * Time: 4:05 PM
 */
class FVets_SalesRule_Block_Adminhtml_Promo_Quote_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Conditions
{
	protected function _prepareForm()
	{
		parent::_prepareForm();
		$form = $this->getForm();

		$fieldset = $form->addFieldset('premier_fieldset', array('legend'=>Mage::helper('salesrule')->__('Premier Commercial Policy')));

		$field = $fieldset->addField(
			'premier_calculation_type',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Attribute'),
				'name'  => 'premier[calculation_type]',
				'required'  => true,
				'class' => 'required-entry',
				'values'    => array(
					'weight' => Mage::helper('salesrule')->__('Weight'),
					'qty' => Mage::helper('salesrule')->__('Quantity'),
				),
			),
			''
		);

		$field = $fieldset->addField(
			'premier_from',
			'text',
			array(
				'label' => Mage::helper('salesrule')->__('From'),
				'name'  => 'premier[from]',
				'required'  => false,
				'default' => '0'
			),
			$this->_form->getElement('premier_calculation_type')->getId()
		);

		$field = $fieldset->addField(
			'premier_to',
			'text',
			array(
				'label' => Mage::helper('salesrule')->__('To'),
				'name'  => 'premier[to]',
				'required'  => false,
				'default' => '0'
			),
			$this->_form->getElement('premier_from')->getId()
		);

		$field = $fieldset->addField(
			'premier_group',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Group'),
				'name'  => 'premier[group]',
				'required'  => false,
				'values' => Mage::getResourceSingleton('fvets_salesrule/salesrule_premier_collection')->getGroupsOptionHash(),
				'note' => 'The customer will have only one promo from same group'
			),
			$this->_form->getElement('premier_to')->getId()
		);

		/*$field = $fieldset->addField(
			'premier_newgroup',
			'text',
			array(
				'label' => Mage::helper('salesrule')->__('New Group'),
				'name'  => 'premier[newgroup]',
				'required'  => false,
				'default' => '',
			),
			$this->_form->getElement('premier_group')->getId()
		);*/

		$model = Mage::registry('current_promo_quote_rule');
		$form->setValues($model->getData());

		$this->setForm($form);
		return $this;
	}
}