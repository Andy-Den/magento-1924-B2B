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
 * Condition list block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Condition_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $conditions = Mage::getResourceModel('fvets_payment/condition_collection')
                         ->addStoreFilter(Mage::app()->getStore())
                         ->addFieldToFilter('status', 1);
        $conditions->setOrder('name', 'asc');
        $this->setConditions($conditions);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return FVets_Payment_Block_Condition_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'fvets_payment.condition.html.pager'
        )
        ->setCollection($this->getConditions());
        $this->setChild('pager', $pager);
        $this->getConditions()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
