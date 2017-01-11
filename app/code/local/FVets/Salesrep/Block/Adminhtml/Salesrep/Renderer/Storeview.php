<?php

class FVets_Salesrep_Block_Adminhtml_Salesrep_Renderer_Storeview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{

	/**
	 * Render row store views
	 *
	 * @param Varien_Object $row
	 * @return string
	 */
	public function render(Varien_Object $row)
	{
		$out = '';
		$skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
		$skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
		$origStores = $row->getData($this->getColumn()->getIndex());

		if (strstr($origStores, ','))
			$origStores = explode(',',$origStores);

		if (is_null($origStores) && $row->getStoreName()) {
			$scopes = array();
			foreach (explode("\n", $row->getStoreName()) as $k => $label) {
				$scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
			}
			$out .= implode('<br/>', $scopes) . $this->__(' [deleted]');
			return $out;
		}

		if (empty($origStores) && !$skipEmptyStoresLabel) {
			return '';
		}
		if (!is_array($origStores)) {
			$origStores = array($origStores);
		}

		if (empty($origStores)) {
			return '';
		}
		elseif (in_array(0, $origStores) && count($origStores) == 1 && !$skipAllStoresLabel) {
			return Mage::helper('adminhtml')->__('All Store Views');
		}

		$data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

		foreach ($data as $website) {
			$out .= $website['label'] . '<br/>';
			foreach ($website['children'] as $group) {
				$out .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
				foreach ($group['children'] as $store) {
					$out .= str_repeat('&nbsp;', 6) . $store['label'] . '<br/>';
				}
			}
		}

		return $out;
	}

}
