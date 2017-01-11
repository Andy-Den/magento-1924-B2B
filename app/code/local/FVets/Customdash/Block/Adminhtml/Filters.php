<?php

class FVets_Customdash_Block_Adminhtml_Filters extends Mage_Adminhtml_Block_Template
{
  protected $testUsers = array("%@4vets.com.br%");
  protected $testUsersStr = "%@4vets.com.br%";
  protected $badOrderStatus = 'canceled';

  public function setDateFilter(&$collection)
  {
	$fromDate = $this->getFromDate();
	$toDate = $this->getToDate();

	if (!isset($fromDate))
	{
	  $dayBefore = strtotime(date('Y-m') . '-01 00:00:00');
	  $fromDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime("-11 months", $dayBefore))));
	} else
	{
	  $fromDate = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->getFromDate() . ' 00:00:01')));
	}

	if (!isset($toDate))
	{
	  $toDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59'));
	} else
	{
	  $toDate = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->getToDate() . ' 23:59:59')));
	}

	$fromDate = Mage::helper('fvets_customdash')->convertToUtc($fromDate);
	$toDate = Mage::helper('fvets_customdash')->convertToUtc($toDate);

	//date_default_timezone_set('America/Sao_Paulo');

	$collection->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));
  }

  public function setStatusFilter(&$collection)
  {
	$collection->addFieldtoFilter('main_table.status', array('nin' => $this->badOrderStatus));
  }

  public function setGenericFilter(&$collection)
  {
	foreach ($this->testUsers as $pattern)
	{
	  $collection->addFieldtoFilter('main_table.customer_email', array('nlike' => $pattern));
	}
  }

  public function setCurrentWebsiteFilter(&$collection = null, &$query = null, $pattern = null)
  {
	$_websiteFilter = $this->getWebsiteFilter();
	$storesAllowed = array();
	$stringStoresAllowed = '';
	if (isset($_websiteFilter) && !empty($_websiteFilter))
	{
	  $website = Mage::getModel('core/website')->load($_websiteFilter);

	  if ($website->getConfig('general/store_information/is_brand') == 1)
	  {
		$stores = Mage::getModel('core/store')->getCollection()
		  ->addFieldToFilter('code', array('like' => '%_' . $website->getCode()));

		foreach ($stores as $store)
		{
		  if (!is_null($collection))
		  {
			array_push($storesAllowed, $store->getId());
		  } else
		  {
			$stringStoresAllowed = $stringStoresAllowed . $store->getId() . ', ';
		  }
		}
	  } else
	  {
		foreach ($website->getGroups() as $group)
		{
		  $stores = $group->getStores();
		  foreach ($stores as $store)
		  {
			if (!is_null($collection))
			{
			  array_push($storesAllowed, $store->getId());
			} else
			{
			  $stringStoresAllowed = $stringStoresAllowed . $store->getId() . ', ';
			}
		  }
		}
	  }
	  if (!is_null($collection))
	  {
		$collection->addFieldtoFilter('main_table.store_id', array('in' => $storesAllowed));
	  } else
	  {
		$query = str_replace($pattern, $pattern . ' and `sfo`.`store_id` in (' . rtrim($stringStoresAllowed, ', ') . ')', $query);
	  }
	} else
	{
	  if (isset($_websiteFilter) && $_websiteFilter == 0)
	  {
		$allStores = Mage::getModel('core/store')->getCollection()
		  ->toOptionArray();

		if (!is_null($collection))
		{
		  $collection->addFieldtoFilter('main_table.store_id', array('in' => $allStores));
		} else
		{
		  $stringStoresAllowed = '';
		  foreach ($allStores as $store)
		  {
			$stringStoresAllowed = $stringStoresAllowed . ($store['value'] . ',');
		  }
		  $stringStoresAllowed = rtrim($stringStoresAllowed, ',');
		  $query = str_replace($pattern, $pattern . ' and `sfo`.`store_id` in (' . $stringStoresAllowed . ')', $query);
		}
	  }
	}
  }

  protected function setCurrentStoreviewFilter(&$collection)
  {
	$storeviewId = Mage::app()->getRequest()->getParam('storeview_switcher');

	if (!$storeviewId)
	{
	  return;
	}

	$collection->addFieldToFilter('main_table.store_id', $storeviewId);
  }

  public function setTipoClienteFilter(&$collection)
  {
	$_tipoCliente = $this->getTipoCliente();
	if (isset($_tipoCliente))
	{
	  if ($_tipoCliente == 1)
	  {
		$_tipoCliente = 'ERP';
	  } else
	  {
		$_tipoCliente = 'SITE';
	  }
	  $collection->getSelect()->join('customer_entity_varchar', 'main_table.customer_id = customer_entity_varchar.entity_id and customer_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'origem\') and customer_entity_varchar.value = \'' . $_tipoCliente . '\'', array());
	  //echo $collection->getSelect()->__toString();
	}
  }

  public function setStatusReportFilter(&$collection)
  {
	$collection->addFieldtoFilter('status', array('in' => array('pending', 'complete')));
  }
}