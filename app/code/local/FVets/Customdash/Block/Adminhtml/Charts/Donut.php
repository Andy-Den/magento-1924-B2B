<?php

class FVets_Customdash_Block_Adminhtml_Charts_Donut extends FVets_Customdash_Block_Adminhtml_Business
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('fvets/customdash/charts/donut.phtml');
	}
}
