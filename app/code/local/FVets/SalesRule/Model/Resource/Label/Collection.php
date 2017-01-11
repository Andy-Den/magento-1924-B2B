<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/6/15
 * Time: 3:41 PM
 */
class FVets_SalesRule_Model_Resource_Label_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('fvets_salesrule/label');
	}

}