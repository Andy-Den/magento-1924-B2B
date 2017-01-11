<?php

class FVets_Salesrep_Model_Resource_Salesrep_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fvets_salesrep/salesrep');
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray();
    }

    /**
     * get sales representatives as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField='id', $labelField='name', $additional=array())
    {
        $data = parent::_toOptionArray($valueField, $labelField, $additional);
        array_unshift($data, array('value' => '', 'label' => Mage::helper('core')->__('Please select')));
        return $data;
    }

    /**
	* Select the salesrep with current stores
	*/
	public function addStoresToFilter($stores)
	{
		$select = $this->getSelect();
		if (is_array($stores))
		{
			foreach ($stores as $storeId) {
				$arrayTemp[] = array('finset' => $storeId);
			}
			$this->addFieldToFilter('store_id', $arrayTemp);
		}
		else
		{
			$select->where('SELECT FIND_IN_SET((?),main_table.store_id)', $stores);
		}

		return $this;
	}

	/**
	* Select the salesrep with current store
	*/
	public function addStoreToFilter($store)
	{
		$this->getSelect()
			->where('SELECT FIND_IN_SET((?),main_table.store_id)', $store);

		return $this;
	}

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array
     */
    protected function _toOptionHash($valueField='id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    /**
     * add the category filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Category|int) $category
     * @return FVets_Salesrep_Model_Resource_Salesrep_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])) {
            $this->getSelect()->join(
                array('related_category' => $this->getTable('fvets_salesrep/category')),
                'related_category.salesrep_id = main_table.id',
                array('position')
            );

			if (is_array($category)) {
				$this->getSelect()->where('related_category.category_id IN (?)', $category);
			} else {
				$this->getSelect()->where('related_category.category_id = ?', $category);
			}

            $this->_joinedFields['category'] = true;
        }
        return $this;
    }

	public function addCategoryToFilter($category) {
		return $this->addCategoryFilter($category);
	}

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select
     * @author Douglas Borella Ianitsky
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
