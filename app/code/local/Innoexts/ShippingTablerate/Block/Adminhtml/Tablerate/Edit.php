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
 * Table rate edit
 *
 * @category   Innoexts
 * @package    Innoexts_ShippingTablerate
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate_Edit 
    extends Innoexts_Core_Block_Adminhtml_Widget_Form_Container 
{
    /**
     * Object identifier
     * 
     * @var string
     */
    protected $_objectId = 'tablerate_id';
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
    protected $_controller = 'tablerate';
    /**
     * Add Label
     * 
     * @var string
     */
    protected $_addLabel = 'New Rate';
    /**
     * Edit label
     * 
     * @var string
     */
    protected $_editLabel = "Edit Rate '%s'";
    /**
     * Save label
     * 
     * @var string
     */
    protected $_saveLabel = 'Save Rate';
    /**
     * Delete label
     * 
     * @var string
     */
    protected $_deleteLabel = 'Delete Rate';
    /**
     * Model name
     * 
     * @var string
     */
    protected $_modelName = 'shippingtablerate';
    /**
     * Website
     * 
     * @var Mage_Core_Model_Website
     */
    protected $_website;
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
     * Retrieve text helper
     *
     * @return Innoexts_ShippingTablerate_Helper_Data
     */
    public function getTextHelper()
    {
        return $this->getShippingTablerateHelper();
    }
    /**
     * Get website
     * 
     * @return Mage_Core_Model_Website
     */
    protected function getWebsite()
    {
        if (is_null($this->_website)) {
            $this->_website = $this->getShippingTablerateHelper()->getWebsite();
        }
        return $this->_website;
    }
    /**
     * Get website identifier
     * 
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->getShippingTablerateHelper()->getWebsiteId($this->getWebsite());
    }
    /**
     * Check is allowed action
     * 
     * @param   string $action
     * @return  bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()->isAllowed('sales/shipping/tablerates/'.$action);
    }
    /**
     * Preparing block layout
     *
     * @return Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate_Edit
     */
    protected function _prepareLayout()
    {
        $json = Mage::helper('innoexts_core')->getDirectoryHelper()->getRegionJson2();
        $this->_formScripts[] = 'var updater = new RegionUpdater("shippingtablerate_dest_country_id", "none", "shippingtablerate_dest_region_id", '.$json.', "disable")';
        parent::_prepareLayout();
        return $this;
    }
    /**
     * Get Url parameters
     * 
     * @return array
     */
    protected function getUrlParams()
    {
        return array('website' => $this->getWebsiteId());
    }
    /**
     * Get URL for back (reset) button
     * 
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', $this->getUrlParams());
    }
    /**
     * Get URL for delete button
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array_merge(
            array($this->_objectId => $this->getRequest()->getParam($this->_objectId)), $this->getUrlParams()
        ));
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
        return $this->getUrl('*/'.$this->_controller.'/save', $this->getUrlParams());
    }
}