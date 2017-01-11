<?php

class FVets_Catalog_Block_Product_View_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Get price configuration
     *
     * @param Mage_Catalog_Model_Product_Option_Value|Mage_Catalog_Model_Product_Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = parent::_getPriceConfiguration($option);

        $data['multiply_factor'] = $option->getMultiplyFactor();

        return $data;
    }
}