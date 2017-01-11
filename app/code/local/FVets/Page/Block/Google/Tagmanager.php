<?php

class FVets_Page_Block_Google_Tagmanager extends Mage_Core_Block_Template
{
	const XML_PATH_TAGMANAGER_ACTIVE              = 'google/tagmanager/active';
	const XML_PATH_TAGMANAGER_ACCOUNT              = 'google/tagmanager/account';

	function _toHtml()
	{
		if (!(bool)Mage::app()->getStore()->getConfig(self::XML_PATH_TAGMANAGER_ACTIVE))
		{
			return;
		}

		return parent::_toHtml();
	}

	function getTagmanagerAccount()
	{
		return Mage::app()->getStore()->getConfig(self::XML_PATH_TAGMANAGER_ACCOUNT);
	}
}