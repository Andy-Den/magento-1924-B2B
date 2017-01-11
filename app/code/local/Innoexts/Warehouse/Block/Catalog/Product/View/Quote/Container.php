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
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product quote container block
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Catalog_Product_View_Quote_Container 
    extends Mage_Core_Block_Template 
{
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('warehouse/catalog/product/view/quote/container.phtml');
    }
    /**
     * Prepare layout
     * 
     * @return self
     */
    protected function _prepareLayout()
    {
        $this->setChild('quote', $this->getLayout()->createBlock('warehouse/catalog_product_view_quote'));
        parent::_prepareLayout();
        return $this;
    }
}