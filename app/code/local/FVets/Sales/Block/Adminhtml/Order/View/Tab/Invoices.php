<?php

class FVets_Sales_Block_Adminhtml_Order_View_Tab_Invoices extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Invoices
{
    public function canShowTab()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/tabs/view_invoices');
    }
}