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
class FVets_SalesRule_Model_Resource_Salesrule_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
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
     * @return FVets_SalesRule_Model_Resource_Salesrule_Customer_Collection
     * @author Douglas Borella Ianitsky
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('fvets_salesrule/customer')),
                'related.customer_id = e.entity_id',
                array('rel_id', 'position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add salesrule filter
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule | int $salesrule
     * @return FVets_SalesRule_Model_Resource_Salesrule_Customer_Collection
     * @author Douglas Borella Ianitsky
     */
    public function addSalesruleFilter($salesrule)
    {
        if ($salesrule instanceof Mage_SalesRule_Model_Rule) {
            $salesrule = $salesrule->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        if (is_array($salesrule)) {
            $this->getSelect()->where('related.salesrule_id IN (?)', implode(',', $salesrule));
        } else {
            $this->getSelect()->where('related.salesrule_id = ?', $salesrule);
        }

        return $this;
    }
}
