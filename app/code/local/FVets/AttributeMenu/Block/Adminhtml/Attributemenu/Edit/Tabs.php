<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu admin edit tabs
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('attributemenu_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fvets_attributemenu')->__('AttributeMenu'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return FVets_AttributeMenu_Block_Adminhtml_Attributemenu_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_attributemenu',
            array(
                'label'   => Mage::helper('fvets_attributemenu')->__('AttributeMenu'),
                'title'   => Mage::helper('fvets_attributemenu')->__('AttributeMenu'),
                'content' => $this->getLayout()->createBlock(
                    'fvets_attributemenu/adminhtml_attributemenu_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_attributemenu',
                array(
                    'label'   => Mage::helper('fvets_attributemenu')->__('Store views'),
                    'title'   => Mage::helper('fvets_attributemenu')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'fvets_attributemenu/adminhtml_attributemenu_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve attributemenu entity
     *
     * @access public
     * @return FVets_AttributeMenu_Model_Attributemenu
     * @author Ultimate Module Creator
     */
    public function getAttributemenu()
    {
        return Mage::registry('current_attributemenu');
    }
}
