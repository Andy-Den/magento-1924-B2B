<?php
class FVets_CustomReports_Block_Report_Comissions_Filter_Abstract extends Mage_Adminhtml_Block_Widget_Grid {

	public function setDateFilter(&$collection)
	{
		$fromDate = $this->getFromDate();
		$toDate = $this->getToDate();

		if (!isset($fromDate) || !isset($toDate)) {
			return;
		}

		$fromDate = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->getFromDate() . ' 00:00:01')));
		$toDate = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->getToDate() . ' 23:59:59')));

		$collection->addFieldtoFilter('main_table.created_at', array('from' => $fromDate, 'to' => $toDate));
	}

	public function setStatusFilter(&$collection)
	{
		$collection->addFieldtoFilter('main_table.status', array('neq' => 'canceled'));
	}

	public function setGenericFilter(&$collection)
	{
		$ignoreUserPatterns = array("%@4vets.com.br");
		$collection->addFieldtoFilter('main_table.customer_email', array('nlike' => $ignoreUserPatterns));
	}

	public function setCurrentUserStoreFilter(&$collection)
	{
		$storePermission = $this->getStorePermission();

		if (!isset($storePermission)) {
			return;
		}
		$collection->addFieldtoFilter('main_table.store_id', array('in' => $storePermission));
	}

	public function setStoreReportFilter(&$collection)
	{
		$_storeFilter = $this->getStoreFilter();
		if (isset($_storeFilter) && !empty($_storeFilter)) {
			$collection->addFieldtoFilter('store_id', array('in' => $_storeFilter));
		}
	}

	public function setWebsiteReportFilter(&$collection)
	{
		$_websiteFilter = $this->getWebsiteFilter();
		$storesAllowed = array();
		if (isset($_websiteFilter) && !empty($_websiteFilter)) {
			$website = Mage::getModel('core/website')->load($_websiteFilter);
			foreach ($website->getGroups() as $group) {
				$stores = $group->getStores();
				foreach ($stores as $store) {
					array_push($storesAllowed, $store->getId());
				}
			}
			$collection->addFieldtoFilter('store_id', array('in' => $storesAllowed));
		}
	}

	public function setStatusReportFilter(&$collection) {
		$collection->addFieldtoFilter('status', array('neq' => array(Mage_Sales_Model_Order::STATE_CANCELED)));
	}
}

