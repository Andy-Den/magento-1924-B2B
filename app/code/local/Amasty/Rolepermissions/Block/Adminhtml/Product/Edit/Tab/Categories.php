<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getCategories() && !in_array($node->getId(), $rule->getCategories()))
            $item['disabled'] = true;

        return $item;
    }
}
