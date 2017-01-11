<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * Admin source model for Value
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Model_Attributemenu_Attribute_Source_Value extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * get possible values
     *
     * @access public
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     * @author Ultimate Module Creator
     */
	public function getAllOptions($withEmpty = true, $defaultValues = false)
	{
		$attributemenuId = Mage::app()->getRequest()->getParam('id');

		if ($attributemenuId)
		{
			$attributeMenu = Mage::getModel('fvets_attributemenu/attributemenu')->load($attributemenuId);
			$attribute = $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
				->addFieldToFilter('main_table.attribute_id', $attributeMenu->getAttribute())
				->getFirstItem();
			$source = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute->getAttributeCode());
			return $source->getSource()->getAllOptions($withEmpty, $defaultValues);
		} else
		{
			return array();
		}
	}


    /**
     * get options as array
     *
     * @access public
     * @param bool $withEmpty
     * @return string
     * @author Ultimate Module Creator
     */
    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * get option text
     *
     * @access public
     * @param mixed $value
     * @return string
     * @author Ultimate Module Creator
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionsArray();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $texts = array();
        foreach ($value as $v) {
            if (isset($options[$v])) {
                $texts[] = $options[$v];
            }
        }
        return implode(', ', $texts);
    }
}
