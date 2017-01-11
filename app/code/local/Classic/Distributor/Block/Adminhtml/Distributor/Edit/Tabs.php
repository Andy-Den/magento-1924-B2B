<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor admin edit tabs
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Distributor_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('distributor_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('classic_distributor')->__('Distributor'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Classic_Distributor_Block_Adminhtml_Distributor_Edit_Tabs
     * @author Douglas Borella Ianitsky
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_distributor',
            array(
                'label'   => Mage::helper('classic_distributor')->__('Distributor'),
                'title'   => Mage::helper('classic_distributor')->__('Distributor'),
                'content' => $this->getLayout()->createBlock(
                    'classic_distributor/adminhtml_distributor_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'regions',
            array(
                'label' => Mage::helper('classic_distributor')->__('Associated regions'),
                'url'   => $this->getUrl('*/*/regions', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve distributor entity
     *
     * @access public
     * @return Classic_Distributor_Model_Distributor
     * @author Douglas Borella Ianitsky
     */
    public function getDistributor()
    {
        return Mage::registry('current_distributor');
    }
}
