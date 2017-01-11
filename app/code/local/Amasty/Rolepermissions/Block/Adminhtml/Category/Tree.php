<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected function _getNodeJson($node, $level = 0)
    {
        $node = parent::_getNodeJson($node, $level);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getCategories() && !in_array($node['id'], $rule->getCategories()))
        {
            $node['disabled'] = true;
            $node['allowChildren'] = false;
        }

        return $node;
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $code = "if (node.disabled) return;";
        $html = preg_replace('|(categoryClick : function[^{]+\{\s*)|s', "\\1$code\n", $html);

        $storeId = +$this->getRequest()->getParam('store');
        $root = +Mage::app()->getStore($storeId)->getRootCategoryId();

        $rule = Mage::helper('amrolepermissions')->currentRule();
        if ($rule->getCategories()
            && !in_array(
                $root,
                $rule->getCategories()
            )
        ) {
            $defaultParams = "disabled:true,\nallowChildren:false,";
            $html = preg_replace('|defaultLoadTreeParams = {[\n\s]+parameters : {\n|s', "\\0$defaultParams\n", $html);
        }

        return $html;
    }
}
