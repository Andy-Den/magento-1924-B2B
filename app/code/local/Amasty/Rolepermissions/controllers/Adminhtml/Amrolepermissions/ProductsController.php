<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Adminhtml_Amrolepermissions_ProductsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $rule = Mage::getModel('amrolepermissions/rule')->load($this->getRequest()->getParam('rid'), 'role_id');

        Mage::register('current_rule', $rule, true);
    }

    public function relatedGridAction()
    {
        $this->_initRule();

        $grid = $this->getLayout()->createBlock('amrolepermissions/adminhtml_tab_products_grid')
            ->setProductIds($this->getRequest()->getPost('product_ids', null));

        $this->getResponse()->setBody($grid->toHtml());
    }

    public function productsAction()
    {
        $this->_initRule();

        $this->getResponse()->setBody(
            Mage::getBlockSingleton('amrolepermissions/adminhtml_tab_products')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/amrolepermissions/products');
    }
}
