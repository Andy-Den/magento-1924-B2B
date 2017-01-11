<?php 
/**
 * Default Helper
 * 
 * @author     Design:Slider GbR <magento@design-slider.de>
 * @copyright  (C)Design:Slider GbR <www.design-slider.de>
 * @license    OSL <http://opensource.org/licenses/osl-3.0.php>
 * @link       http://www.design-slider.de/magento-onlineshop/magento-extensions/private-sales/
 * @package    DS_PrivateSales
 */
class DS_PrivateSales_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Check if private sales is enabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)Mage::getStoreConfig('privatesales/general/enable');
    }

    /**
     * Check if current user is guest
     * 
     * @return boolean
     */
    public function haveGuest()
    {
        return !Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    /**
     * Check if catalog navigation can be shown
     * 
     * @return boolean
     */
    public function canShowNavigation()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/access/navigation');
        }
        
        return true;
    }
    
    /**
     * Check if account registration can be shown
     * 
     * @return boolean
     */
    public function canShowRegistration()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/registration/disable');
        }
        
        return true;
    }

	public function canShowPricesAndAddToCartButton()
	{
		if ($this->isEnabled() && $this->haveGuest())
		{
			return !(bool)Mage::getStoreConfig('privatesales/access/prices');
		}

		return true;
	}
    
    /**
     * Check if forgot password can be shown
     * 
     * @return boolean
     */
    public function canShowForgotPassword()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/forgot_password/disable');
        }
        
        return true;
    }
    
    /**
     * Check if cms page can be shown
     * 
     * @return boolean
     */
    public function canShowCmsPage()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/access/content');
        }
        
        return true;
    }
    
    /**
     * Check if catalog page can be shown
     * 
     * @return boolean
     */
    public function canShowCatalogPage()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/access/catalog');
        }
        
        return true;
    }
    
    /**
     * Check if overall authentication is required
     * 
     * @return boolean
     */
    public function canShowAnything()
    {
        if ($this->isEnabled() && $this->haveGuest())
        {
            return !(bool)Mage::getStoreConfig('privatesales/access/authonly');
        }
        
        return true;
    }

    /**
     * Get Show Registion Login Panel Option
     * 
     * @return int
     */
    public function getShowRegistrationLoginPanel()
    {
        if ($this->isEnabled())
        {
            return (int)Mage::getStoreConfig('privatesales/registration/login_panel');    
        }
        
        return 1;
    }
    
    /**
     * Check if Registion Login Panel can be shown
     * 
     * @return boolean
     */
    public function canShowRegistrationLoginPanel()
    {
        if ($this->isEnabled())
        {
            return ($this->getShowRegistrationLoginPanel() > 0);
        }
        
        return true;
    }

    /**
     * Get custom registration panel header
     * 
     * @return string
     */
    public function getRegistrationPanelHeader()
    {
        if ($this->isEnabled())
        {
            return Mage::getStoreConfig('privatesales/registration/panel_header');
        }
        
        return '';
        
    }

    /**
     * Get custom registration panel text
     * 
     * @return string
     */
    public function getRegistrationPanelText()
    {
        if ($this->isEnabled())
        {
            return Mage::getStoreConfig('privatesales/registration/panel_text');
        }
        
        return '';
    }

    /**
     * Check if registration panel button can be shown
     * 
     * @return boolean
     */
    public function canShowRegistrationPanelButton()
    {
        if ($this->isEnabled())
        {
            return (bool)Mage::getStoreConfig('privatesales/registration/panel_button');
        }
        
        return true;
    }

    /**
     * Get custom registration panel button text
     * 
     * @return string
     */
    public function getRegistrationPanelButtonText()
    {
        if ($this->isEnabled())
        {
            return Mage::getStoreConfig('privatesales/registration/panel_button_text');
        }
        
        return '';
    }

    /**
     * Get custom registration disabled error message
     * 
     * @return string
     */
    public function getRegistrationCustomErrorMessage()
    {
        return Mage::getStoreConfig('privatesales/registration/disable_error_msg');
    }

    /**
     * Get registration disabled error message
     * 
     * @return string
     */
    public function getRegistrationErrorMessage()
    {
        $msg = $this->getRegistrationCustomErrorMessage();
        if (!strlen(trim($msg)))
        {
            $msg = $this->__('Account registration has been locked.');
        }
        
        return $msg;
    }

    /**
     * Get custom forgot password disabled error message
     * 
     * @return string
     */
    public function getForgotPasswordCustomErrorMessage()
    {
        return Mage::getStoreConfig('privatesales/forgot_password/disable_error_msg');
    }

    /**
     * Get forgot password disabled error message
     * 
     * @return string
     */
    public function getForgotPasswordErrorMessage()
    {
        $msg = $this->getForgotPasswordCustomErrorMessage();
        if (!strlen(trim($msg)))
        {
            $msg = $this->__('Forgot password has been locked.');
        }
        
        return $msg;
    }
}