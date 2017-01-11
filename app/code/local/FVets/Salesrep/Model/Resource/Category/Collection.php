<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Sales Rep - Category relation resource model collection
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Resource_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
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
     * @return FVets_Salesrep_Model_Resource_Category_Collection
     * @author Douglas Borella Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_salesrep/category')),
                'related.category_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add sales rep filter
     *
     * @access public
     * @param FVets_Salesrep_Model_Salesrep | int $salesrep
     * @return FVets_Salesrep_Model_Resource_Salesrep_Category_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addSalesrepFilter($salesrep)
    {
        if ($salesrep instanceof FVets_Salesrep_Model_Salesrep) {
            $salesrep = $salesrep->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.salesrep_id = ?', $salesrep);
        return $this;
    }
}
