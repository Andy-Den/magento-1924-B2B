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
 * Condition list on customer page block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Customer_List_Condition extends Mage_Customer_Block_Account
{
    /**
     * get the list of conditions
     *
     * @access protected
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function getConditionCollection()
    {
        if (!$this->hasData('condition_collection')) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $collection = Mage::getResourceSingleton('fvets_payment/condition_collection')
                ->addStoreFilter(Mage::app()->getStore())
                ->addFieldToFilter('status', 1)
                ->addCustomerFilter($customer);
            $collection->getSelect()->order('related_customer.position', 'ASC');
            $this->setData('condition_collection', $collection);
        }
        return $this->getData('condition_collection');
    }
}
