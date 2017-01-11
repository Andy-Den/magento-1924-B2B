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
 * Table rate methods
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Method 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Container 
{
    /**
     * Block group
     * 
     * @var string
     */
    protected $_blockGroup = 'shippingtablerate';
    /**
     * Controller
     * 
     * @var string
     */
    protected $_controller = 'adminhtml_tablerate_method';
    /**
     * Header label
     * 
     * @var string
     */
    protected $_headerLabel = 'Shipping Table Rate Methods';
    /**
     * Add Label
     * 
     * @var string
     */
    protected $_addLabel = 'Add New Method';
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
     * Get text helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    public function getTextHelper()
    {
        return $this->getWarehouseHelper();
    }
    /**
     * Check is allowed action
     * 
     * @param string $action
     * 
     * @return bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()
            ->isAllowed('sales/shipping/tablerates/methods');
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('warehouse/tablerate/method.phtml');
    }
    /**
     * Get create URL
     * 
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array());
    }
}