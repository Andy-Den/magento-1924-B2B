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
 * Condition resource model
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Model_Resource_Condition extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     */
    public function _construct()
    {
        $this->_init('fvets_payment/condition', 'entity_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @access public
     * @param int $conditionId
     * @return array
     */
    public function lookupStoreIds($conditionId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('fvets_payment/condition_store'), 'store_id')
            ->where('condition_id = ?', (int)$conditionId);
        return $adapter->fetchCol($select);
    }

	/**
	 * Get category ids to which specified item is assigned
	 *
	 * @access public
	 * @param int $conditionId
	 * @return array
	 */
	public function lookupCategoryIds($conditionId)
	{
		$adapter = $this->_getReadAdapter();
		$select  = $adapter->select()
			->from($this->getTable('fvets_payment/condition_category'), 'category_id')
			->where('condition_id = ?', (int)$conditionId);
		return $adapter->fetchCol($select);
	}

    /**
     * Perform operations after object load
     *
     * @access public
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_Payment_Model_Resource_Condition
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
			$categoryIds = $this->lookupCategoryIds($object->getId());
			$categories = array();

			foreach ($categoryIds as $id)
			{
				$categories[$id] = Mage::getModel('catalog/category')->load($id)->getName();
			}

			if (count($categories) > 0)
			{
				$object->setData('category_id', implode(', ', $categories));
			}
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param FVets_Payment_Model_Condition $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('payment_condition_store' => $this->getTable('fvets_payment/condition_store')),
                $this->getMainTable() . '.entity_id = payment_condition_store.condition_id',
                array()
            )
            ->where('payment_condition_store.store_id IN (?)', $storeIds)
            ->order('payment_condition_store.store_id DESC')
            ->limit(1);
        }

		if ($object->getCategoryId()) {
			$categoryIds = array((int)$object->getCategoryId());
			$select->join(
				array('payment_condition_category' => $this->getTable('fvets_payment/condition_category')),
				$this->getMainTable() . '.entity_id = payment_condition_category.condition_id',
				array()
			)
				->where('payment_condition_category.category_id IN (?)', $categoryIds)
				->order('payment_condition_category.category_id DESC')
				->limit(1);
		}
        return $select;
    }

    /**
     * Assign condition to store views
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_Payment_Model_Resource_Condition
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('fvets_payment/condition_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'condition_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                $data[] = array(
                    'condition_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }
    /**
     * process multiple select fields
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return FVets_Payment_Model_Resource_Condition
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $paymentmethods = $object->getPaymentMethods();
        if (is_array($paymentmethods)) {
            $object->setPaymentMethods(implode(',', $paymentmethods));
        }
        $applytogroups = $object->getApplyToGroups();
        if (is_array($applytogroups)) {
            $object->setApplyToGroups(implode(',', $applytogroups));
        }
        return parent::_beforeSave($object);
    }
}
