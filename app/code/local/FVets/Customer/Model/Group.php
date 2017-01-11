<?php
class FVets_Customer_Model_Group extends Mage_Customer_Model_Group
{

	public function addWebsiteFilter($website = false, $withAdmin = true)
	{
		if ($website)
		{
			if ($website instanceof Mage_Core_Model_Website) {
				$website = $website->getId();
			}

			if ($withAdmin)
				$website .= ',0';

			$this->getSelect()->where(
				"main_table.website_id IN({$website})"
			);

		}
		else
		{
			$rule = Mage::helper('amrolepermissions')->currentRule();

			if ($rule->getScopeStoreviews()) {
				$accessible = $rule->getPartiallyAccessibleWebsites();
				if ($withAdmin)
					$accessible[] = 0;

				$this->getSelect()->where(
					"main_table.website_id IN(" . implode(',', $accessible) . ")"
				);
			}
		}

		return $this;
	}

}