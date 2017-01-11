<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group admin edit tabs
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Douglas Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalogrestrictiongroup_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tabs
     * @author Douglas Ianitsky
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_catalogrestrictiongroup',
            array(
                'label'   => Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'),
                'title'   => Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'),
                'content' => $this->getLayout()->createBlock(
                    'fvets_catalogrestrictiongroup/adminhtml_catalogrestrictiongroup_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'products',
            array(
                'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Associated products'),
                'url'   => $this->getUrl('*/*/products', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        $this->addTab(
            'customers',
            array(
                'label' => Mage::helper('fvets_catalogrestrictiongroup')->__('Associated customers'),
                'url'   => $this->getUrl('*/*/customers', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve restriction group entity
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    public function getCatalogrestrictiongroup()
    {
        return Mage::registry('current_catalogrestrictiongroup');
    }
}
