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
 * Warehouse area tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Area 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Editable_Area_Container 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Grid block
     * 
     * @var string
     */
    protected $_gridBlockType = 'warehouse/adminhtml_warehouse_edit_tab_area_grid';
    /**
     * Form block
     * 
     * @var string
     */
    protected $_formBlockType = 'warehouse/adminhtml_warehouse_edit_tab_area_form';
    /**
     * Tab title
     * 
     * @var string
     */
    protected $_title = 'Areas';
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('warehouseAreaTab');
        $this->setTemplate('warehouse/warehouse/edit/tab/area.phtml');
    }
    /**
     * Get warehouse helper
     * 
     * @return Varien_Object
     */
    public function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Retrieve Tab class
     * 
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
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
    /**
     * Retrieve registered warehouse
     *
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    protected function getWarehouse()
    {
        return Mage::registry('warehouse');
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
     * Check if edit function enabled
     * 
     * @return bool
     */
    protected function canEdit()
    {
        $warehouse = $this->getWarehouse();
        return ($this->isSaveAllowed() && $warehouse->getId()) ? true : false;
    }
}