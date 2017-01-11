<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (http://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Adminhtml_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $rule = Mage::getModel('amrolepermissions/rule')->load($this->getRequest()->getParam('rid'), 'role_id');

        Mage::register('current_rule', $rule, true);
    }

    public function categoriesJsonAction()
    {
        $this->_initRule();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('amrolepermissions/adminhtml_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}
