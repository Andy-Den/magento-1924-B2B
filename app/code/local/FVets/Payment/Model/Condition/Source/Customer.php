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
 * Condition source model for customer form
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Condition_Source_Customer extends FVets_Payment_Model_Condition_Source
{
	/**
	 * Get all options
	 *
	 * @access public
	 * @param bool $withEmpty
	 * @return array
	 */
	public function getAllOptions($withEmpty = false)
	{
		if (is_null($this->_options))
		{
			$param = (Mage::app()->getRequest()->getParam('id')) ? Mage::app()->getRequest()->getParam('id') : Mage::app()->getRequest()->getParam('customer_id') ;
			$customer =  Mage::getModel("customer/customer")->load($param);
			
			$this->_options = Mage::getResourceModel('fvets_payment/condition_collection')
				->addWebsiteFilter($customer->getWebsiteId())
				->setOrder('name', 'ASC')
				->load()
				->toOptionArray();
							
		}
		$options = $this->_options;
		if ($withEmpty) {
			array_unshift($options, array('value'=>'', 'label'=>''));
		}
		return $options;
	}
}
