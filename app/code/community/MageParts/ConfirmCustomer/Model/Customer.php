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

class MageParts_ConfirmCustomer_Model_Customer extends Mage_Customer_Model_Customer
{

	/*
	 * Config paths
	 */
	const XML_PATH_APPROVAL_EMAIL_ENABLED 			= 'confirmcustomer/email/enabled';
	const XML_PATH_APPROVAL_EMAIL_TEMPLATE 			= 'confirmcustomer/email/template';
	const XML_PATH_APPROVAL_EMAIL_IDENTITY 			= 'confirmcustomer/email/identity';

	const XML_PATH_ACTIVATE_EMAIL_ENABLED 			= 'confirmcustomer/email_activate_account/enabled';
	const XML_PATH_ASK_FOR_ACTIVATE_EMAIL_TEMPLATE 			= 'confirmcustomer/email_activate_account/template_ask_for_activate';
	const XML_PATH_ACTIVATE_EMAIL_TEMPLATE 			= 'confirmcustomer/email_activate_account/template';
	const XML_PATH_ACTIVATE_EMAIL_IDENTITY 			= 'confirmcustomer/email_activate_account/identity';

	const XML_PATH_ADMIN_NOTIFICATION_ENABLED		= 'confirmcustomer/admin_notification/enabled';
	const XML_PATH_ADMIN_NOTIFICATION_TEMPLATE		= 'confirmcustomer/admin_notification/template';
	const XML_PATH_ADMIN_NOTIFICATION_IDENTITY		= 'confirmcustomer/admin_notification/identity';
	const XML_PATH_ADMIN_NOTIFICATION_RECIPIENTS	= 'confirmcustomer/admin_notification/recipients';

	const XML_PATH_GENERAL_WELCOME_EMAIL			= 'confirmcustomer/general/welcome_email';


	/**
	 * Processing object before save data
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	protected function _beforeSave()
	{

		if (Mage::getStoreConfig(FVets_Core_Helper_Data::XML_PATH_STORE_IS_BRAND))
		{
			$store  = Mage::getModel('core/store')->load(Mage::app()->getRequest()->getPost('created_at'));

			$this->setStoreId($store->getId());
			$this->setWebsiteId($store->getWebsiteId());
			$this->setStoreView($store->getId());
		}

		parent::_beforeSave();
		return $this;
	}

	/**
	 * Modifies the original function to include a custom made event
	 * dispatcher. This is to ensure that e-mails are sent out upon
	 * account confirmation / creation.
	 *
	 * @param string $type
	 * @param string $backUrl
	 * @param string $storeId
	 * @return Mage_Customer_Model_Customer
	 */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
		// whether or not extension is enabled
		$enabled = Mage::getStoreConfig(MageParts_ConfirmCustomer_Helper_Data::MP_CC_ENABLED, $storeId);

		// first check if we should send out the default welcome e-mail
		$defaultWelcomeEmailEnabled = (intval(Mage::getStoreConfig(self::XML_PATH_GENERAL_WELCOME_EMAIL, $storeId)) == 1) ? true : false;

		if (!$enabled || ($enabled && $defaultWelcomeEmailEnabled)) {
			// run parent function
			parent::sendNewAccountEmail($type, $backUrl, $storeId);
		}

		// send out admin notification e-mail
		$this->sendNewAccountNotificationEmail($storeId);

		// dispatch custom event
		Mage::dispatchEvent('customer_new_account_email_sent',
			array('customer' => $this)
		);

		return $this;
	}

	/**
     * Send email notification to customer regarding account approval
     *
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendAccountApprovalEmail($storeId = '0')
    {
    	// don't send an approval e-mail if the extension is disabled
		if (!Mage::getStoreConfig(MageParts_ConfirmCustomer_Helper_Data::MP_CC_ENABLED, $storeId)) {
			return $this;
		}
		
		// make sure approval e-mails are enabled before sending any
		$enabled = (intval(Mage::getStoreConfig(self::XML_PATH_APPROVAL_EMAIL_ENABLED, $storeId)) == 1) ? true : false;

		if ($enabled) {
			$translate = Mage::getSingleton('core/translate');

			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);

			if (!$storeId) {
				$storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
			}

			Mage::getModel('core/email_template')
				->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
				->sendTransactional(
					Mage::getStoreConfig(self::XML_PATH_APPROVAL_EMAIL_TEMPLATE, $storeId),
					Mage::getStoreConfig(self::XML_PATH_APPROVAL_EMAIL_IDENTITY, $storeId),
					$this->getEmail(),
					$this->getName(),
					array('customer' => $this));

			$translate->setTranslateInline(true);
		}
		
        return $this;
    }

	public function sendAccountActivateEmail($storeId = '0')
	{
		// don't send an approval e-mail if the extension is disabled
		if (!Mage::getStoreConfig(MageParts_ConfirmCustomer_Helper_Data::MP_CC_ENABLED, $storeId)) {
			return $this;
		}

		// make sure approval e-mails are enabled before sending any
		$enabled = (intval(Mage::getStoreConfig(self::XML_PATH_ACTIVATE_EMAIL_ENABLED, $storeId)) == 1) ? true : false;

		$translate = Mage::getSingleton('core/translate');

		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		if (!$storeId) {
			$storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
		}

		Mage::getModel('core/email_template')
			->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
			->sendTransactional(
				Mage::getStoreConfig(self::XML_PATH_ACTIVATE_EMAIL_TEMPLATE, $storeId),
				Mage::getStoreConfig(self::XML_PATH_ACTIVATE_EMAIL_IDENTITY, $storeId),
				$this->getEmail(),
				$this->getName(),
				array('customer' => $this));

		$translate->setTranslateInline(true);

		return $this;
	}

	public function sendAskForActivateEmail($activatelinkurl, $storeviewId)
	{
		// don't send an approval e-mail if the extension is disabled
		if (!Mage::getStoreConfig(MageParts_ConfirmCustomer_Helper_Data::MP_CC_ENABLED, $storeviewId)) {
			return $this;
		}

		// make sure activate e-mails are enabled before sending any
		$enabled = (intval(Mage::getStoreConfig(self::XML_PATH_ACTIVATE_EMAIL_ENABLED, $storeviewId)) == 1) ? true : false;
		if (!$enabled) {
			return $this;
		}

		$translate = Mage::getSingleton('core/translate');

		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);

		$this->setActivatelinkurl($activatelinkurl);

		Mage::getModel('core/email_template')
			->setDesignConfig(array('area' => 'frontend', 'store' => $storeviewId))
			->sendTransactional(
				Mage::getStoreConfig(self::XML_PATH_ASK_FOR_ACTIVATE_EMAIL_TEMPLATE, $storeviewId),
				Mage::getStoreConfig(self::XML_PATH_ACTIVATE_EMAIL_IDENTITY, $storeviewId),
				$this->getEmail(),
				$this->getName(),
				array('customer' => $this));

		$translate->setTranslateInline(true);

		return $this;
	}

	/**
	 * Send email notification to customer regarding account approval
	 *
	 * @throws Mage_Core_Exception
	 * @return Mage_Customer_Model_Customer
	 */
	public function sendNewAccountNotificationEmail($storeId = '0')
	{
		// don't send a notification e-mail if the extension is disabled
		if (!Mage::getStoreConfig(MageParts_ConfirmCustomer_Helper_Data::MP_CC_ENABLED, $storeId)) {
			return $this;
		}

		// make sure notification e-mails are enabled before sending any
		$enabled = (intval(Mage::getStoreConfig(self::XML_PATH_ADMIN_NOTIFICATION_ENABLED, $storeId)) == 1) ? true : false;

		if ($enabled) {
			$translate = Mage::getSingleton('core/translate');

			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);

			if (!$storeId) {
				$storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
			}

			// set up list of recipients
			$recipients = array();

			// get recipient list from config
			$recipientsConfig = Mage::getStoreConfig(self::XML_PATH_ADMIN_NOTIFICATION_RECIPIENTS, $storeId);

			if (!empty($recipientsConfig)) {
				if (strrpos($recipientsConfig,',') > 0) {
					$recipientArr = explode(',',$recipientsConfig);

					if (count($recipientArr)) {
						$recipients = $recipientArr;
					}
				}
				else if (strrpos($recipientsConfig,'@') > 0) {
					$recipients = array($recipientsConfig);
				}
			}

			// send notification e-mail to each recipient
			if (count($recipients)) {
				foreach ($recipients as $address) {
					try {
					Mage::getModel('core/email_template')
						->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
						->sendTransactional(
						Mage::getStoreConfig(self::XML_PATH_ADMIN_NOTIFICATION_TEMPLATE, $storeId),
						Mage::getStoreConfig(self::XML_PATH_ADMIN_NOTIFICATION_IDENTITY, $storeId),
						$address,
						$this->getName(),
						array('customer' => $this, 'address' => $this->getAddresses()[0]));
					} catch (Exception $ex) {
						//let the flow continues
					}
				}
			}

			$translate->setTranslateInline(true);
		}

		return $this;
	}

	public function sendNewRegisteredAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
	{
		$types = array(
			'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
			'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
			'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
		);
		if (!isset($types[$type])) {
			Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
		}

		if (!$storeId) {
			$storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
		}

		$this->_sendEmailTemplate($types[$type], self::XML_PATH_REGISTER_EMAIL_IDENTITY,
			array('customer' => $this, 'back_url' => $backUrl), $storeId);

		return $this;
	}

	/**
	 * Validate customer attribute values for activate action.
	 * For existing customer password + confirmation will be validated only when password is set (i.e. its change is requested)
	 *
	 * @return bool
	 */
	public function activateValidate()
	{
		$errors = array();

		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
		}

		$password = $this->getPassword();
		if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
			$errors[] = Mage::helper('customer')->__('The password cannot be empty.');
		}
		if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
			$errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
		}
		$confirmation = $this->getPasswordConfirmation();
		if ($password != $confirmation) {
			$errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
		}

		if (empty($errors)) {
			return true;
		}
		return $errors;
	}

}