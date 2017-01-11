<?php

class FVets_Sales_Model_Sales_Order_Observer
{
    public function setItemRealOriginalPrice($observer)
    {
        $item = $observer->getOrderItem();
        $product = $item->getProduct();
        $item->setRealOriginalPrice($product->getPrice());
    }
}