<?php
class Amasty_Rolepermissions_Block_Adminhtml_Product_Edit_Tab_Websites extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites
{

	/**
	 * Get HTML of store chooser
	 *
	 * @param Mage_Core_Model_Store $storeTo
	 * @return string
	 */
	public function getChooseFromStoreHtml($storeTo)
	{
		if (!$this->_storeFromHtml) {
			$this->_storeFromHtml = '<select name="copy_to_stores[__store_identifier__]" disabled="disabled">';
			$this->_storeFromHtml.= '<option value="0">'.Mage::helper('catalog')->__('Default Values').'</option>';
			foreach ($this->getWebsiteCollection() as $_website) {
				if (!$this->hasWebsite($_website->getId())) {
					continue;
				}

				$allowed = Mage::helper('amrolepermissions')->currentRule()->getScopeWebsites();
				if (!is_array($allowed) || !in_array($_website->getId(), $allowed)) {
					continue;
				}

				$optGroupLabel = $this->escapeHtml($_website->getName());
				$this->_storeFromHtml .= '<optgroup label="' . $optGroupLabel . '"></optgroup>';
				foreach ($this->getGroupCollection($_website) as $_group) {
					$optGroupName = $this->escapeHtml($_group->getName());
					$this->_storeFromHtml .= '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $optGroupName . '">';
					foreach ($this->getStoreCollection($_group) as $_store) {
						$this->_storeFromHtml .= '<option value="' . $_store->getId() . '">&nbsp;&nbsp;&nbsp;&nbsp;';
						$this->_storeFromHtml .= $this->escapeHtml($_store->getName()) . '</option>';
					}
				}
				$this->_storeFromHtml .= '</optgroup>';
			}
			$this->_storeFromHtml .= '</select>';
		}
		return str_replace('__store_identifier__', $storeTo->getId(), $this->_storeFromHtml);
	}

}
?>