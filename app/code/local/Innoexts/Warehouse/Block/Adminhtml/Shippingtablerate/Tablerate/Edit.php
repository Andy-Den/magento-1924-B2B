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
 * Table rate edit
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Edit 
    extends Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate_Edit 
{
    /**
     * Check is allowed action
     * 
     * @param   string $action
     * 
     * @return  bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()
            ->isAllowed('sales/shipping/tablerates/tablerates');
    }
}