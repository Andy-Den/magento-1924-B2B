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

class MageParts_ConfirmCustomer_Model_Observer
{

	/**
	 * Retrieve customer session
	 *
	 * @return Mage_Customer_Model_Session
	 */
	public function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * Check if a customer is approved before signing in
	 *
	 * Executed on: customer_login
	 *
	 * @param object $event
	 */
	public function checkApproveStatus($event)
	{
		// skip this checkup for the account creation / confirmation process
		$actionName = strtolower(Mage::app()->getRequest()->getActionName());

		if ($actionName == 'createpost' || $actionName == 'create' || $actionName == 'confirm' || $actionName == 'confirmation') {
			return;
		}

		// make sure extension is enabled before we continue
		if (!Mage::helper('confirmcustomer')->getIsEnabled()) {
			return;
		}

		// do redirect
		$this->redirectCustomer($event->getCustomer());
	}

	/**
	 * Check if a customer is approved before signing in after account is created
	 *
	 * Executed on: customer_new_account_email_sent
	 *
	 * @param object $customer
	 */
	public function customerNewAccountCompleted($customer)
	{
		// make sure extension is enabled before we continue
		if (!Mage::helper('confirmcustomer')->getIsEnabled()) {
			return;
		}

		// do redirect
		$this->redirectCustomer($customer);
	}

	/**
	 * Redirect customer to another page
	 *
	 * @param $customer Mage_Customer_Model_Customer
	 * @return void
	 */
	public function redirectCustomer($customer)
	{
		// get helper object
		$helper = Mage::helper('confirmcustomer');

		// in some cases the $customer variable will be an event including a customer object
		if (!($customer instanceof Mage_Customer_Model_Customer)) {
			if ($customer->getCustomer()) {
				$customer = $customer->getCustomer();
			}
		}

		// now check if the customer who just logged in is approved
		$approved = (int) $customer->getMpCcIsApproved() == MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED ? true : false;

		// customer wasn't approved
		if (!$approved) {
			// logout customer
			$this->_getCustomerSession()->logout()
            	->setBeforeAuthUrl(Mage::getUrl());

			if ($helper->getRedirectEnabled()) {
				// get redirect URL
				$redirectURL = $helper->getRedirectUrl();

				Mage::getSingleton('core/session')->getMessages(true); // The true is for clearing them after loading them
				Mage::getSingleton('customer/session')->getMessages(true); // The true is for clearing them after loading them
				// redirect customer
				header("Status: 301");
				header('Location: '.$redirectURL);
				exit;
				//Mage::getSingleton('customer/session')->setBeforeAuthUrl($redirectURL);
			}
			else if ($helper->getErrorMsgEnabled()) {
				$this->_getCustomerSession()->addError($helper->getErrorMsgText());
				return;
			}
			else if ($helper->getExceptionEnabled()) {
				throw Mage::exception('Mage_Core', $helper->getErrorMsgText(),	5
				);
				return;
			}

		}
	}

}