<?php

class FVets_SalesRule_Model_SalesRule_Resource_Rule extends Mage_SalesRule_Model_Resource_Rule
{

	public function setActualProductAttributes($rule, $attributes)
	{
		$write = $this->_getWriteAdapter();
		$write->delete($this->getTable('salesrule/product_attribute'), array('rule_id=?' => $rule->getId()));

		//Getting attribute IDs for attribute codes
		$attributeIds = array();
		$select = $this->_getReadAdapter()->select()
			->from(array('a' => $this->getTable('eav/attribute')), array('a.attribute_id'))
			->where('a.attribute_code IN (?)', array($attributes));
		$attributesFound = $this->_getReadAdapter()->fetchAll($select);
		if ($attributesFound) {
			foreach ($attributesFound as $attribute) {
				$attributeIds[] = $attribute['attribute_id'];
			}

			$data = array();
			if (count($rule->getCustomerGroupIds()) > 0)
			{
				foreach ($rule->getCustomerGroupIds() as $customerGroupId) {
					foreach ($rule->getWebsiteIds() as $websiteId) {
						foreach ($attributeIds as $attribute) {
							$data[] = array (
								'rule_id'           => $rule->getId(),
								'website_id'        => $websiteId,
								'customer_group_id' => $customerGroupId,
								'attribute_id'      => $attribute
							);
						}
					}
				}
			} else {
				foreach ($rule->getWebsiteIds() as $websiteId) {
					foreach ($attributeIds as $attribute) {
						$data[] = array (
							'rule_id'           => $rule->getId(),
							'website_id'        => $websiteId,
							'customer_group_id' => 0,
							'attribute_id'      => $attribute
						);
					}
				}
			}
			$write->insertMultiple($this->getTable('salesrule/product_attribute'), $data);
		}

		return $this;
	}
}