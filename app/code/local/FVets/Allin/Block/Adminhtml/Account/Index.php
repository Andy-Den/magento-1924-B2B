<?php

class FVets_Allin_Block_Adminhtml_Account_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'fvets_allin';
        $this->_controller = 'adminhtml_account';
        $this->_headerText = Mage::helper('fvets_allin')->__('Accounts Management');
        $this->_addButtonLabel = Mage::helper('fvets_allin')->__('New');
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}
