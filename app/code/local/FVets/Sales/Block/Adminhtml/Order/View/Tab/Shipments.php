<?php

class FVets_Sales_Block_Adminhtml_Order_View_Tab_Shipments extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments
{
    public function canShowTab()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/tabs/view_shipments');
    }
}