<?php
class FVets_Page_Helper_Rule extends Mage_Core_Helper_Abstract
{
	/**
	 * return coupon rule if applied for specific customer
	 *
	 * @param   Mage_SalesRule_Model_Coupon $coupon
	 * @param   int $customerId
	 * @return  bool
	 */
	public function getCouponRule($coupon, $customerId)
	{
		$rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());

		/**
		 * check if coupon is active
		 */
		if (!(bool)$rule->getIsActive())
		{
			return false;
		}

		/**
		 * check if coupon is valid to store
		 */
		$storeId = Mage::app()->getStore()->getStoreId();
		$websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();

		if (!in_array($websiteId, $rule->getWebsiteIds()))
		{
			return false;
		}

		/**
		 * check if coupon is valid to customer group
		 */
		$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		if (!in_array($customerGroupId, $rule->getCustomerGroupIds()))
		{
			return false;
		}

		/**
		 * check if coupon is valid to date
		 */
		if ($rule->hasFromDate() && $rule->hasToDate()) {
			$fromDate = new Zend_Date($rule->getFromDate(), Varien_Date::DATE_INTERNAL_FORMAT);
			$toDate = new Zend_Date($rule->getToDate(), Varien_Date::DATE_INTERNAL_FORMAT);
			$today = new Zend_Date(date('Y-m-d'), Varien_Date::DATE_INTERNAL_FORMAT);

			if ($today->compare($fromDate) < 0 || $today->compare($toDate) > 0)
			{
				return false;
			}

		}

		/**
		 * check per coupon usage limit
		 */

		if ($coupon->getId()) {
			// check entire usage limit
			if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
				return false;
			}
			// check per customer usage limit
			if ($customerId && $coupon->getUsagePerCustomer()) {
				$couponUsage = new Varien_Object();
				Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon(
					$couponUsage, $customerId, $coupon->getId());
				if ($couponUsage->getCouponId() &&
					$couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
				) {
					return false;
				}
			}
		}

		/**
		 * check per rule usage limit
		 */
		$ruleId = $rule->getId();
		if ($ruleId && $rule->getUsesPerCustomer()) {
			$ruleCustomer   = Mage::getModel('salesrule/rule_customer');
			$ruleCustomer->loadByCustomerRule($customerId, $ruleId);
			if ($ruleCustomer->getId()) {
				if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
					return false;
				}
			}
		}
		$rule->afterLoad();
		/**
		 * passed all validations, remember to be valid
		 */
		return $rule;
	}
}