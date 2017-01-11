<?php

class FVets_Catalog_Model_Product_Option_Value extends Mage_Catalog_Model_Product_Option_Value
{
    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return float|int
     */
    public function getPrice($flag=false)
    {
        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getOption()->getProduct()->getFinalPrice();
            $price = $basePrice*($this->_getData('price')/100);
            if ($this->getAction() == 'decrease') {
                $price = -$price;
            }
            return $price;
        }
        return $this->_getData('price');
    }
}