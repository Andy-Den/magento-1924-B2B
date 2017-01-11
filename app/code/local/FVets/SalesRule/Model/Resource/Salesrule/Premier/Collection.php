<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule - customer relation resource model collection
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Resource_Salesrule_Premier_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		$this->_init('fvets_salesrule/salesrule_premier');
	}

	/**
	 * Return all groups from same rule website
	 *
	 * @access public
	 * @return FVets_SalesRule_Model_Resource_Salesrule_Customer_Collection
	 * @author Douglas Borella Ianitsky
	 */
	public function getGroupsOptionHash()
	{
		$name='premier_policy_group';
		$attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($name)->getFirstItem();
		$attributeId = $attributeInfo->getAttributeId();
		$attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
		$attributeOptions = $attribute ->getSource()->getAllOptions(false);

		return $attributeOptions;
	}
}
