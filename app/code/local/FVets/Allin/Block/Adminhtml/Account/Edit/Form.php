<?php

class FVets_Allin_Block_Adminhtml_Account_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$model = Mage::registry('fvets_allin');

		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
			'enctype' => 'multipart/form-data'
		));

		$fieldset = $form->addFieldset('fvets_allin', array(
			'legend' => Mage::helper('fvets_allin')->__('Account Information'),
			'class' => 'fieldset-wide'
		));

		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id'
			));
		}

		$fieldset->addField('name', 'text', array(
			'label' => Mage::helper('fvets_allin')->__('Name'),
			'name' => 'name',
			'required' => true
		));

		$fieldset->addField('website_id', 'select', array(
			'label' => Mage::helper('fvets_allin')->__('Website'),
			'class' => 'required-entry',
			'values' => Mage::helper('fvets_allin')->getWebsites(),
			'name' => 'website_id'
		));

		$fieldset->addField('list_name', 'text', array(
			'label' => Mage::helper('fvets_allin')->__('List Name'),
			'name' => 'list_name'
		));

		$fieldset->addField('user', 'text', array(
			'label' => Mage::helper('fvets_allin')->__('User'),
			'name' => 'user'
		));

		$fieldset->addField('password', 'password', array(
			'label' => Mage::helper('fvets_allin')->__('Password'),
			'name' => 'password'
		));

//		$fieldset->addField('create_list', 'checkbox', array(
//			'name' => 'create_list',
//			'label' => Mage::helper('fvets_allin')->__('Create List?')
//		));
//		$model->setData('create_list', '1');

		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
