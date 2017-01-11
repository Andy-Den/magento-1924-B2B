<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/20/15
 * Time: 2:09 PM
 */
class FVets_Datavalidate_IndexController extends Mage_Core_Controller_Front_Action
{

	public function indexAction()
	{
		Mage::helper('datavalidate/validate')->executeDataValidation();
	}

}