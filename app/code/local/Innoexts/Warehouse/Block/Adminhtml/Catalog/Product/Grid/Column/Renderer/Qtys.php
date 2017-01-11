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
 * Product grid quantities renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Grid_Column_Renderer_Qtys 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text 
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
     * Render a grid cell as qtys
     * 
     * @param Varien_Object $row
     * 
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $helper     = $this->getWarehouseHelper();
        $value      = $row->getData($this->getColumn()->getIndex());
        if (is_array($value) && count($value)) {
            $output     = '<table cellspacing="0" class="qty-table"><col width="100"/><col width="40"/>';
            $totalQty   = 0;

			$available_stockIds = array_keys(Mage::helper('warehouse/catalog_product')->getStockItems($row));

            foreach ($value as $stockId => $qty) {

				if (!in_array($stockId, $available_stockIds))
				{
					continue;
				}

				$stores = $helper->getWarehouseByStockId($stockId)->getStores();

				$rule = Mage::helper('amrolepermissions')->currentRule();

				foreach ($stores as $store)
				{
					if($rule->storeAccessible($store))
					{
						$output .= '<tr><td>'.
							$helper->getWarehouseTitleByStockId($stockId).'</td><td>'.((!is_null($qty))?$qty:$helper->__('N / A')).
							'</td></tr>';
						$totalQty += floatval($qty);
						break;
					}
				}
            }

            $output .= '<tr><td><strong>Total</strong></td><td><strong>'.$totalQty.'</strong></td></tr>';
            $output .= '</table>';
            return $output;
        }
        return '';
    }
}