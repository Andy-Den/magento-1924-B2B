<?php

class FVets_Customdash_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function convertToUtc($date, $format = 'Y-m-d H:i:s')
	{
		$clientzone = Mage::getStoreConfig('general/locale/timezone');
		$dateObj = new DateTime($date, new DateTimeZone($clientzone));
		$dateObj->setTimezone(new DateTimeZone('UTC'));
		return $dateObj->format($format);
	}

}
