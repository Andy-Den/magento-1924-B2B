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
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    protected function _canAddTab($customer)
    {
        if ($customer->getId()) {
            return true;
        }
        if (!$customer->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the condition tab to customers
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_Payment_Model_Adminhtml_Observer
     */
    public function addCustomerConditionBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs) {
            $block->addTabAfter(
                'conditions',
                array(
                    'label' => Mage::helper('fvets_payment')->__('Conditions'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/payment_condition_customer/conditions',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                ),
				'addresses'
            );
        }
        return $this;
    }

    /**
     * save condition - customer relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_Payment_Model_Adminhtml_Observer
     */
    public function saveCustomerConditionData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('conditions', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $customer = $observer->getCustomer();
            $conditionCustomer = Mage::getResourceSingleton('fvets_payment/condition_customer')
                ->saveCustomerRelation($customer, $post);
        }
        return $this;
    }}
