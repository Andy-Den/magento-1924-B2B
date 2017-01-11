<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition admin edit tabs
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
class FVets_Payment_Block_Adminhtml_Condition_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('condition_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('fvets_payment')->__('Condition'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_condition',
            array(
                'label'   => Mage::helper('fvets_payment')->__('Condition'),
                'title'   => Mage::helper('fvets_payment')->__('Condition'),
                'content' => $this->getLayout()->createBlock(
                    'fvets_payment/adminhtml_condition_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_condition',
                array(
                    'label'   => Mage::helper('fvets_payment')->__('Store views'),
                    'title'   => Mage::helper('fvets_payment')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'fvets_payment/adminhtml_condition_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        $this->addTab(
            'customers',
            array(
                'label' => Mage::helper('fvets_payment')->__('Associated customers'),
                'url'   => $this->getUrl('*/*/customers', array('_current' => true)),
                'class' => 'ajax'
            )
        );
		$this->addTab(
			'categories',
			array(
				'label' => Mage::helper('fvets_payment')->__('Só mostrar quando contiver as marcas'),
				'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
				'class' => 'ajax'
			)
		);
		$this->addTab(
			'exclude',
			array(
				'label' => Mage::helper('fvets_payment')->__('Nunca mostrar quando contiver as marcas'),
				'url'   => $this->getUrl('*/*/excluded', array('_current' => true)),
				'class' => 'ajax'
			)
		);
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve condition entity
     *
     * @access public
     * @return FVets_Payment_Model_Condition
     */
    public function getCondition()
    {
        return Mage::registry('current_condition');
    }
}
