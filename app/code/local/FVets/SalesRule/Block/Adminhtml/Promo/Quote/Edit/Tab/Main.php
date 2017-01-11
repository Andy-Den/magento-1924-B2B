<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/20/15
 * Time: 4:05 PM
 */
class FVets_SalesRule_Block_Adminhtml_Promo_Quote_Edit_Tab_Main extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main
{
	protected function _prepareForm()
	{
		parent::_prepareForm();
		$form = $this->getForm();
		$fieldset = $form->getElements()->searchById('base_fieldset');

		$field = $fieldset->addField(
			'rule_type',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Rule Type'),
				'name'  => 'rule_type',
				'required'  => true,
				'class' => 'required-entry',
				'values'=> Mage::getModel('fvets_salesrule/salesrule_attribute_source_ruletype')->getAllOptions(false),

			),
			$this->_form->getElement('is_active')->getId()
		);

		$fieldset->addField(
			'apply_to_all',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Apply to all customers'),
				'name'  => 'apply_to_all',
				'required'  => true,
				'class' => 'required-entry',
				'note' => 'Only for customers of the selected website',
				'values'=> array(
					array(
						'value' => 1,
						'label' => Mage::helper('salesrule')->__('Yes'),
					),
					array(
						'value' => 0,
						'label' => Mage::helper('salesrule')->__('No'),
					),
				),
			),
			$this->_form->getElement('rule_type')->getId()
		);

		$fieldset->addField('only_on_sale', 'text', array(
			'name' => 'only_on_sale',
			'label' => Mage::helper('salesrule')->__('Only on order nº'),
			'note' => Mage::helper('salesrule')->__('Use 0 (zero) for anyone')
		));

		$fieldset->addField('days_from_last_order', 'text', array(
			'name' => 'days_from_last_order',
			'label' => Mage::helper('salesrule')->__('Days from last order'),
			'note' => Mage::helper('salesrule')->__('Use 0 (zero) for infinite')
		));

		$fieldset->addField('is_mix', 'select', array(
			'name' => 'is_mix',
			'label' => Mage::helper('salesrule')->__('Is mix?'),
			'options' => array(
				'1' => Mage::helper('salesrule')->__('Yes'),
				'0' => Mage::helper('salesrule')->__('No'),
			),
		));

		$fieldset->addField(
			'create_labels',
			'select',
			array(
				'label' => Mage::helper('salesrule')->__('Create Labels?'),
				'name'  => 'create_labels',
				'note' => 'Criar labels para todos os produtos que coincidam com as regras da abas condição. Apenas para os atributos "ID_ERP" e "CATEGORY_ID"',
				'values'=> array(
					array(
						'value' => 1,
						'label' => Mage::helper('salesrule')->__('Yes'),
					),
					array(
						'value' => 0,
						'label' => Mage::helper('salesrule')->__('No'),
					),
				),
			)
		);

		$fieldset->addField('create_labels_text', 'text', array(
			'name' => 'create_labels_text',
			'label' => Mage::helper('salesrule')->__('Descrição das Labels')
		));

		$fieldset->addField('clean_labels', 'checkbox', array(
			'name' => 'clean_labels',
			'label' => Mage::helper('salesrule')->__('Limpar Labels')
		));

		$form->getElement('customer_group_ids')->setRequired(false);

		$model = $model = Mage::registry('current_promo_quote_rule');
		$form->setValues($model->getData());

		$this->setForm($form);


		// Append dependency javascript
		/*$this->setChild('form_after', $this->getLayout()
			->createBlock('adminhtml/widget_form_element_dependence')
			->addFieldMap('rule_rule_type', 'rule_type')
			->addFieldMap('rule_premier_attribute_id', 'premier_attribute_id')
			->addFieldMap('rule_premier_from', 'premier_from')
			->addFieldMap('rule_premier_to', 'premier_to')
			->addFieldDependence('premier_attribute_id','rule_type', '2')
			->addFieldDependence('premier_from','rule_type', '2')
			->addFieldDependence('premier_to','rule_type', '2')
		);*/

		return $this;
	}
}