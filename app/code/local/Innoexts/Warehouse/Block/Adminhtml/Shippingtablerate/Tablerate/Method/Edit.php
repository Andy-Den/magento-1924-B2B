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
 * Table rate method edit
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Shippingtablerate_Tablerate_Method_Edit 
    extends Innoexts_Core_Block_Adminhtml_Widget_Form_Container 
{
    /**
     * Object identifier
     * 
     * @var string
     */
    protected $_objectId = 'method_id';
    /**
     * Block group
     * 
     * @var string
     */
    protected $_blockGroup = 'shippingtablerate';
    /**
     * Block sub group
     * 
     * @var string
     */
    protected $_blockSubGroup = 'adminhtml';
    /**
     * Controller
     * 
     * @var string
     */
    protected $_controller = 'tablerate_method';
    /**
     * Add Label
     * 
     * @var string
     */
    protected $_addLabel = 'New Method';
    /**
     * Edit label
     * 
     * @var string
     */
    protected $_editLabel = "Edit Method '%s'";
    /**
     * Save label
     * 
     * @var string
     */
    protected $_saveLabel = 'Save Method';
    /**
     * Delete label
     * 
     * @var string
     */
    protected $_deleteLabel = 'Delete Method';
    /**
     * Model name
     * 
     * @var string
     */
    protected $_modelName = 'shippingtablerate_method';
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
        if (($action == 'delete') && (1 == $this->getModel()->getId())) {
            return false;
        }
        return $this->getAdminSession()
            ->isAllowed('sales/shipping/tablerates/methods');
    }
    /**
     * Get Url parameters
     * 
     * @return array
     */
    protected function getUrlParams()
    {
        return array();
    }
    /**
     * Get URL for back (reset) button
     * 
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/*/', 
            $this->getUrlParams()
        );
    }
    /**
     * Get URL for delete button
     * 
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            '*/*/delete', 
            array_merge(
            array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId)), 
                $this->getUrlParams()
            )
        );
    }
    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl(
            '*/'.$this->_controller.'/save', 
            $this->getUrlParams()
        );
    }
}