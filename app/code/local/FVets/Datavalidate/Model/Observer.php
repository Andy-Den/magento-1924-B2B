<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/19/15
 * Time: 6:44 PM
 */
class FVets_Datavalidate_Model_Observer
{
	public function executeDataValidation()
	{
		Mage::helper('datavalidate/validate')->executeDataValidation();
	}
}