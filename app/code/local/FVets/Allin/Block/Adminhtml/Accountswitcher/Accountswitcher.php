<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/3/15
 * Time: 10:22 AM
 */
class FVets_Allin_Block_Adminhtml_Accountswitcher_Accountswitcher extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
	}

	public function listAccounts() {
		$accounts = Mage::getModel('fvets_allin/account')->getCollection();
		return $accounts;
	}
}