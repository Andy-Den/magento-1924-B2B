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
 * Customer helper
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Helper_Customer extends FVets_Payment_Helper_Data
{

    /**
     * get the selected conditions for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return array()
     */
    public function getSelectedConditions(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->hasSelectedConditions()) {
            $conditions = array();
            foreach ($this->getSelectedConditionsCollection($customer) as $condition) {
                $conditions[] = $condition;
            }
            $customer->setSelectedConditions($conditions);
        }
        return $customer->getData('selected_conditions');
    }

    /**
     * get condition collection for a customer
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @return FVets_Payment_Model_Resource_Condition_Collection
     */
    public function getSelectedConditionsCollection(Mage_Customer_Model_Customer $customer)
    {
        $collection = Mage::getResourceSingleton('fvets_payment/condition_collection')
            ->addAdminCustomerFilter($customer);
        return $collection;
    }

}
