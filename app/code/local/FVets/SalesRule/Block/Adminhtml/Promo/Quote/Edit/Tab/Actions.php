<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/20/15
 * Time: 4:05 PM
 */
class FVets_SalesRule_Block_Adminhtml_Promo_Quote_Edit_Tab_Actions extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions
{
	protected function _prepareForm()
	{
		parent::_prepareForm();
		$form = $this->getForm();
		$fieldset = $form->getElements()->searchById('action_fieldset');

		$fieldset->addField(
			'stop_condition_discount',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Stop payment conditions discount'),
				'name'  => 'stop_condition_discount',
				'required'  => false,
				'class' => 'required-entry',
				'values'    => array(
					0 => Mage::helper('salesrule')->__('No'),
					1 => Mage::helper('salesrule')->__('Yes'),
				),

			),
			$this->_form->getElement('stop_rules_processing')->getId()
		);

		$fieldset->addField(
			'stallment_discount_amount',
			'text',
			array(
				'label' => Mage::helper('salesrule')->__('Stallment discount amount'),
				'name'  => 'stallment_discount_amount',
				'required'  => false,
				'default'	=> 0
			),
			$this->_form->getElement('discount_amount')->getId()
		);

		$model = Mage::registry('current_promo_quote_rule');
		$form->setValues($model->getData());

		$this->setForm($form);
		return $this;
	}
}