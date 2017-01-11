<?php
/**
 * MageParts
 * 
 * NOTICE OF LICENSE
 * 
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright 
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates. 
 * For information regarding modifications see http://www.magentocommerce.com.
 *  
 * DISCLAIMER
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE 
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF 
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category   MageParts
 * @package    MageParts_ConfirmCustomer
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author 	   MageParts Crew
 */

class MageParts_ConfirmCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * States of approval
     */
    const STATE_APPROVED = 1;
    const STATE_UNAPPROVED_WAITING = 0;
	const STATE_UNAPPROVED_FINANCIAL = 2;
	const STATE_UNAPPROVED_DUPLICATED = 3;
	const STATE_UNAPPROVED_INACTIVE = 4;



	/**
     * system config options
     */
    const MP_CC_ENABLED = 'confirmcustomer/general/enabled';

    const MP_CC_REDIRECT_ENABLED 	= 'confirmcustomer/redirect/enabled';
	const MP_CC_REDIRECT_CMS_PAGE 	= 'confirmcustomer/redirect/cms_page';
	const MP_CC_REDIRECT_USE_CUSTOM = 'confirmcustomer/redirect/use_custom_url';
	const MP_CC_REDIRECT_CUSTOM_URL = 'confirmcustomer/redirect/custom_url';

	const MP_CC_ERROR_MSG_ENABLED	= 'confirmcustomer/error_msg/enabled';
	const MP_CC_ERROR_MSG_TEXT		= 'confirmcustomer/error_msg/text';

	/**
	 * Whether or not the extension is enabled
	 *
	 * @var boolean
	 */
	protected $_enabled;

	/**
	 * Whether or not error messages is enabled
	 *
	 * @var boolean
	 */
	protected $_errorMsgEnabled;

	/**
	 * Error message text
	 *
	 * @var string
	 */
	protected $_errorMsgText;

	/**
	 * Whether or not redirect is enabled
	 *
	 * @var boolean
	 */
	protected $_redirectEnabled;

	/**
	 * Store id
	 *
	 * @var int
	 */
	protected $_storeId;

	/**
	 * Redirect URL for unapproved customers attempting to sign in
	 *
	 * @var string
	 */
	protected $_redirectURL;


	/**
	 * Retrieve whether or not the extension is enabled
	 *
	 * @return boolean
	 */
	public function getIsEnabled()
	{
		if(is_null($this->_enabled)) {
			$this->_enabled = intval(Mage::getStoreConfig(self::MP_CC_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_enabled;
	}

	/**
	 * Get current store id
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if(is_null($this->_storeId)) {
			$this->_storeId = intval(Mage::app()->getStore()->getId());
		}

		return $this->_storeId;
	}

	/**
	 * Get whether or not error messages is enabled
	 *
	 * @return boolean
	 */
	public function getErrorMsgEnabled()
	{
		if(is_null($this->_errorMsgEnabled)) {
			$this->_errorMsgEnabled = intval(Mage::getStoreConfig(self::MP_CC_ERROR_MSG_ENABLED, $this->getStoreId()))==1 ? true : false;
		}
		return $this->_errorMsgEnabled;
	}

	public function getExceptionEnabled()
	{
		return Mage::getSingleton('customer/session')->getExceptionEnabled();
	}

	/**
	 * Error message to be displayed, if any
	 *
	 * @return string
	 */
	public function getErrorMsgText()
	{
		if(is_null($this->_errorMsgText)) {
			$this->_errorMsgText = $this->__(Mage::getStoreConfig(self::MP_CC_ERROR_MSG_TEXT, $this->getStoreId()));
		}
		return $this->_errorMsgText;
	}

	/**
	 * Get whether or not redirection is enabled
	 *
	 * @return boolean
	 */
	public function getRedirectEnabled()
	{
		//if(is_null($this->_redirectEnabled)) {
			$this->_redirectEnabled = Mage::getSingleton('customer/session')->getRedirectEnabled();
			if (is_null($this->_redirectEnabled)) {
				$this->_redirectEnabled = intval(Mage::getStoreConfig(self::MP_CC_REDIRECT_ENABLED, $this->getStoreId())) == 1 ? true : false;
			}
		//}
		return $this->_redirectEnabled;
	}

	/**
	 * Retrieve redirection URL for unapproved customers
	 *
	 * @return boolean
	 */
	public function getRedirectUrl()
	{
		if(is_null($this->_redirectURL)) {
			// get store id
			$storeId = $this->getStoreId();
			//Verifica se o usuário pode acessar a store atual
			$storeviews = explode(',',Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId())->getStoreView());
			if (!in_array(Mage::app()->getStore()->getId(), $storeviews))
			{
				$storeId = $storeviews[0];
			}


			if ($this->getRedirectEnabled()) {
				// check if we should use a custom URL or CMS page
				$useCustomUrl = intval(Mage::getStoreConfig(self::MP_CC_REDIRECT_USE_CUSTOM, $storeId))==1 ? true : false;

				if ($useCustomUrl) {
					$this->_redirectURL = Mage::getStoreConfig(self::MP_CC_REDIRECT_CUSTOM_URL, $storeId);
				}
				else {
					// get CMS page identifier
					$pageId = Mage::getStoreConfig(self::MP_CC_REDIRECT_CMS_PAGE, $storeId);

					if (!empty($pageId)) {
						// check if id includes a delimiter
						$delPos = strrpos($pageId, '|');

						// get page id by delimiter position
						if ($delPos) {
							$pageId = substr($pageId, 0, $delPos);
						}

						// retrieve redirect URL
						$this->_redirectURL = $this->getPageUrl($pageId, $storeId);
					}
				}
			}
		}

		return $this->_redirectURL;
	}

	/**
	 * Get states of approval
	 *
	 * @return array
	 */
	public function getApprovalStates()
	{
		return array(
			self::STATE_APPROVED => $this->__('Yes'),
			self::STATE_UNAPPROVED_WAITING => $this->__('No / Waiting'),
			self::STATE_UNAPPROVED_FINANCIAL => $this->__('No / Financial'),
			self::STATE_UNAPPROVED_DUPLICATED => $this->__('No / Duplicated'),
			self::STATE_UNAPPROVED_INACTIVE => $this->__('No / Inactive'),
		);
	}

	/**
	 * Retrieve readable version of running Magento installation
	 *
	 * @return float
	 */
	public function getMagentoVersion()
	{
		// get current magento version
    	$version = Mage::getVersion();

    	// get position of first '.'
    	$pos = strpos($version,'.');

    	// remove all '.' after the first one
    	$version1 = substr($version,0,$pos+1);
    	$version2 = str_replace('.','',substr($version,$pos+1));

    	// parse the version number to a float number
    	$version = floatval("{$version1}{$version2}");

    	return $version;
	}

	/**
	 * Retrieve page direct URL
	 *
	 * @param string $pageId
	 * @return string
	 */
	public function getPageUrl($pageId = null, $storeId = null)
	{
		if (!$storeId) {
			$storeId = Mage::app()->getStore()->getId();
		}
		$page = Mage::getModel('cms/page');
		if (!is_null($pageId) && $pageId !== $page->getId()) {
			$page->setStoreId($storeId);
			if (!$page->load($pageId)) {
				return null;
			}
		}

		if (!$page->getId()) {
			return null;
		}

		return Mage::app()->getStore($storeId)->getUrl(null, array('_direct' => $page->getIdentifier()));
	}

}