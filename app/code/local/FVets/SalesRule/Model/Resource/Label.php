<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/6/15
 * Time: 6:20 PM
 */

class FVets_SalesRule_Model_Resource_Label extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct()
	{
		$this->_init('fvets_salesrule/label', 'label_id');
	}

}