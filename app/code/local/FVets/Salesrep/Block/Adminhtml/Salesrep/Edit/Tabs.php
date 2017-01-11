<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Sales Rep admin edit tabs
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('salesrep_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fvets_salesrep')->__('Sales Rep'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tabs
     * @author Douglas Borella Ianitsky
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_salesrep',
            array(
                'label'   => Mage::helper('fvets_salesrep')->__('Sales Rep'),
                'title'   => Mage::helper('fvets_salesrep')->__('Sales Rep'),
                'content' => $this->getLayout()->createBlock(
                    'fvets_salesrep/adminhtml_salesrep_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('fvets_salesrep')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
		$this->addTab(
			'customers',
			array(
				'label' => Mage::helper('fvets_payment')->__('Associated customers'),
				'url'   => $this->getUrl('*/*/customers', array('_current' => true)),
				'class' => 'ajax'
			)
		);
		$this->addTab(
			'regions',
			array(
				'label' => Mage::helper('fvets_salesrep')->__('Associated regions'),
				'url'   => $this->getUrl('*/*/regions', array('_current' => true)),
				'class' => 'ajax'
			)
		);
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve sales rep entity
     *
     * @access public
     * @return FVets_Salesrep_Model_Salesrep
     * @author Douglas Borella Ianitsky
     */
    public function getSalesrep()
    {
        return Mage::registry('fvets_salesrep');
    }
}
