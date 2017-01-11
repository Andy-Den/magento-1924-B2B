<?php 
/**
 * Event Observer
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Model_Observer
{

    /**
     * Remember redirect state
     * @var boolean
     */
    private static $_virgin = true;
    
    /**
     * Environment Statics
     * @var string
     */
    private static $m, $c, $a;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$m = Mage::app()->getRequest()->getModuleName();
        self::$c = Mage::app()->getRequest()->getControllerName();
        self::$a = Mage::app()->getRequest()->getActionName();
    }
    
    /**
     * Event Handler: Check if current page can be shown and if not, redirect to login
     */
    public function checkPageAccess()
    {
        if (($this->_isBlockablePage()) && !Mage::helper('privatesales')->canShowAnything() ||
            ($this->_isCmsPage() && !Mage::helper('privatesales')->canShowCmsPage()) ||
            ($this->_isCatalogPage() && !Mage::helper('privatesales')->canShowCatalogPage()))
        {
            $this->_loginRedirect();
        }
    }
    
    /**
     * Check if current page is generally blockable (whitelist)
     * 
     * @return boolean
     */
    private function _isBlockablePage()
    {
        return !(
            (Mage::getSingleton('admin/session')->isLoggedIn()) ||
            (self::$m=='admin') ||
            (self::$m=='api') ||
            (self::$m=='customer' && self::$c=='account')
        );
    }
    
    /**
     * Check if current request points to blockable catalog page
     * 
     * @return boolean
     */
    private function _isCatalogPage()
    {
        return (self::$m=='catalog' || self::$m=='catalogsearch' || self::$m=='checkout' || self::$m=='salesrule');
    }
    
    /**
     * Check if current request points to blockable cms page
     * 
     * @return boolean
     */
    private function _isCmsPage()
    {
        return (self::$m=='cms' && self::$c=='page' && self::$a=='view');
    }
    
    /**
     * Redirect user to login page
     */
    private function _loginRedirect()
    {
        if (self::$_virgin)
        {
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(
                Mage::helper('core/url')->getCurrentUrl()
            );

            Mage::app()->getResponse()->setRedirect(
                Mage::getUrl('customer/account/login')
            );
            
            self::$_virgin = false;
        }
    }
}