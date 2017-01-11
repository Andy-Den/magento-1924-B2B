<?php

class FVets_Sales_Block_Reorder_Sidebar extends Mage_Sales_Block_Reorder_Sidebar
{
	/**
	 * Get list of last ordered products
	 * Without "brindes"
	 *
	 * @return array
	 */
	public function getItems()
	{
		$items = array();
		$order = $this->getLastOrder();
		$limit = 5;

		if ($order) {
			$website = Mage::app()->getStore()->getWebsiteId();
			foreach ($order->getParentItemsRandomCollection($limit) as $item) {
				if ($item->getProduct() && in_array($website, $item->getProduct()->getWebsiteIds()) && $item->getRowTotal() > 0) {
					$items[] = $item;
				}
			}
		}

		return $items;
	}
}