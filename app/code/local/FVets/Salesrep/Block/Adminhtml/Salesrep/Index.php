<?php

class FVets_Salesrep_Block_Adminhtml_Salesrep_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'fvets_salesrep';
        $this->_controller = 'adminhtml_salesrep';
        $this->_headerText = Mage::helper('fvets_salesrep')->__('Sales Representative');
        $this->_addButtonLabel = Mage::helper('fvets_salesrep')->__('New');

        parent::__construct();

        if (!Mage::getSingleton('admin/session')->isAllowed('salesrep/create')) {
            $this->_removeButton('add');
        }
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}
