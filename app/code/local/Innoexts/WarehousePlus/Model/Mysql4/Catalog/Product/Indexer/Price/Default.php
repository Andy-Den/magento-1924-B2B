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
 * Default product type price indexer resource
 * 
 * @category   Innoexts
 * @package    Innoexts_WarehousePlus
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehousePlus_Model_Mysql4_Catalog_Product_Indexer_Price_Default 
    extends Innoexts_Warehouse_Model_Mysql4_Catalog_Product_Indexer_Price_Default 
{
    /**
     * Prepare products default final price in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * 
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Default
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $helper             = $this->getWarehouseHelper();
        $indexerHelper      = $this->getProductPriceIndexerHelper();
        $isMultipleMode     = $helper->isMultipleMode();
        $write              = $this->_getWriteAdapter();
        $this->_prepareDefaultFinalPriceTable();
        $select             = $indexerHelper->getFinalPriceSelect($write);
        $select->where('e.type_id=?', $this->getTypeId());
        
        $statusCond     = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        
        if ($this->getVersionHelper()->isGe1600()) {
            if (Mage::helper('core')->isModuleEnabled('Mage_Tax')) {
                $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
            } else {
                $taxClassId = new Zend_Db_Expr('0');
            }
        } else {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        }
        $select->columns(array('tax_class_id' => $taxClassId));
       
        $indexerHelper->addStockJoin($select);
        $indexerHelper->addCurrencyRateJoin($select);
        $indexerHelper->addTierPriceJoin($select, 'tp', $this->getTable('catalog/product_index_tier_price'));
        
        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $indexerHelper->addBatchPriceJoin($select, 'bp', $indexerHelper->getBatchPriceIndexTable());
        $price          = new Zend_Db_Expr("IF (bp.price IS NOT NULL, bp.price, {$price})");
        if ($isMultipleMode) {
            $minPrice       = new Zend_Db_Expr("IF (bp.min_price IS NOT NULL, bp.min_price, {$price})");
            $maxPrice       = new Zend_Db_Expr("IF (bp.max_price IS NOT NULL, bp.max_price, {$price})");
        }
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        
        $indexerHelper->addBatchPriceJoin($select, 'bsp', $indexerHelper->getBatchSpecialPriceIndexTable());
        $specialPrice   = new Zend_Db_Expr("IF (bsp.price IS NOT NULL, bsp.price, {$price})");
        if ($isMultipleMode) {
            $specialMinPrice    = new Zend_Db_Expr("IF (bsp.min_price IS NOT NULL, bsp.min_price, {$price})");
            $specialMaxPrice    = new Zend_Db_Expr("IF (bsp.max_price IS NOT NULL, bsp.max_price, {$price})");
        }
        $finalPrice     = $indexerHelper->getFinalPriceExpr($write, $price, $specialPrice, $specialFrom, $specialTo);
        if ($isMultipleMode) {
            $finalMinPrice       = $indexerHelper->getFinalPriceExpr($write, $minPrice, $specialMinPrice, $specialFrom, $specialTo);
            $finalMaxPrice       = $indexerHelper->getFinalPriceExpr($write, $maxPrice, $specialMaxPrice, $specialFrom, $specialTo);
        } else {
            $finalMinPrice       = $finalPrice;
            $finalMaxPrice       = $finalPrice;
        }
        $select->columns(array(
            'orig_price'    => $price, 
            'price'         => $finalPrice, 
            'min_price'     => $finalMinPrice, 
            'max_price'     => $finalMaxPrice, 
            'tier_price'    => new Zend_Db_Expr('tp.min_price'), 
            'base_tier'     => new Zend_Db_Expr('tp.min_price'), 
        ));
        if ($this->getVersionHelper()->isGe1700()) {
            $select->columns(array(
                'group_price'      => new Zend_Db_Expr('gp.price'), 
                'base_group_price' => new Zend_Db_Expr('gp.price'), 
            ));
        }
        $select->columns(array(
            'stock_id'      => new Zend_Db_Expr('cis.stock_id'), 
            'currency'      => $indexerHelper->getCurrencyExpr('cw.website_id'), 
            'store_id'      => new Zend_Db_Expr('cs.store_id'), 
        ));
        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }
        $eventData = array(
            'select'            => $select, 
            'entity_field'      => new Zend_Db_Expr('e.entity_id'), 
            'website_field'     => new Zend_Db_Expr('cw.website_id'), 
            'stock_field'       => new Zend_Db_Expr('cis.stock_id'), 
            'currency_field'    => $indexerHelper->getCurrencyExpr('cw.website_id'), 
            'store_field'       => new Zend_Db_Expr('cs.store_id'), 
        );
        Mage::dispatchEvent('prepare_catalog_product_index_select', $eventData);


		//Remove unused stock ids FROM websites
		$select->joinInner(
			array('wstore' =>  'warehouse_store'),
			'wstore.store_id = cs.store_id',
			array()
		)
			->joinInner(
				array('w' =>  'warehouse'),
				'w.warehouse_id = wstore.warehouse_id AND w.stock_id = cis.stock_id',
				array()
			)
		;

		//Remove unused stock ids GROM customer groups
		$select->where('(`cg`.website_id = `cw`.website_id OR `cg`.website_id = 0)');


        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);
        $select = $write->select()->join(array('wd' => $this->_getWebsiteDateTable()), 'i.website_id = wd.website_id', array());
        
        $parameters = array(
            'index_table'       => array('i' => $this->_getDefaultFinalPriceTable()), 
            'select'            => $select, 
            'entity_id'         => 'i.entity_id', 
            'customer_group_id' => 'i.customer_group_id', 
            'website_id'        => 'i.website_id', 
            'stock_id'          => 'i.stock_id', 
            'currency'          => 'i.currency', 
            'store_id'          => 'i.store_id', 
            'update_fields'     => array('price', 'min_price', 'max_price'), 
        );
        if ($this->getVersionHelper()->isGe1600()) {
            $parameters['website_date'] = 'wd.website_date';
        } else {
            $parameters['website_date'] = 'wd.date';
        }
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', $parameters);
        return $this;
    }
}