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
 * Table rates
 * 
 * @category   Innoexts
 * @package    Innoexts_ShippingTablerate
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_ShippingTablerate_Block_Adminhtml_Tablerate 
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
    protected $_controller = 'adminhtml_tablerate';
    /**
     * Header label
     * 
     * @var string
     */
    protected $_headerLabel = 'Shipping Table Rates';
    /**
     * Add Label
     * 
     * @var string
     */
    protected $_addLabel = 'Add New Rate';
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
     * 
     * @return  bool
     */
    protected function isAllowedAction($action)
    {
        return $this->getAdminSession()->isAllowed('sales/shipping/tablerates/'.$action);
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('shippingtablerate/tablerate.phtml');
    }
    /**
     * Get create URL
     * 
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new', array('website' => $this->getWebsiteId()));
    }
}