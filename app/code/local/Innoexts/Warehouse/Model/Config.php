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
 * Warehouse config
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Config 
    extends Varien_Object 
{
    /**
     * Config path constants
     */
    /**
     * Options
     */
    const XML_PATH_OPTIONS_MODE                             = 'warehouse/options/mode';
    const XML_PATH_OPTIONS_DISPLAY_INFORMATION              = 'warehouse/options/display_information';
    const XML_PATH_OPTIONS_SORT_BY                          = 'warehouse/options/sort_by';
    const XML_PATH_OPTIONS_DISPLAY_ORIGIN                   = 'warehouse/options/display_origin';
    const XML_PATH_OPTIONS_DISPLAY_DISTANCE                 = 'warehouse/options/display_distance';
    const XML_PATH_OPTIONS_DISTANCE_UNIT                    = 'warehouse/options/distance_unit';
    const XML_PATH_OPTIONS_DISPLAY_DESCRIPTION              = 'warehouse/options/display_description';
    const XML_PATH_OPTIONS_SINGLE_ASSIGNMENT_METHOD         = 'warehouse/options/single_assignment_method';
    const XML_PATH_OPTIONS_MULTIPLE_ASSIGNMENT_METHOD       = 'warehouse/options/multiple_assignment_method';
    const XML_PATH_OPTIONS_SPLIT_ORDER                      = 'warehouse/options/split_order';
    const XML_PATH_OPTIONS_SPLIT_QTY                        = 'warehouse/options/split_qty';
    const XML_PATH_OPTIONS_FORCE_CART_NO_BACKORDERS         = 'warehouse/options/force_cart_no_backorders';
    const XML_PATH_OPTIONS_FORCE_CART_ITEM_NO_BACKORDERS    = 'warehouse/options/force_cart_item_no_backorders';
    const XML_PATH_OPTIONS_ALLOW_ADJUSTMENT                 = 'warehouse/options/allow_adjustment';
    /**
     * Catalog
     */
    const XML_PATH_CATALOG_DISPLAY_INFORMATION              = 'warehouse/catalog/display_information';
    const XML_PATH_CATALOG_DISPLAY_OUT_OF_STOCK             = 'warehouse/catalog/display_out_of_stock';
    const XML_PATH_CATALOG_DISPLAY_ORIGIN                   = 'warehouse/catalog/display_origin';
    const XML_PATH_CATALOG_DISPLAY_DISTANCE                 = 'warehouse/catalog/display_distance';
    const XML_PATH_CATALOG_DISPLAY_DESCRIPTION              = 'warehouse/catalog/display_description';
    const XML_PATH_CATALOG_DISPLAY_AVAILABILITY             = 'warehouse/catalog/display_availability';
    const XML_PATH_CATALOG_DISPLAY_QTY                      = 'warehouse/catalog/display_qty';
    const XML_PATH_CATALOG_DISPLAY_TAX                      = 'warehouse/catalog/display_tax';
    const XML_PATH_CATALOG_DISPLAY_SHIPPING                 = 'warehouse/catalog/display_shipping';
    
    const XML_PATH_CATALOG_DISPLAY_BACKEND_MANAGE_STOCK     = 'warehouse/catalog/display_backend_manage_stock';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_MIN_QTY          = 'warehouse/catalog/display_backend_min_qty';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_MIN_SALE_QTY     = 'warehouse/catalog/display_backend_min_sale_qty';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_MAX_SALE_QTY     = 'warehouse/catalog/display_backend_max_sale_qty';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_IS_QTY_DECIMAL   = 'warehouse/catalog/display_backend_is_qty_decimal';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_BACKORDERS       = 'warehouse/catalog/display_backend_backorders';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_NOTIFY_STOCK_QTY = 'warehouse/catalog/display_backend_notify_stock_qty';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_ENABLE_QTY_INCREMENTS = 'warehouse/catalog/display_backend_enable_qty_increments';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_QTY_INCREMENTS   = 'warehouse/catalog/display_backend_qty_increments';
    
    const XML_PATH_CATALOG_DISPLAY_BACKEND_GRID_QTY         = 'warehouse/catalog/display_backend_grid_qty';
    const XML_PATH_CATALOG_DISPLAY_BACKEND_GRID_BATCH_PRICES = 'warehouse/catalog/display_backend_grid_batch_prices';
    const XML_PATH_CATALOG_ENABLE_SHELVES                   = 'warehouse/catalog/enable_shelves';
    /**
     * Shipping
     */
    const XML_PATH_SHIPPING_ENABLE_CARRIER_FILTER           = 'warehouse/shipping/enable_carrier_filter';
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
     * Get mode
     * 
     * @return string
     */
    public function getMode()
    {
        return Mage::getStoreConfig(self::XML_PATH_OPTIONS_MODE);
    }
    /**
     * Set mode
     * 
     * @param string $mode
     * 
     * @return Innoexts_Warehouse_Model_Config
     */
    public function setMode($mode)
    {
        Mage::app()->getStore()->setConfig(self::XML_PATH_OPTIONS_MODE, $mode);
        return $this;
    }
    /**
     * Check if single mode is enabled
     * 
     * @return bool
     */
    public function isSingleMode()
    {
        return ($this->getMode() == 'single') ? true : false;
    }
    /**
     * Check if multiple mode is enabled
     * 
     * @return bool
     */
    public function isMultipleMode()
    {
        return ($this->getMode() == 'multiple') ? true : false;
    }
    /**
     * Check if information is visible
     * 
     * @return bool
     */
    public function isInformationVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_DISPLAY_INFORMATION);
    }
    /**
     * Get sort by
     * 
     * @return bool
     */
    public function getSortBy()
    {
        return Mage::getStoreConfig(self::XML_PATH_OPTIONS_SORT_BY);
    }
    /**
     * Check if sort by id
     * 
     * @return bool
     */
    public function isSortById()
    {
        return ($this->getSortBy() == 'id') ? true : false;
    }
    /**
     * Check if sort by code
     * 
     * @return bool
     */
    public function isSortByCode()
    {
        return ($this->getSortBy() == 'code') ? true : false;
    }
    /**
     * Check if sort by title
     * 
     * @return bool
     */
    public function isSortByTitle()
    {
        return ($this->getSortBy() == 'title') ? true : false;
    }
    /**
     * Check if sort by priority
     * 
     * @return bool
     */
    public function isSortByPriority()
    {
        return ($this->getSortBy() == 'priority') ? true : false;
    }
    /**
     * Check if sort by origin
     * 
     * @return bool
     */
    public function isSortByOrigin()
    {
        return ($this->getSortBy() == 'origin') ? true : false;
    }
    /**
     * Check if origin is visible
     * 
     * @return bool
     */
    public function isOriginVisible()
    {
        return (
            $this->isInformationVisible() && 
            Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_DISPLAY_ORIGIN)
        ) ? true : false;
    }
    /**
     * Check if distance is visible
     * 
     * @return bool
     */
    public function isDistanceVisible()
    {
        return (
            $this->isInformationVisible() && 
            Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_DISPLAY_DISTANCE)
        ) ? true : false;
    }
    /**
     * Get distance unit
     * 
     * @return string
     */
    public function getDistanceUnit()
    {
        return Mage::getStoreConfig(self::XML_PATH_OPTIONS_DISTANCE_UNIT);
    }
    /**
     * Check if mile distance unit is enabled
     * 
     * @return bool
     */
    public function isMileDistanceUnit()
    {
        return ($this->getDistanceUnit() == 'mi') ? true : false;
    }
    /**
     * Check if kilometer distance unit is enabled
     * 
     * @return bool
     */
    public function isKilometerDistanceUnit()
    {
        return ($this->getDistanceUnit() == 'km') ? true : false;
    }
    /**
     * Check if description is visible
     * 
     * @return bool
     */
    public function isDescriptionVisible()
    {
        return (
            $this->isInformationVisible() && 
            Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_DISPLAY_DESCRIPTION)
        ) ? true : false;
    }
    /**
     * Check if split order is enabled
     * 
     * @return bool
     */
    public function isSplitOrderEnabled()
    {
        if ($this->isMultipleMode()) {
            return (
                (Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_SPLIT_ORDER)) && 
                !($this->getWarehouseHelper()->isPayPalExpressRequest())
            ) ? true : false;
        } else {
            return false;
        }
    }
    /**
     * Check if split quantity is enabled
     * 
     * @return bool
     */
    public function isSplitQtyEnabled()
    {
        if ($this->isMultipleMode()) {
            return Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_SPLIT_QTY);
        } else {
            return false;
        }
    }
    /**
     * Check if force cart no backorders is enabled
     * 
     * @return bool
     */
    public function isForceCartNoBackordersEnabled()
    {
        if ($this->isMultipleMode()) {
            return Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_FORCE_CART_NO_BACKORDERS);
        } else {
            return false;
        }
    }
    /**
     * Check if force cart item no backorders is enabled
     * 
     * @return bool
     */
    public function isForceCartItemNoBackordersEnabled()
    {
        if ($this->isMultipleMode()) {
            return Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_FORCE_CART_ITEM_NO_BACKORDERS);
        } else {
            return false;
        }
    }
    /**
     * Get single assignment method code
     * 
     * @return string
     */
    public function getSingleAssignmentMethodCode()
    {
        if ($this->isSingleMode()) {
            return Mage::getStoreConfig(self::XML_PATH_OPTIONS_SINGLE_ASSIGNMENT_METHOD);
        } else {
            return null;
        }
    }
    /**
     * Check if assigned areas is the current single assignment method
     * 
     * @return bool
     */
    public function isAssignedAreaSingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'assigned_area') ? true : false;
    }
    /**
     * Check if nearest is the current single assignment method
     * 
     * @return bool
     */
    public function isNearestSingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'nearest') ? true : false;
    }
    /**
     * Check if assigned store is the current single assignment method
     * 
     * @return bool
     */
    public function isAssignedStoreSingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'assigned_store') ? true : false;
    }
    /**
     * Check if assigned customer group is the current single assignment method
     * 
     * @return bool
     */
    public function isAssignedCustomerGroupSingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'assigned_customer_group') ? true : false;
    }
    /**
     * Check if assigned currency is the current single assignment method
     * 
     * @return bool
     */
    public function isAssignedCurrencySingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'assigned_currency') ? true : false;
    }
    /**
     * Check if manual is the current single assignment method
     * 
     * @return bool
     */
    public function isManualSingleAssignmentMethod()
    {
        return ($this->getSingleAssignmentMethodCode() == 'manual') ? true : false;
    }
    /**
     * Get multiple assignment method code
     * 
     * @return string
     */
    public function getMultipleAssignmentMethodCode()
    {
        if ($this->isMultipleMode()) {
            return Mage::getStoreConfig(self::XML_PATH_OPTIONS_MULTIPLE_ASSIGNMENT_METHOD);
        } else {
            return null;
        }
    }
    /**
     * Check if lowest shipping is the current multiple assignment method
     * 
     * @return bool
     */
    public function isLowestShippingMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'lowest_shipping') ? true : false;
    }
    /**
     * Check if lowest tax is the current multiple assignment method
     * 
     * @return bool
     */
    public function isLowestTaxMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'lowest_tax') ? true : false;
    }
    /**
     * Check if lowest subtotal is the current multiple assignment method
     * 
     * @return bool
     */
    public function isLowestSubtotalMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'lowest_subtotal') ? true : false;
    }
    /**
     * Check if lowest grand total is the current multiple assignment method
     * 
     * @return bool
     */
    public function isLowestGrandTotalMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'lowest_grand_total') ? true : false;
    }
    /**
     * Check if nearest is the current multiple assignment method
     * 
     * @return bool
     */
    public function isNearestMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'nearest') ? true : false;
    }
    /**
     * Check if priority is the current multiple assignment method
     * 
     * @return bool
     */
    public function isPriorityMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'priority') ? true : false;
    }
    /**
     * Check if manual is the current multiple assignment method
     * 
     * @return bool
     */
    public function isManualMultipleAssignmentMethod()
    {
        return ($this->getMultipleAssignmentMethodCode() == 'manual') ? true : false;
    }
    
    /**
     * Check if adjustment is allowed
     * 
     * @return bool
     */
    public function isAllowAdjustment()
    {
        $helper = $this->getWarehouseHelper();
        return (
            $helper->isAdmin() || 
            Mage::getStoreConfigFlag(self::XML_PATH_OPTIONS_ALLOW_ADJUSTMENT) || 
            $this->isManualSingleAssignmentMethod() || 
            $this->isManualMultipleAssignmentMethod()
        ) ? true : false;
    }
    /**
     * Check if priority is enabled
     * 
     * @return bool
     */
    public function isPriorityEnabled()
    {
        return (
            $this->isPriorityMultipleAssignmentMethod() || 
            $this->isSortByPriority() || 
            $this->isSplitQtyEnabled()
        ) ? true : false;
    }
    /**
     * Check if catalog information visible
     * 
     * @return bool
     */
    public function isCatalogInformationVisible()
    {
        return (
            $this->isInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_INFORMATION)
        ) ? true : false;
    }
    /**
     * Check if catalog out of stock visible
     * 
     * @return bool
     */
    public function isCatalogOutOfStockVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_OUT_OF_STOCK)
        ) ? true : false;
    }
    /**
     * Check if catalog origin visible
     * 
     * @return bool
     */
    public function isCatalogOriginVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_ORIGIN)
        ) ? true : false;
    }
    /**
     * Check if catalog distance visible
     * 
     * @return bool
     */
    public function isCatalogDistanceVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_DISTANCE)
        ) ? true : false;
    }
    /**
     * Check if catalog description visible
     * 
     * @return bool
     */
    public function isCatalogDescriptionVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_DESCRIPTION)
        ) ? true : false;
    }
    /**
     * Check if catalog availability visible
     * 
     * @return bool
     */
    public function isCatalogAvailabilityVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_AVAILABILITY)
        ) ? true : false;
    }
    /**
     * Check if catalog qty visible
     * 
     * @return bool
     */
    public function isCatalogQtyVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_QTY)
        ) ? true : false;
    }
    /**
     * Check if catalog tax visible
     * 
     * @return bool
     */
    public function isCatalogTaxVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_TAX)
        ) ? true : false;
    }
    /**
     * Check if catalog shipping visible
     * 
     * @return bool
     */
    public function isCatalogShippingVisible()
    {
        return (
            $this->isCatalogInformationVisible() && Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_SHIPPING)
        ) ? true : false;
    }
    /**
     * Check if catalog backend manage stock visible
     * 
     * @return bool
     */
    public function isCatalogBackendManageStockVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_MANAGE_STOCK);
    }
    /**
     * Check if catalog backend min qty visible
     * 
     * @return bool
     */
    public function isCatalogBackendMinQtyVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_MIN_QTY);
    }
    /**
     * Check if catalog backend min sale qty visible
     * 
     * @return bool
     */
    public function isCatalogBackendMinSaleQtyVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_MIN_SALE_QTY);
    }
    /**
     * Check if catalog backend max sale qty visible
     * 
     * @return bool
     */
    public function isCatalogBackendMaxSaleQtyVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_MAX_SALE_QTY);
    }
    /**
     * Check if catalog backend is qty decimal visible
     * 
     * @return bool
     */
    public function isCatalogBackendIsQtyDecimalVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_IS_QTY_DECIMAL);
    }
    /**
     * Check if catalog backend backorders visible
     * 
     * @return bool
     */
    public function isCatalogBackendBackordersVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_BACKORDERS);
    }
    /**
     * Check if catalog backend notify stock qty visible
     * 
     * @return bool
     */
    public function isCatalogBackendNotifyStockQtyVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_NOTIFY_STOCK_QTY);
    }
    /**
     * Check if catalog backend enable qty increments visible
     * 
     * @return bool
     */
    public function isCatalogBackendEnableQtyIncrementsVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_ENABLE_QTY_INCREMENTS);
    }
    /**
     * Check if catalog backend qty increments visible
     * 
     * @return bool
     */
    public function isCatalogBackendQtyIncrementsVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_QTY_INCREMENTS);
    }
    /**
     * Check if catalog backend grid qty visible
     * 
     * @return bool
     */
    public function isCatalogBackendGridQtyVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_GRID_QTY);
    }
    /**
     * Check if catalog backend grid batch prices visible
     * 
     * @return bool
     */
    public function isCatalogBackendGridBatchPricesVisible()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_DISPLAY_BACKEND_GRID_BATCH_PRICES);
    }
    /**
     * Check if shelves function is enabled
     * 
     * @return bool
     */
    public function isShelvesEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATALOG_ENABLE_SHELVES);
    }
    /**
     * Check if shipping methods filter is enabled
     * 
     * @return bool
     */
    public function isShippingCarrierFilterEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHIPPING_ENABLE_CARRIER_FILTER);
    }
}
?>