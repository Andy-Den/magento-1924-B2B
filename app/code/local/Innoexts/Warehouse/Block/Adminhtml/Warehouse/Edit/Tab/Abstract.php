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
 * Warehouse tab block
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Abstract 
    extends Innoexts_Core_Block_Adminhtml_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
    /**
     * Form field name suffix
     * 
     * @var string
     */
    protected $_formFieldNameSuffix = 'warehouse';
    /**
     * Form HTML identifier prefix
     * 
     * @var string
     */
    protected $_formHtmlIdPrefix = 'warehouse_';
    /**
     * Form field set identifier
     * 
     * @var string
     */
    protected $_formFieldsetId;
    /**
     * Form field set legend
     * 
     * @var string
     */
    protected $_formFieldsetLegend = 'Tab';
    /**
     * Model name
     * 
     * @var string
     */
    protected $_modelName = 'warehouse';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Tab';
    /**
     * Retrieve warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper() {
        return Mage::helper('warehouse');
    }
    /**
     * Check is allowed action
     * 
     * @param   string $action
     * 
     * @return  bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()->isAllowed('catalog/warehouses/'.$action);
    }
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getWarehouseHelper()->__($this->_title);
    }
    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getWarehouseHelper()->__($this->_title);
    }
    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}