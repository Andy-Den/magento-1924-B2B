<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule - premier relation model
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Resource_Salesrule_Premier extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Douglas Borella Ianitsky
     */
    protected function  _construct()
    {
        $this->_init('fvets_salesrule/premier', 'rel_id');
    }
    /**
     * Save salesrule - premier relations
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule $salesrule
     * @param array $data
     * @return FVets_SalesRule_Model_Resource_Salesrule_Premier
     * @author Douglas Borella Ianitsky
     */
    public function saveSalesruleRelation($salesrule, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('salesrule_id=?', $salesrule->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

		if (isset($data['newgroup']) && trim($data['newgroup'] != ''))
		{
			$data['group'] = $data['newgroup'];
		}

		$this->_getWriteAdapter()->insert(
			$this->getMainTable(),
			array(
				'salesrule_id'		=> $salesrule->getId(),
				'calculation_type'	=> $data['calculation_type'],
				'from'				=> $data['from'],
				'to'				=> $data['to'],
				'group'				=> (isset($data['group'])) ? $data['group'] : null,
			)
		);
        return $this;
    }
}
