<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_WarehousePlus
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Price scope backend
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Adminhtml_System_Config_Backend_Price_Scope 
    extends Mage_Adminhtml_Model_System_Config_Backend_Price_Scope 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_WarehousePlus_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Callback function which called after transaction commit in resource model
     * 
     * @return Innoexts_WarehousePlus_Model_Adminhtml_System_Config_Backend_Price_Scope
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        if ($this->isValueChanged()) {
            $helper         = $this->getWarehouseHelper();
            $processHelper  = $helper->getProcessHelper();
            $helper->getProductPriceHelper()->changeScope($this->getValue());
            $processHelper->reindexProductPrice();
            $processHelper->reindexProductFlat();
            $processHelper->reindexSearchFlat();
        }
        return $this;
    }
}