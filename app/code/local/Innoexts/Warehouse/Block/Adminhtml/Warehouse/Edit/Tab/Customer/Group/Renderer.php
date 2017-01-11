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
 * Warehouse customer group renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Customer_Group_Renderer 
    extends Mage_Adminhtml_Block_Widget 
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;
    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->setTemplate('warehouse/warehouse/edit/tab/customer/group/renderer.phtml');
    }
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
     * Set form element
     * 
     * @param Varien_Data_Form_Element_Abstract $element
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element; 
        return $this;
    }
    /**
     * Get form element
     * 
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }
    /**
     * Render block
     * 
     * @param Varien_Data_Form_Element_Abstract $element
     * 
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    /**
     * Retrieve registered warehouse
     *
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouse()
    {
        return Mage::registry('warehouse');
    }
    /**
     * Get customer groups
     * 
     * @return array
     */
    public function getCustomerGroups()
    {
        return $this->getWarehouseHelper()->getCustomerHelper()->getGroups();
    }
}