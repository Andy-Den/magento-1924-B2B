<?php

include_once 'Nusoap/nusoap.php';

class FVets_Allin_Helper_Data extends Mage_Core_Helper_Data
{
	private $url = 'http://painel02.allinmail.com.br/wsAllin';
	private $ticket = null;

	private $listarListasUrl = 'http://painel01.allinmail.com.br/wsAllin/listar_listas.php?wsdl';
	private $inserirEmailBaseUrl = 'http://painel01.allinmail.com.br/wsAllin/inserir_email_base.php?wsdl';

	public function getUrl()
	{
		return $this->url;
	}

	public function getTicket($account)
	{
		if ($this->ticket != null)
		{
			return $this->ticket;
		} else
		{
			$client = new nusoap_client("$this->url/login.php?wsdl", true);
			$ticket = $client->call('getTicket', array($account->getUser(), $account->getPassword()));
			return $ticket;
		}
	}

	public function getWebsites()
	{
		$websites = Mage::app()->getWebsites();
		$result = array();
		foreach ($websites as $website)
		{
			$result[$website->getId()] = $website->getName();
		}
		return $result;
	}

	public function getListarListasUrl()
	{
		return $this->listarListasUrl;
	}

	public function getInserirEmailBaseUrl()
	{
		return $this->inserirEmailBaseUrl;
	}

	public function getCustomerList($accountId)
	{
		$account = Mage::getModel('fvets_allin/account')->load($accountId);

		$synchronizeCollection = Mage::getModel('fvets_allin/synchronize')
			->getResourceCollection();
		$synchronizeCollection
			->getSelect()
			->where('id_account = ' . $account->getId())
			->order('data', 'desc')
			->limit(1);

		$lastSynchronize = null;

		if ($synchronizeCollection)
		{
			foreach ($synchronizeCollection as $synchronize)
			{
				$lastSynchronize = $synchronize;
				break;
			}
		}

		$collection = Mage::getModel('customer/customer')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('mp_cc_is_approved', MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED)
			->addAttributeToFilter('website_id', $account->getWebsiteId());

		if ($lastSynchronize)
		{
			$collection->addAttributeToFilter('updated_at', array('gt' => $lastSynchronize->getData('data')));
		}

		$collection
			->getSelect()
			//website name
			->joinleft(
				array('cw' => 'core_website'),
				'cw.website_id = e.website_id',
				array('cw.name as website_name')
			)
			//representante
			->joinleft(
				array('cev3' => 'customer_entity_varchar'),
				'cev3.entity_id = e.entity_id and cev3.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'fvets_salesrep\' and entity_type_id = 1)',
				array()
			)
			->joinleft(
				array('fs' => 'fvets_salesrep'),
				'fs.id = cev3.value',
				array('fs.name as representante')
			)
			//cidade
			->joinleft(
				array('cae' => 'customer_address_entity'),
				'cae.parent_id = e.entity_id',
				array()
			)
			->joinleft(
				array('caev' => 'customer_address_entity_varchar'),
				'caev.entity_id = cae.entity_id and caev.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'city\' and entity_type_id = 2)',
				array('caev.value as cidade')
			)
			//estado
			->joinleft(
				array('caev2' => 'customer_address_entity_varchar'),
				'caev2.entity_id = cae.entity_id and caev2.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'region\' and entity_type_id = 2)',
				array('caev2.value as estado')
			);
		//->order('updated_at desc');

		//echo $collection->getSelect()->__toString();

		return $collection;
	}

	public function getAllCustomersList($accountId, $filters = null)
	{
		$account = Mage::getModel('fvets_allin/account')->load($accountId);

		if (!$filters) {
			$collection = Mage::getModel('customer/customer')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('mp_cc_is_approved', MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED)
				->addAttributeToFilter('website_id', $account->getWebsiteId());

			$collection
				->getSelect()
				//website name
				->joinleft(
					array('cw' => 'core_website'),
					'cw.website_id = e.website_id',
					array('cw.name as website_name')
				)
				//representante
				->joinleft(
					array('cev3' => 'customer_entity_varchar'),
					'cev3.entity_id = e.entity_id and cev3.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'fvets_salesrep\' and entity_type_id = 1)',
					array()
				)
				->joinleft(
					array('fs' => 'fvets_salesrep'),
					'fs.id = cev3.value',
					array('fs.name as representante')
				)
				//cidade
				->joinleft(
					array('cae' => 'customer_address_entity'),
					'cae.parent_id = e.entity_id',
					array()
				)
				->joinleft(
					array('caev' => 'customer_address_entity_varchar'),
					'caev.value_id = (select caev11.value_id from customer_address_entity_varchar caev11 where caev11.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'city\' and entity_type_id = 2) and caev11.entity_id = e.entity_id limit 1)',
					array('caev.value as cidade')
				)
				//estado
				->joinleft(
					array('caev2' => 'customer_address_entity_varchar'),
					'caev2.value_id = (select caev11.value_id from customer_address_entity_varchar caev11 where caev11.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'region\' and entity_type_id = 2) and caev11.entity_id = e.entity_id limit 1)',
					array('caev2.value as estado')
				);
			//->order('updated_at desc');

			$collection->getSelect()->distinct(true);
		} else {
			$collection = Mage::getModel('customer/customer')
				->getCollection();
			$collection->addAttributeToSelect('*');
			foreach ($filters as $key => $filter) {
				$collection->addAttributeToFilter($key, $filter);
			}
		}
		//echo $collection->getSelect()->__toString();
		return $collection;
	}

	public function jaRealizouLogin($customer)
	{
		$event = Mage::getModel('log/customer')->loadByCustomer($customer);
		$jaRealizouLogin = false;
		if ($event && $event->getLogId())
		{
			$jaRealizouLogin = true;
		}
		return $jaRealizouLogin;
	}

	public function log($data, $filename = 'FVets_Allin.log')
	{
		return Mage::getModel('core/log_adapter', $filename)->log($data);
	}

	public function saveTrash($email, $websiteId, $status = 0)
	{
		$trash = Mage::getModel('fvets_allin/trash');
		$idAccount = $this->getAccountIdByWebsiteId($websiteId);
		$trash->setIdAccount($idAccount);
		$trash->setEmail($email);
		$trash->setStatus($status);
		$trash->save();
	}

	private function getAccountIdByWebsiteId($websiteId)
	{
		$account = Mage::getModel('fvets_allin/account')->getCollection()
			->addFieldToFilter('website_id', $websiteId)
			->getFirstItem();
		if ($account)
		{
			return $account->getId();
		} else
		{
			return null;
		}
	}

	private function getDataLogPathFilename($websiteId)
	{
		$websiteCode = Mage::getModel('core/website')->load($websiteId)->getCode();

		$filename = date("Y-m-d H:i:s") . '_exported_data' . '.csv';
		//$filePath = Mage::getBaseDir() . DS . 'var' . DS . 'export' . DS . 'fvets' . DS . 'allin' . DS . $filename;
		$filePath = DS . 'wwwroot' . DS . 'whitelabel' . DS . 'integracao' . DS . $websiteCode . DS . 'report' . DS . 'allin' . DS . $filename;
		return $filePath;
	}

	public function saveDataLog($websiteId, $data)
	{
		try
		{
			file_put_contents($this->getDataLogPathFilename($websiteId), $data);
		} catch (Exception $ex)
		{
			$this->log($ex->__toString());
		}
	}

	public function getAttributeLabelByCodeAndValue($code, $value)
	{
		$attribute = Mage::getResourceModel('eav/entity_attribute_collection')
			->setCodeFilter($code)
			->getFirstItem();
		if ($attribute->usesSource()) {
			$options = $attribute->getSource()->getAllOptions(false);
			foreach ($options as $option) {
				if ($option['value'] == $value) {
					return $option['label'];
				}
			}
		}
		return null;
	}

	function getArrayAllowedCategories($websiteId)
	{
		$website = Mage::getModel('core/website')->load($websiteId);
		$stores = $website->getStoreCollection()
			->addFieldToFilter('is_active', 1);

		$group = $website->load($websiteId)->getGroupCollection()->getFirstItem();
		$rootCategoryId = $group->getRootCategoryId();
		$categories = Mage::getModel('catalog/category')->getCollection()
			->addFieldToFilter('parent_id', $rootCategoryId);

		$categoriesArray = array();

		foreach ($categories as $category)
		{
			foreach ($stores as $store)
			{
				$category->setStoreId($store->getId())->load();
				if ($category->getIsActive())
				{
					$categoriesArray[$category->getId()] = $category;
				}
			}
		}
		return $categoriesArray;
	}

	function formatKey($str)
	{
		$str = Mage::helper('catalog/product_url')->format($str);
		$urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		return $urlKey;
	}

	function clearString($str) {
		$str = preg_replace('/[áàãâä]/ui', 'a', $str);
		$str = preg_replace('/[éèêë]/ui', 'e', $str);
		$str = preg_replace('/[íìîï]/ui', 'i', $str);
		$str = preg_replace('/[óòõôö]/ui', 'o', $str);
		$str = preg_replace('/[úùûü]/ui', 'u', $str);
		$str = preg_replace('/[ç]/ui', 'c', $str);
		// $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
		$str = preg_replace('/[^a-z0-9]/i', '_', $str);
		$str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
		return strtoupper($str);
	}
}
