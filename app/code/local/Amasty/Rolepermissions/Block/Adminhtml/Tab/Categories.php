<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    const MODE_ALL = 0;
    const MODE_SELECTED = 1;

    public function isReadonly()
    {
        return false;
    }

    protected function getCategoryIds()
    {
        $rule = Mage::registry('current_rule');

        if ($rule->getCategories())
            return explode(',', $rule->getCategories());
        else
            return array();
    }

    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl('adminhtml/amrolepermissions_rule/categoriesJson', array('_current'=>true));
    }

    protected function _prepareLayout()
    {
        $this->setTemplate('amasty/amrolepermissions/categories.phtml');

        $form = new Varien_Data_Form();

        $categories = $this->getCategoryIds();

        $select = new Varien_Data_Form_Element_Select(array(
            'html_id' => 'amrolepermissions[categories_access_mode]',
            'name' => 'amrolepermissions[categories_access_mode]',
            'values' => array(
                $this->__('All Categories'),
                $this->__('Selected Categories')
            ),
            'value' => empty($categories) ? 0 : 1,
        ));

        $form->addElement($select);

        $this->setSelect($select);

        return parent::_prepareLayout();
    }
}
