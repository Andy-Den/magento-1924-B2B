<?php
class FVets_Core_Helper_Data extends Mage_Core_Helper_Data
{

	const XML_PATH_STORE_IS_BRAND       = 'general/store_information/is_brand';
	const XML_PATH_STORE_BASE_URL       = 'web/unsecure/base_url';
	const PATH_STORE_ALIAS				= 'general/store_information/alias';
	const XML_PATH_IGNORE_WILDCARD        = 'sales_email/order/ignore_wildcard';

	public $isUserAdmin;

	//Get storeviews that end with brand code
	function getBrandStoreviews($code, $include_brand = false)
	{
		$return = array();

		foreach (Mage::app()->getWebsites() as $website) {
			foreach ($website->getGroups() as $group) {
				$stores = $group->getStores();
				foreach ($stores as $store)
				{
					if ($store->getIsActive())
					{
						$brand_code = explode('_', $store->getCode());
						$brand_code = end($brand_code);
						if ($brand_code == $code)
						{
							if ((!$include_brand && $store->getCode() != $code) || $include_brand)
								$return[] = $store;

						}
					}
				}
			}
		}

		return $return;
	}

	function getStoreAlias($store)
	{
		if (is_integer($store))
		{
			$store = Mage::getModel('core/store')->load($store);
		}

		$alias = $store->getConfig(self::PATH_STORE_ALIAS);

		if (trim($alias) == '')
		{
			$alias = $store->getName();
		}

		return $alias;

	}

	function canSentEmailFromCustomer($email_pedido, $email_envio)
	{
		$wildcard = explode(',', Mage::getStoreConfig(self::XML_PATH_IGNORE_WILDCARD, $this->getStoreId()));

		$email_pedido = explode('@', $email_pedido);
		$email_pedido = end($email_pedido);
		//Verifica se o email do cliente que fez o pedido é de teste ou não.
		//Se for de testes, continua, pois os outros não podem receber este email.
		if (in_array($email_pedido, $wildcard))
		{
			if (!is_array($email_envio))
			{
				$email_envio = array($email_envio);
			}
			foreach ($email_envio as  $key => $value)
			{
				$value = explode('@', $value);
				$value = end($value);
				//Verifica se os emails para quem está sendo enviado o pedido e cópias, é teste ou não.
				//Se não for de testes, continua, pois os outros não podem receber este email.
				if (!in_array($value, $wildcard))
				{
					unset($email_envio[$key]);
				}
			}
		}

		if (isset($email_envio) && (!is_array($email_envio) || (is_array($email_envio) && count($email_envio) > 0)))
		{
			return $email_envio;
		}

		Mage::logException(new Exception('This letter cannot be sent from the wildcard:'.implode(',', $wildcard))); // translation is intentionally omitted
		return false;
	}

	function isLoggedUserAdmin()
	{
		if (!$this->isUserAdmin) {
			$id = Mage::getSingleton('admin/session')->getUser()->getId();
			$roleData = Mage::getModel('admin/user')->load($id)->getRole()->getData();
			$this->isUserAdmin = $roleData['role_name'] == 'Adminfistrators';
		}

		return $this->isUserAdmin;
	}
}