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
 * Payment default helper
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Helper_Data extends Mage_Payment_Helper_Data
{

	const XML_PAYMENT_CASHONDELIVERY_PAYMENTCONDITIONS = 'payment/cashondelivery/payment_conditions';

	const XML_PAYMENTCONDITION_ORDERBY = 'payment_condition/general/orderby';

	private $_conditions = null;

	/**
	 * Retreive payment method form html
	 *
	 * @param   Mage_Payment_Model_Abstract $method
	 * @return  Mage_Payment_Block_Form
	 */
	public function getMethodFormBlock(Mage_Payment_Model_Method_Abstract $method)
	{
		$block = false;
		$blockType = $method->getFormBlockType();
		if ($this->getLayout()) {
			$block = $this->getLayout()->createBlock($blockType, 'choose-payment-method-'.$method->getCode());
			$block->setMethod($method);
		}
		return $block;
	}

	function getCashondeliveryConditions()
	{
		if (!isset($this->_conditions))
		{
			$conditions = trim(Mage::getStoreConfig(self::XML_PAYMENT_CASHONDELIVERY_PAYMENTCONDITIONS));
			if (strlen($conditions) > 0) {
				$this->_conditions = explode("\n", $conditions);
				for ($i = 0; $i < count($this->_conditions); $i++) {
					if (strpos($this->_conditions[$i], "|")) {
						$conditions = explode("|", $this->_conditions[$i]);
						$this->_conditions[$i] = array('id' => $conditions[0], 'value' => $conditions[1]);
					} else {
						$this->_conditions[$i] = array('value' => $this->_conditions[$i]);
					}
				}
			}
			else
				return false;
		}

		return $this->_conditions;
	}

    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

	public function createExpireDate($index, $condition)
	{
		$date = new DateTime('NOW');
		//Add days to first split
		date_add($date, date_interval_create_from_date_string($condition->getStartDays() . ' days'));
		//Add additional days to split
		date_add($date, date_interval_create_from_date_string($condition->getSplitRange() * $index . ' days'));

		return $date->format('Y-m-d');
	}

}