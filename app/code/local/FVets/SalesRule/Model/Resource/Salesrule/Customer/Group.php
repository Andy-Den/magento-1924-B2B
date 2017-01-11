<?php
/**
 * FVets_SalesRule extension
 *
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule - customer relation model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Resource_Salesrule_Customer_Group extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * initialize resource model
	 *
	 * @access protected
	 * @see Mage_Core_Model_Resource_Abstract::_construct()
	 * @author Douglas Borella Ianitsky
	 */
	protected function  _construct()
	{
		$this->_init('salesrule/customer_group', 'rule_id');
	}
}
