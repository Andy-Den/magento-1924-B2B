<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule admin edit tabs
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Block_Adminhtml_Promo_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
    /**
     * before render html
     *
     * @access protected
     * @return FVets_SalesRule_Block_Adminhtml_Salesrule_Edit_Tabs
     * @author Douglas Borella Ianitsky
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'customers',
            array(
                'label' => Mage::helper('fvets_salesrule')->__('Associated customers'),
                'url'   => $this->getUrl('*/salesrule/customers', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve salesrule entity
     *
     * @access public
     * @return FVets_SalesRule_Model_Salesrule
     * @author Douglas Borella Ianitsky
     */
    public function getSalesrule()
    {
        return Mage::registry('current_promo_quote_rule');
    }
}
