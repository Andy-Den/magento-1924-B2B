<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu resource model
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Model_Resource_Attributemenu extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        $this->_init('fvets_attributemenu/entity', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $attributemenuId
     * @return array
     * @author Ultimate Module Creator
     */
    public function lookupStoreIds($attributemenuId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('fvets_attributemenu/entity_store'), 'store_id')
            ->where('attributemenu_id = ?', (int)$attributemenuId);
        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_AttributeMenu_Model_Resource_Attributemenu
     * @author Ultimate Module Creator
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param FVets_AttributeMenu_Model_Attributemenu $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('attributemenu_attributemenu_store' => $this->getTable('fvets_attributemenu/entity_store')),
                $this->getMainTable() . '.entity_id = attributemenu_attributemenu_store.attributemenu_id',
                array()
            )
            ->where('attributemenu_attributemenu_store.store_id IN (?)', $storeIds)
            ->order('attributemenu_attributemenu_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Assign attributemenu to store views
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_AttributeMenu_Model_Resource_Attributemenu
     * @author Ultimate Module Creator
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('fvets_attributemenu/entity_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'attributemenu_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);

            Mage::helper('fvets_attributemenu')->deleteRewriteRule($object, $delete);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'attributemenu_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);

            Mage::helper('fvets_attributemenu')->createRewriteRule($object, $insert);
        }

        return parent::_afterSave($object);
    }
    /**
     * process multiple select fields
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_AttributeMenu_Model_Resource_Attributemenu
     * @author Ultimate Module Creator
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $value = $object->getValue();
        if (is_array($value)) {
            $object->setValue(implode(',', $value));
        }
        return parent::_beforeSave($object);
    }
}
