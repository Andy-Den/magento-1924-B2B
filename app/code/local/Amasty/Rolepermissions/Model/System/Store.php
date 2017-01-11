<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_System_Store extends Mage_Adminhtml_Model_System_Store
{
    /**
     * Get websites as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getWebsiteOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = parent::getWebsiteOptionHash($withDefault, $attribute);

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            $accessible = $rule->getPartiallyAccessibleWebsites();

            if (isset($options[0]))
                unset($options[0]); // Unset admin store

            foreach ($options as $id => $value)
            {
                if (!in_array($id, $accessible))
                    unset($options[$id]);
            }
        }

        return $options;
    }

	/**
	 * Website label/value array getter, compatible with form dropdown options
	 *
	 * @param bool $empty
	 * @param bool $all
	 * @return array
	 */
	public function getWebsiteValuesForForm($empty = false, $all = false)
	{
		$options = array();
		if ($empty) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
				'value' => ''
			);
		}
		if ($all && $this->_isAdminScopeAllowed) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('Admin'),
				'value' => 0
			);
		}

		$accessible =  Mage::helper('amrolepermissions')->currentRule()->getPartiallyAccessibleWebsites();

		foreach ($this->_websiteCollection as $website) {
			if (in_array($website->getId(), $accessible))
			{
				$options[] = array(
					'label' => $website->getName(),
					'value' => $website->getId(),
				);
			}
		}
		return $options;
	}

	/**
	 * Retrieve store values for form
	 *
	 * @param bool $empty
	 * @param bool $all
	 * @return array
	 */
	public function getStoreValuesForForm($empty = false, $all = false)
	{
		$options = array();
		if ($empty) {
			$options[] = array(
				'label' => '',
				'value' => ''
			);
		}
		if ($all && $this->_isAdminScopeAllowed) {
			$options[] = array(
				'label' => Mage::helper('adminhtml')->__('All Store Views'),
				'value' => 0
			);
		}

		$nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

		$accessible =  Mage::helper('amrolepermissions')->currentRule()->getPartiallyAccessibleWebsites();

		foreach ($this->_websiteCollection as $website) {
			$websiteShow = false;

			if (!in_array($website->getId(), $accessible)) {
				continue;
			}

			foreach ($this->_groupCollection as $group) {
				if ($website->getId() != $group->getWebsiteId()) {
					continue;
				}
				$groupShow = false;
				foreach ($this->_storeCollection as $store) {

					if (!Mage::helper('amrolepermissions')->currentRule()->storeAccessible($store->getId())) {
						continue;
					}

					if ($group->getId() != $store->getGroupId()) {
						continue;
					}
					if (!$websiteShow) {
						$options[] = array(
							'label' => $website->getName(),
							'value' => array()
						);
						$websiteShow = true;
					}
					if (!$groupShow) {
						$groupShow = true;
						$values    = array();
					}
					$values[] = array(
						'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
						'value' => $store->getId()
					);
				}
				if ($groupShow) {
					$options[] = array(
						'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
						'value' => $values
					);
				}
			}
		}
		return $options;
	}
}
