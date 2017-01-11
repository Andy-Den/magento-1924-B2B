<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 11/7/14
 * Time: 3:23 PM
 */
class FVets_Salesrep_Block_Email extends FVets_Salesrep_Block_Info
{
	public function getCacheKeyInfo()
	{
		return array(
			'fvets_salesrep_block_email',
			Mage::app()->getStore()->getCode(),
			$this->getCustomer()->getId()
		);
	}
}