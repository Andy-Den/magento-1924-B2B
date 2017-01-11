<?php

class FVets_Catalog_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Type_Abstract extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('option_action',
            $this->getLayout()->createBlock('adminhtml/html_select')
                ->setData(array(
                    'id' => 'product_option_{{option_id}}_action',
                    'class' => 'select product-option-action'
                ))
        );

        $this->getChild('option_action')->setName('product[options][{{option_id}}][action]')
            ->setOptions(Mage::getSingleton('fvets_catalog/adminhtml_system_config_source_product_options_action')
                ->toOptionArray());

        return $this;
    }

    /**
     * Get html of Action select element
     *
     * @return string
     */
    public function getActionSelectHtml()
    {
        if ($this->getCanEditPrice() === false) {
            $this->getChild('option_action')->setExtraParams('disabled="disabled"');
        }
        return $this->getChildHtml('option_action');
    }
}