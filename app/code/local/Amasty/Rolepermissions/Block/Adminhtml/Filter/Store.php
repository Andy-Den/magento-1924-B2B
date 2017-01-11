<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Filter_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Store
{
    public function getHtml()
    {
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $websiteCollection = $storeModel->getWebsiteCollection();
        $groupCollection = $storeModel->getGroupCollection();
        $storeCollection = $storeModel->getStoreCollection();

        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($storeRestrictions = $rule->getScopeStoreviews())
        {
            foreach ($storeCollection as $id => $store)
            {
                if (!in_array($id, $storeRestrictions))
                    unset($storeCollection[$id]);
            }
        }



        $allShow = $this->getColumn()->getStoreAll();

        $html  = '<select name="' . $this->escapeHtml($this->_getHtmlName()) . '" '
            . $this->getColumn()->getValidateClass() . '>';
        $value = $this->getColumn()->getValue();
        if ($allShow) {
            $html .= '<option value="0"' . ($value == 0 ? ' selected="selected"' : '') . '>'
                . Mage::helper('adminhtml')->__('All Store Views') . '</option>';
        } else {
            $html .= '<option value=""' . (!$value ? ' selected="selected"' : '') . '></option>';
        }
        foreach ($websiteCollection as $website) {
            //$websiteShow = false;
            foreach ($groupCollection as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                //$groupShow = false;
                foreach ($storeCollection as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    /*if (!$websiteShow) {
                        $websiteShow = true;
                        $html .= '<optgroup label="' . $this->escapeHtml($website->getName()) . '"></optgroup>';
                    }*/
                    /*if (!$groupShow) {
                        $groupShow = true;
                        $html .= '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;'
                            . $this->escapeHtml($group->getName()) . '">';
                    }*/
                    $value = $this->getValue();
                    $selected = $value == $store->getId() ? ' selected="selected"' : '';
                    $html .= '<option value="' . $store->getId() . '"' . $selected . '>&nbsp;&nbsp;&nbsp;&nbsp;'
                        . $this->escapeHtml($store->getName()) . '</option>';
                }
               /* if ($groupShow) {
                    $html .= '</optgroup>';
                }*/
            }
        }
        if (!$storeRestrictions)
        {
            if ($this->getColumn()->getDisplayDeleted()) {
                $selected = ($this->getValue() == '_deleted_') ? ' selected' : '';
                $html.= '<option value="_deleted_"'.$selected.'>'.$this->__('[ deleted ]').'</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
}
