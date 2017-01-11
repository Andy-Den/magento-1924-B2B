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
 * Warehouse plus data helper
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Helper_Index_Process 
    extends Innoexts_Warehouse_Helper_Index_Process 
{
    /**
     * Get product flat process 
     * 
     * @return Mage_Index_Model_Process
     */
    protected function getProductFlat()
    {
        return Mage::getSingleton('index/indexer')->getProcessByCode('catalog_product_flat');
    }
    /**
     * Get search process 
     * 
     * @return Mage_Index_Model_Process
     */
    protected function getSearch()
    {
        return Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext');
    }
    /**
     * Reindex product flat
     * 
     * @return Innoexts_WarehousePlus_Helper_Index_Process
     */
    public function reindexProductFlat()
    {
        $process = $this->getProductFlat();
        if ($process) {
            $process->reindexAll();
        }
        return $this;
    }
    /**
     * Reindex search
     * 
     * @return Innoexts_WarehousePlus_Helper_Index_Process
     */
    public function reindexSearchFlat()
    {
        $process = $this->getSearch();
        if ($process) {
            $process->reindexAll();
        }
        return $this;
    }
    /**
     * Change product flat process status
     * 
     * @param int $status
     * 
     * @return Innoexts_WarehousePlus_Helper_Index_Process
     */
    public function changeProductFlatStatus($status)
    {
        $process = $this->getProductFlat();
        if ($process) {
            $process->changeStatus($status);
        }
        return $this;
    }
    /**
     * Change search process status
     * 
     * @param int $status
     * 
     * @return Innoexts_WarehousePlus_Helper_Index_Process
     */
    public function changeSearchStatus($status)
    {
        $process = $this->getSearchProcess();
        if ($process) {
            $process->changeStatus($status);
        }
        return $this;
    }
}