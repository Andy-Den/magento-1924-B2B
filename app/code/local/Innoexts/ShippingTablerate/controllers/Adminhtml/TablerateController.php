<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_ShippingTablerate
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Table rates controller
 * 
 * @category   Innoexts
 * @package    Innoexts_ShippingTablerate
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ShippingTablerate_Adminhtml_TablerateController 
    extends Innoexts_Core_Controller_Adminhtml_Action 
{
    /**
     * Model names
     * 
     * @var array
     */
    protected $_modelNames = array(
        'shippingtablerate' => 'shippingtablerate/tablerate', 
    );
    /**
     * Retrieve shipping table rate helper
     *
     * @return Innoexts_ShippingTablerate_Helper_Data
     */
    protected function getShippingTablerateHelper()
    {
        return Mage::helper('shippingtablerate');
    }
    /**
     * Get website id
     * 
     * @return integer
     */
    protected function getWebsiteId()
    {
        return $this->getShippingTablerateHelper()->getWebsiteId();
    }
    /**
     * Set redirect into responce
     *
     * @param   string $path
     * @param   array $arguments
     * 
     * @return Innoexts_ShippingTablerate_Adminhtml_TablerateController
     */
    protected function _redirect($path, $arguments = array())
    {
        $arguments = array_merge(array('website' => $this->getWebsiteId()), $arguments);
        parent::_redirect($path, $arguments);
        return $this;
    }
    /**
     * Get model
     * 
     * @param string $type
     * 
     * @return Mage_Core_Model_Abstract
     */
    protected function _getModel($type)
    {
        $model = parent::_getModel($type);
        $model->setWebsiteId($this->getWebsiteId());
        return $model;
    }
    /**
     * Check is allowed action
     * 
     * @return bool
     */
    protected function _isAllowed()
    {
        $adminSession = $this->getAdminSession();
        switch ($this->getRequest()->getActionName()) {
            case 'new': 
            case 'save': 
                return $adminSession->isAllowed('sales/shipping/tablerates/save'); 
                break;
            case 'delete': 
                return $adminSession->isAllowed('sales/shipping/tablerates/delete'); 
                break;
            default: 
                return $adminSession->isAllowed('sales/shipping/tablerates'); 
                break;
        }
    }
    /**
     * Index action
     */
    public function indexAction()
    {
        $helper = $this->getShippingTablerateHelper();
        $this->_indexAction('shippingtablerate', false, 'sales/shipping/tablerates', array(
            $helper->__('Sales'), 
            $helper->__('Shipping'), 
            $helper->__('Shipping Table Rates'), 
        ));
    }
    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->_gridAction('shippingtablerate', true);
    }
    /**
     * New action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    /**
     * Edit action
     */
    public function editAction()
    {
        $helper = $this->getShippingTablerateHelper();
        $this->_editAction(
            'shippingtablerate', false, 'sales/shipping/tablerates', 'tablerate_id', '', 
            $helper->__('New Rate'), $helper->__('Edit Rate'), 
            array(
                $helper->__('Sales'), 
                $helper->__('Shipping'), 
                $helper->__('Shipping Table Rates'), 
            ), 
            $helper->__('This rate no longer exists.')
        );
    }
    /**
     * Save action
     */
    public function saveAction()
    {
        $helper = $this->getShippingTablerateHelper();
        $this->_saveAction(
            'shippingtablerate', false, 'tablerate_id', '', 'edit', 
            $helper->__('The rate has been saved.'), 
            $helper->__('An error occurred while saving the rate.')
        );
    }
    /**
     * Delete action
     */
    public function deleteAction()
    {
        $helper = $this->getShippingTablerateHelper();
        $this->_deleteAction(
            'shippingtablerate', false, 'tablerate_id', '', 'edit', 
            $helper->__('Unable to find a rate to delete.'), 
            $helper->__('The rate has been deleted.')
        );
    }
    /**
     * Mass delete action
     */
    public function massDeleteAction()
    {
        $helper = $this->getShippingTablerateHelper();
        $this->_massDeleteAction(
            'shippingtablerate', false, 'tablerate_id', '', 
            $helper->__('Please select rate(s).'), 
            $helper->__('Total of %d record(s) have been deleted.')
        );
    }
    /**
     * Export rates to CSV format
     */
    public function exportCsvAction()
    {
        $this->_exportCsvAction('shipping_table_rates.csv', 'shippingtablerate/adminhtml_tablerate_grid');
    }
    /**
     * Export rates to XML format
     */
    public function exportXmlAction()
    {
        $this->_exportXmlAction('shipping_table_rates.xml', 'shippingtablerate/adminhtml_tablerate_grid');
    }
}