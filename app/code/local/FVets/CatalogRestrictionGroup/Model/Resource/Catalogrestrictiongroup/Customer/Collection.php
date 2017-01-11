<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - customer relation resource model collection
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer_Collection
     * @author Douglas Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_catalogrestrictiongroup/entity_customer')),
                'related.customer_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add restriction group filter
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup | int $catalogrestrictiongroup
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Customer_Collection
     * @author Douglas Ianitsky
     */
    public function addCatalogrestrictiongroupFilter($catalogrestrictiongroup)
    {
        if ($catalogrestrictiongroup instanceof FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup) {
            $catalogrestrictiongroup = $catalogrestrictiongroup->getId();
        }
        if (!$this->_joinedFields ) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.catalogrestrictiongroup_id = ?', $catalogrestrictiongroup);
        return $this;
    }
}
