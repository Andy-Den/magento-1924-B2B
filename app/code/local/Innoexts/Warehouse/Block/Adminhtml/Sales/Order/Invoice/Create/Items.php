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
 * Invoice items
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Sales_Order_Invoice_Create_Items 
    extends Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items 
{
    /**
     * Get warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Whether to show 'Return to stock' checkbox for item
     * @param Mage_Sales_Model_Order_Creditmemo_Item $item
     * 
     * @return bool
     */
    public function canReturnItemToStock($item=null)
    {
        $canReturnToStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT);
        if (!is_null($item)) {
            if (!$item->hasCanReturnToStock()) {
                $product = $item->getOrderItem()->getProduct();
                if ($product->getId() && $product->getStockItem()->getManageStock()) $item->setCanReturnToStock(true);
                else $item->setCanReturnToStock(false);
            }
            $canReturnToStock = $item->getCanReturnToStock();
        }
        return $canReturnToStock;
    }
}