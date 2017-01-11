<?php
/**
 * FVets_SalesRule extension
 *
 * @category       FVets
 * @package        FVets_SalesRule
 */

/**
 * SalesRule setup
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Júlio Reis
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_SalesRule_Validator extends Mage_SalesRule_Model_Validator
{
    protected $_customer = null;

    /**
     * Quote item discount calculation process
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_SalesRule_Model_Validator
     */
    public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $quote = $item->getQuote();
        $address = $this->_getAddress($item);

        $itemPrice = $this->_getItemPrice($item);
        $baseItemPrice = $this->_getItemBasePrice($item);
        $itemOriginalPrice = $this->_getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->_getItemBaseOriginalPrice($item);

        if ($itemPrice < 0) {
            return $this;
        }

        $appliedRuleIds = array();
        $this->_stopFurtherRules = false;
        foreach ($this->_getRules() as $rule) {

            /* @var $rule Mage_SalesRule_Model_Rule */
            if (!$this->_canProcessRule($rule, $address)) {
                continue;
            }

            if (!$rule->getActions()->validate($item)) {
                continue;
            }

            $qty = $this->_getItemQty($item, $rule);

            if (Mage::getSingleton('checkout/session')->getPaymentConditionSplit() > 1 && $rule->getStallmentDiscountAmount() > 0) {
                $rulePercent = min(100, $rule->getStallmentDiscountAmount());
            } else {
                $rulePercent = min(100, $rule->getDiscountAmount());
            }

            $discountAmount = 0;
            $baseDiscountAmount = 0;
            //discount for original price
            $originalDiscountAmount = 0;
            $baseOriginalDiscountAmount = 0;

            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100 - $rule->getDiscountAmount());
                //no break;
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty / $step) * $step;
                    }
                    $_rulePct = $rulePercent / 100;
                    $discountAmount = ($qty * $itemPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseDiscountAmount = ($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct;
                    //get discount for original price
                    $originalDiscountAmount = ($qty * $itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;
                    $baseOriginalDiscountAmount =
                        ($qty * $baseItemOriginalPrice - $item->getDiscountAmount()) * $_rulePct;

                    if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
                        $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent);
                        $item->setDiscountPercent($discountPercent);
                    }
                    break;
                case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount = $qty * ($itemPrice - $quoteAmount);
                    $baseDiscountAmount = $qty * ($baseItemPrice - $rule->getDiscountAmount());
                    //get discount for original price
                    $originalDiscountAmount = $qty * ($itemOriginalPrice - $quoteAmount);
                    $baseOriginalDiscountAmount = $qty * ($baseItemOriginalPrice - $rule->getDiscountAmount());
                    break;

                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $step = $rule->getDiscountStep();
                    if ($step) {
                        $qty = floor($qty / $step) * $step;
                    }
                    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount = $qty * $quoteAmount;
                    $baseDiscountAmount = $qty * $rule->getDiscountAmount();
                    break;

                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    if (empty($this->_rulesItemTotals[$rule->getId()])) {
                        Mage::throwException(Mage::helper('salesrule')->__('Item totals are not set for rule.'));
                    }

                    /**
                     * prevent applying whole cart discount for every shipping order, but only for first order
                     */
                    if ($quote->getIsMultiShipping()) {
                        $usedForAddressId = $this->getCartFixedRuleUsedForAddress($rule->getId());
                        if ($usedForAddressId && $usedForAddressId != $address->getId()) {
                            break;
                        } else {
                            $this->setCartFixedRuleUsedForAddress($rule->getId(), $address->getId());
                        }
                    }
                    $cartRules = $address->getCartFixedRules();
                    if (!isset($cartRules[$rule->getId()])) {
                        $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }

                    if ($cartRules[$rule->getId()] > 0) {
                        if ($this->_rulesItemTotals[$rule->getId()]['items_count'] <= 1) {
                            $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                            $baseDiscountAmount = min($baseItemPrice * $qty, $cartRules[$rule->getId()]);
                        } else {
                            $discountRate = $baseItemPrice * $qty /
                                $this->_rulesItemTotals[$rule->getId()]['base_items_price'];
                            $maximumItemDiscount = $rule->getDiscountAmount() * $discountRate;
                            $quoteAmount = $quote->getStore()->convertPrice($maximumItemDiscount);

                            $baseDiscountAmount = min($baseItemPrice * $qty, $maximumItemDiscount);
                            $this->_rulesItemTotals[$rule->getId()]['items_count']--;
                        }

                        $discountAmount = min($itemPrice * $qty, $quoteAmount);
                        $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                        $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);

                        //get discount for original price
                        $originalDiscountAmount = min($itemOriginalPrice * $qty, $quoteAmount);
                        $baseOriginalDiscountAmount = $quote->getStore()->roundPrice($baseItemOriginalPrice);

                        $cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }
                    $address->setCartFixedRules($cartRules);

                    break;

                case Mage_SalesRule_Model_Rule::BUY_X_GET_Y_ACTION:
                    $x = $rule->getDiscountStep();
                    $y = $rule->getDiscountAmount();
                    if (!$x || $y > $x) {
                        break;
                    }
                    $buyAndDiscountQty = $x + $y;

                    $fullRuleQtyPeriod = floor($qty / $buyAndDiscountQty);
                    $freeQty = $qty - $fullRuleQtyPeriod * $buyAndDiscountQty;

                    $discountQty = $fullRuleQtyPeriod * $y;
                    if ($freeQty > $x) {
                        $discountQty += $freeQty - $x;
                    }

                    $discountAmount = $discountQty * $itemPrice;
                    $baseDiscountAmount = $discountQty * $baseItemPrice;
                    //get discount for original price
                    $originalDiscountAmount = $discountQty * $itemOriginalPrice;
                    $baseOriginalDiscountAmount = $discountQty * $baseItemOriginalPrice;
                    break;
            }

            $result = new Varien_Object(array(
                'discount_amount' => $discountAmount,
                'base_discount_amount' => $baseDiscountAmount,
            ));
            Mage::dispatchEvent('salesrule_validator_process', array(
                'rule' => $rule,
                'item' => $item,
                'address' => $address,
                'quote' => $quote,
                'qty' => $qty,
                'result' => $result,
            ));

            $discountAmount = $result->getDiscountAmount();
            $baseDiscountAmount = $result->getBaseDiscountAmount();

            $percentKey = $item->getDiscountPercent();
            /**
             * Process "delta" rounding
             */
            if ($percentKey) {
                $delta = isset($this->_roundingDeltas[$percentKey]) ? $this->_roundingDeltas[$percentKey] : 0;
                $baseDelta = isset($this->_baseRoundingDeltas[$percentKey])
                    ? $this->_baseRoundingDeltas[$percentKey]
                    : 0;
                $discountAmount += $delta;
                $baseDiscountAmount += $baseDelta;

                $this->_roundingDeltas[$percentKey] = $discountAmount -
                    $quote->getStore()->roundPrice($discountAmount);
                $this->_baseRoundingDeltas[$percentKey] = $baseDiscountAmount -
                    $quote->getStore()->roundPrice($baseDiscountAmount);
                $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            } else {
                $discountAmount = $quote->getStore()->roundPrice($discountAmount);
                $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            }

            /**
             * We can't use row total here because row total not include tax
             * Discount can be applied on price included tax
             */

            $itemDiscountAmount = $item->getDiscountAmount();
            $itemBaseDiscountAmount = $item->getBaseDiscountAmount();

            $discountAmount = min($itemDiscountAmount + $discountAmount, $itemPrice * $qty);
            $baseDiscountAmount = min($itemBaseDiscountAmount + $baseDiscountAmount, $baseItemPrice * $qty);

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

            $item->setOriginalDiscountAmount($originalDiscountAmount);
            $item->setBaseOriginalDiscountAmount($baseOriginalDiscountAmount);

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            $this->_maintainAddressCouponCode($address, $rule);
            $this->_addDiscountDescription($address, $rule);

            //Adicionar tipo de desconto, para criar o total
            if (is_array($address->getDiscountType('discount_' . $rule->getSimpleAction())['rules'])) {
                $array = $address->getDiscountType('discount_' . $rule->getSimpleAction())['rules'];
                $array[$rule->getRuleId()] = $rule;
            } else {
                $array[$rule->getRuleId()] = $rule;
            }
            $address->addDiscountType('discount_' . $rule->getSimpleAction(),
                array(
                    'code' => 'discount_' . $rule->getSimpleAction(),
                    'title' => Mage::helper('sales')->__('Discount ' . $rule->getSimpleAction()),
                    'percent' => $rule->getDiscountAmount(),
                    'value' => $address->getDiscountType('discount_' . $rule->getSimpleAction())['value'] + $item->getOriginalDiscountAmount(),
                    'rules' => $array
                )
            );

            if ($rule->getStopRulesProcessing()) {
                $this->_stopFurtherRules = true;
                break;
            }
        }

        $item->setAppliedRuleIds(join(',', $appliedRuleIds));
        $address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param   Mage_SalesRule_Model_Rule $rule
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  bool
     */
    protected function _canProcessRule($rule, $address)
    {
        if (!$this->isCustomerAllowedRule($rule)) {
            return false;
        }

        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        if ((bool)$rule->getDaysFromLastOrder()) {
            if ($this->getDaysFromLastOrder() > $rule->getDaysFromLastOrder()) {
                return false;
            }
        }

        if ((bool)$rule->getOnlyOnSale()) {
            if ($rule->getOnlyOnSale() != ($this->getOrdersQty() + 1)) {
                return false;
            }
        }

        /**
         * check per coupon usage limit
         */
        if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $coupon = Mage::getModel('salesrule/coupon');
                $coupon->load($couponCode, 'code');
                if ($coupon->getId()) {
                    // check entire usage limit
                    if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {

                        $rule->setIsValidForAddress($address, false);
                        return false;
                    }
                    // check per customer usage limit
                    $customerId = $address->getQuote()->getCustomerId();
                    if ($customerId && $coupon->getUsagePerCustomer()) {
                        $couponUsage = new Varien_Object();
                        Mage::getResourceModel('salesrule/coupon_usage')->loadByCustomerCoupon(
                            $couponUsage, $customerId, $coupon->getId());
                        if ($couponUsage->getCouponId() &&
                            $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            $rule->setIsValidForAddress($address, false);
                            return false;
                        }
                    }
                }
            }
        }

        /**
         * check per rule usage limit
         */
        $ruleId = $rule->getId();
        if ($ruleId && $rule->getUsesPerCustomer()) {
            $customerId = $address->getQuote()->getCustomerId();
            $ruleCustomer = Mage::getModel('salesrule/rule_customer');
            $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
            if ($ruleCustomer->getId()) {
                if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                    $rule->setIsValidForAddress($address, false);
                    return false;
                }
            }
        }
        $rule->afterLoad();
        /**
         * quote does not meet rule's conditions
         */
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            return false;
        }
        /**
         * passed all validations, remember to be valid
         */
        $rule->setIsValidForAddress($address, true);
        return true;
    }

    private function getDaysFromLastOrder()
    {
        $days = Mage::registry('days_from_last_order');

        if (!isset($days)) {
            $customer = $this->getCustomer();

            $order = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('status', array('neq' => 'canceled'))
                ->setOrder('main_table.entity_id', 'desc')
                ->getFirstItem();

            if ($order->getId()) {

                $now = time(); // or your date as well
                $your_date = strtotime($order->getCreatedAt());
                $datediff = $now - $your_date;

                $days = floor($datediff / (60 * 60 * 24));
            } else {
                $days = 0;
            }

            Mage::register('days_from_last_order', $days);
        }

        return $days;
    }

    private function getOrdersQty()
    {
        $qty = Mage::registry('orders_qty');

        if (!isset($qty)) {
            $customer = $this->getCustomer();

            $orders = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('status', array('neq' => 'canceled'));

            $qty = $orders->count();

            Mage::register('orders_qty', $qty);
        }

        return $qty;
    }

    protected function isCustomerAllowedRule($rule, $customer = null)
    {
        if (!isset($customer)) {
            $customer = $this->getCustomer();
        }

        if ($customer->getIgnorePromos()) {
            if (!$customer->hasAllowedRules()) {
                $rules = Mage::getModel('salesrule/rule')
                    ->getCollection()
                    ->addCustomerFilter($customer);
                $customer->setAllowedRules($rules->getAllIds());
            }

            return in_array($rule->getId(), $customer->getAllowedRules());
        }

        return true;
    }

    protected function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        return $this->_customer;
    }
}