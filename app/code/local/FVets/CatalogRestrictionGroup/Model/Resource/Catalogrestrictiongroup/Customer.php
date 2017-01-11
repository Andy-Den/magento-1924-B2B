<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - customer relation model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Douglas Ianitsky
     */
    protected function  _construct()
    {
        $this->_init('fvets_catalogrestrictiongroup/entity_customer', 'rel_id');
    }
    /**
     * Save restriction group - customer relations
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @param array $data
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer
     * @author Douglas Ianitsky
     */
    public function saveCatalogrestrictiongroupRelation($catalogrestrictiongroup, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('catalogrestrictiongroup_id=?', $catalogrestrictiongroup->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $customerId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'catalogrestrictiongroup_id' => $catalogrestrictiongroup->getId(),
                    'customer_id'    => $customerId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  customer - restriction group relations
     *
     * @access public
     * @param Mage_Customer_Model_Customer $customer
     * @param array $data
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer
     * @@author Douglas Ianitsky
     */
    public function saveCustomerRelation($customer, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('customer_id=?', $customer->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $catalogrestrictiongroupId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'catalogrestrictiongroup_id' => $catalogrestrictiongroupId,
                    'customer_id'    => $customer->getId(),
                    'position'      => (isset($info['position'])) ? $info['position'] : 0
                )
            );
        }
        return $this;
    }
}
