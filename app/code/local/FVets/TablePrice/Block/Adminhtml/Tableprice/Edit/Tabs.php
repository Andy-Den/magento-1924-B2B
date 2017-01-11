<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price admin edit tabs
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Tableprice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('tableprice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fvets_tableprice')->__('Table Price'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Edit_Tabs
     * @author Douglas Ianitsky
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_tableprice',
            array(
                'label'   => Mage::helper('fvets_tableprice')->__('Table Price'),
                'title'   => Mage::helper('fvets_tableprice')->__('Table Price'),
                'content' => $this->getLayout()->createBlock(
                    'fvets_tableprice/adminhtml_tableprice_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('fvets_tableprice')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve table price entity
     *
     * @access public
     * @return FVets_TablePrice_Model_Tableprice
     * @author Douglas Ianitsky
     */
    public function getTableprice()
    {
        return Mage::registry('current_tableprice');
    }
}
