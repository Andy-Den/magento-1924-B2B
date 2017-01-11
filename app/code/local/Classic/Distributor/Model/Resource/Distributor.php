<?php
/**
 * Classic_Distributor extension
 * 
 * NOTICE OF LICENSE
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Distributor resource model
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Resource_Distributor extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */
    public function _construct()
    {
        $this->_init('classic_distributor/distributor', 'entity_id');
    }

    /**
     * process multiple select fields
     *
     * @access protected
     * @param Mage_Core_Model_Abstract $object
     * @return Classic_Distributor_Model_Resource_Distributor
     * @author Douglas Borella Ianitsky
     */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		$brands = $object->getBrands();
		if (is_array($brands))
		{
			$object->setBrands(implode(',', $brands));
		}

		if ($object->getWebsite() == '')
		{
			$object->setWebsite(null);
		}

		return parent::_beforeSave($object);
	}
}
