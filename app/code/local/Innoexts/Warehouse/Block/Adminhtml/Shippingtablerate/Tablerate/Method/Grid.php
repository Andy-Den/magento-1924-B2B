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
 * Table rate methods grid
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Method_Grid 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid_Area_Grid 
{
    /**
     * Object identifier
     * 
     * @var string
     */
    protected $_objectId = 'method_id';
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tablerateMethodGrid');
        $this->setDefaultSort('code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->_exportPageSize = 10000;
        $this->setEmptyText($this->getTextHelper()->__('No table rate methods found.'));
    }
    /**
     * Prepare collection object
     *
     * @return Varien_Data_Collection
     */
    protected function __prepareCollection()
    {
        return Mage::getModel('shippingtablerate/tablerate_method')->getCollection();
    }
    /**
     * Prepare columns
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Method_Grid
     */
    protected function _prepareColumns()
    {
        $textHelper     = $this->getTextHelper();
        $this->addColumn('method_id', array(
            'header'    => $textHelper->__('ID'), 
            'width'     => '80', 
            'align'     => 'left', 
            'index'     => 'method_id', 
        ));
        $this->addColumn('code', array(
            'header'    => $textHelper->__('Code'), 
            'align'     => 'left', 
            'index'     => 'code', 
        ));
        $this->addColumn('name', array(
            'header'    => $textHelper->__('Name'), 
            'align'     => 'left', 
            'index'     => 'name', 
        ));
        return $this;
    }
    /**
     * Get row URL
     * 
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit', 
            array($this->getObjectId() => $row->getId(), )
        );
    }
    /**
     * Prepare mass action
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Method_Grid
     */
    protected function _prepareMassaction()
    {
        $textHelper     = $this->getTextHelper();
        $this->setMassactionIdField('method_id');
        $block          = $this->getMassactionBlock();
        $block->setFormFieldName($this->getObjectId());
        $block->addItem('delete', array(
            'label'       => $textHelper->__('Delete'), 
            'url'         => $this->getUrl('*/*/massDelete', array()), 
            'confirm'     => $textHelper->__('Are you sure?')
        ));
        return $this;
    }
}