<?php

class FVets_SalesRule_Block_Adminhtml_Promo_Quote_Grid extends Mage_Adminhtml_Block_Promo_Quote_Grid
{

    protected function _prepareColumns()
    {
        $this->addColumnAfter('description', array(
            'header'    => Mage::helper('salesrule')->__('Description'),
            'align'     =>'left',
            'index'     => 'description',
        ), 'name');

        parent::_prepareColumns();

        return $this;
    }

}