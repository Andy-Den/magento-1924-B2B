<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/9/15
 * Time: 6:48 PM
 */
class FVets_Customdash_Block_Adminhtml_Switcher_Storeswitcher extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('fvets/customdash/switcher/storeswitcher.phtml');
	}
}