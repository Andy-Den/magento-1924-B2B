<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Adminhtml_Amrolepermissions_CategoriesController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $rule = Mage::getModel('amrolepermissions/rule')->load($this->getRequest()->getParam('rid'), 'role_id');

        Mage::register('current_rule', $rule, true);
    }

    public function categoriesAction()
    {
        $this->_initRule();

        $this->getResponse()->setBody(
            Mage::getBlockSingleton('amrolepermissions/adminhtml_tab_categories')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/amrolepermissions/categories');
    }
}
