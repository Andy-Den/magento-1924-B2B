<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * Admin source model for Attribute
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Model_Attributemenu_Attribute_Source_Attribute extends Mage_Eav_Model_Entity_Attribute_Source_Table
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
		$productAttrs = Mage::getResourceModel('catalog/product_attribute_collection')
			->setOrder('frontend_label', 'asc');

		$options = array();

		foreach ($productAttrs as $productAttr)
		{
			if ($productAttr->getAttributeCode() && $productAttr->getFrontendLabel())
			$options[] = array('label' => $productAttr->getFrontendLabel(),
				'value' => $productAttr->getAttributeId());
		}
		if ($withEmpty)
		{
			array_unshift($options, array('label' => '', 'value' => ''));
		}
		return $options;

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
