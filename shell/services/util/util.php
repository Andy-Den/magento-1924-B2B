<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 6/10/16
 * Time: 1:39 PM
 */

function getStoreidsByWebsiteId($websiteId)
{
	$collection = Mage::getModel('core/store')->getCollection()
		->addFieldToFilter('website_id', $websiteId);
	return $collection->toOptionArray();
}