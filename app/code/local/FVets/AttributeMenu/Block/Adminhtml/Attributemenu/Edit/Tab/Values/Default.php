<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/27/15
 * Time: 3:37 PM
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit_Tab_Values_Default
	extends Mage_Adminhtml_Block_Template
{
	protected function _prepareLayout()
	{
		$select = $this->getLayout()->createBlock('widgets/adminhtml_select')
			->setData(array(
				'id' => 'attributemenu_value',
				'label' => Mage::helper('fvets_attributemenu')->__('Attribute Value'),
				'element_name' => 'attributemenu[value][]',
				'multiple' => 'multiple',
				'class' => 'required-entry required-entry select multiselect'
			))
			->setOptions(
				$this->getAttributeOptions()
			);
		$this->setChild('submit_select', $select);
		return parent::_prepareLayout();
	}

	public function _toHtml()
	{
		return $this->getChildHtml('submit_select');
	}

	private function getAttributeOptions()
	{
		$attributeId = $this->getRequest()->getPost('attributemenu')['attribute'];
		$valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
			->setAttributeFilter($attributeId)
			->setStoreFilter(0, false);

		$options = array();
		foreach ($valuesCollection as $storeLabel)
		{
			$options[$storeLabel->getOptionId()] = $storeLabel->getValue();
		}
		return $options;
	}
}