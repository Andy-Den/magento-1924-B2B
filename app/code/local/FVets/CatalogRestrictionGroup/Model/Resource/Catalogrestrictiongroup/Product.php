<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - product relation model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Product extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('fvets_catalogrestrictiongroup/entity_product', 'rel_id');
    }
    /**
     * Save restriction group - product relations
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup $catalogrestrictiongroup
     * @param array $data
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Product
     * @author Douglas Ianitsky
     */
    public function saveCatalogrestrictiongroupRelation($catalogrestrictiongroup, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('catalogrestrictiongroup_id=?', $catalogrestrictiongroup->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'catalogrestrictiongroup_id' => $catalogrestrictiongroup->getId(),
                    'product_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  product - restriction group relations
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return FVets_CatalogRestrictionGroup_Model_Resource_Catalogrestrictiongroup_Product
     * @@author Douglas Ianitsky
     */
    public function saveProductRelation($product, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $catalogrestrictiongroupId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'catalogrestrictiongroup_id' => $catalogrestrictiongroupId,
                    'product_id'    => $product->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }
}
