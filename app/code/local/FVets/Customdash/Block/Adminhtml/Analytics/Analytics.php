<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/15/16
 * Time: 9:06 AM
 */
class FVets_Customdash_Block_Adminhtml_Analytics_Analytics extends FVets_Customdash_Block_Adminhtml_Business
{
  public function __construct()
  {
	parent::__construct();
	$this->setTemplate('fvets/customdash/analytics/analytics.phtml');
  }
}
