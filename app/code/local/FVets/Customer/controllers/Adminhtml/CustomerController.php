<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Adminhtml/controllers/CustomerController.php';
class FVets_Customer_Adminhtml_CustomerController extends Mage_Adminhtml_CustomerController
{
    /**
     * Save customer action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(false)
            ;

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

						//se deve enviar um email de ativação...
					if (isset($data['account']['send_activate_email'])) {
						$customer = Mage::getModel('customer/customer')->load($data['customer_id']);

						$storeview = $customer->getStoreView();
						if ($storeview) {
							$storeviewId = explode(',', $storeview)[0];
							//gera um novo token para o customer
							$newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
							$customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
							$newResetPasswordLink = Mage::getModel('core/store')->load($storeviewId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'activate?id=' . $customer->getId() . '&token=' . $customer->getRpToken();

							try {
								$customer->sendAskForActivateEmail($newResetPasswordLink, $storeviewId);
							} catch (Exception $e) {
								if (strpos($e->getMessage(), 'Invalid transactional email code') !== FALSE) {
									$this->_getSession()->addError('Envio de email de ativação de usuário: O usuário não está vinculado a um grupo de acesso ou o grupo não possui um template de email configurado');
								} else {
									$this->_getSession()->addError('Envio de email de ativação de usuário: ' . $e->getMessage());
								}
							}
						}
					}
						//fim

						if (isset($data['account']['is_active'])) {
							$customerStatus = $data['account']['is_active'];
							$customer->setIsActive($customerStatus);
						}

            $errors = $customerForm->validateData($formData);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    $isDefaultBilling = isset($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $index;
                    $address->setIsDefaultBilling($isDefaultBilling);
                    $isDefaultShipping = isset($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $index;
                    $address->setIsDefaultShipping($isDefaultShipping);

					//caso não tenha nome e sobrenome cadastrado no endereço do cliente, replica o do cliente
					if(!$address->getData('firstname')) {
						$address->setData('firstname', $customer->getData('firstname'));
					}
					if(!$address->getData('lastname')) {
						$address->setData('lastname', $customer->getData('lastname'));
					}
					//fim

                    $errors = $addressForm->validateData($formData);
                    if ($errors !== true) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array(
                            'id' => $customer->getId())
                        ));
                        return;
                    }

                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
            }

            // Default billing and shipping
            if (isset($data['account']['default_billing'])) {
                $customer->setData('default_billing', $data['account']['default_billing']);
            }
            if (isset($data['account']['default_shipping'])) {
                $customer->setData('default_shipping', $data['account']['default_shipping']);
            }
            if (isset($data['account']['confirmation'])) {
                $customer->setData('confirmation', $data['account']['confirmation']);
            }

            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')
                && !$customer->getConfirmation()
            ) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = false;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(true);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = true;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                $customer->save();

								if($customer->getData('email') != $customer->getOrigData('email')) {
									$allinHelper = Mage::helper('fvets_allin');
									$allinHelper->saveTrash($customer->getOrigData('email'), $customer->getWebsiteId());
								}

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );
                Mage::dispatchEvent('adminhtml_customer_save_after', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $customer->getId(),
                        '_current' => true
                    ));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
    }

	public function validateAction()
	{
		$response       = new Varien_Object();
		$response->setError(0);
		$websiteId      = Mage::app()->getStore()->getWebsiteId();
		$accountData    = $this->getRequest()->getPost('account');

		$customer = Mage::getModel('customer/customer');
		$customerId = $this->getRequest()->getParam('id');
		if ($customerId) {
			$customer->load($customerId);
			$websiteId = $customer->getWebsiteId();
		} else if (isset($accountData['website_id'])) {
			$websiteId = $accountData['website_id'];
		}

		/* @var $customerForm Mage_Customer_Model_Form */
		$customerForm = Mage::getModel('customer/form');
		$customerForm->setEntity($customer)
			->setFormCode('adminhtml_customer')
			->setIsAjaxRequest(true)
			->ignoreInvisible(false)
		;

		$data   = $customerForm->extractData($this->getRequest(), 'account');
		$errors = $customerForm->validateData($data);
		if ($errors !== true) {
			foreach ($errors as $error) {
				$this->_getSession()->addError($error);
			}
			$response->setError(1);
		}

		# additional validate email
		if (!$response->getError()) {
			# Trying to load customer with the same email and return error message
			# if customer with the same email address exisits
			$checkCustomer = Mage::getModel('customer/customer')
				->setWebsiteId($websiteId);
			$checkCustomer->loadByEmail($accountData['email']);
			if ($checkCustomer->getId() && ($checkCustomer->getId() != $customer->getId())) {
				$response->setError(1);
				$this->_getSession()->addError(
					Mage::helper('adminhtml')->__('Customer with the same email already exists.')
				);
			}
		}

		$addressesData = $this->getRequest()->getParam('address');
		if (is_array($addressesData)) {
			/* @var $addressForm Mage_Customer_Model_Form */
			$addressForm = Mage::getModel('customer/form');
			$addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);
			foreach (array_keys($addressesData) as $index) {
				if ($index == '_template_') {
					continue;
				}
				$address = $customer->getAddressItemById($index);
				if (!$address) {
					$address   = Mage::getModel('customer/address');
				}

				//caso não tenha nome e sobrenome cadastrado no endereço do cliente, replica o do cliente - nesse trecho só para passar na validação
				if(!$address->getData('firstname')) {
					$address->setData('firstname', $customer->getData('firstname'));
				}
				if(!$address->getData('lastname')) {
					$address->setData('lastname', $customer->getData('lastname'));
				}
				//fim

				$requestScope = sprintf('address/%s', $index);
				$formData = $addressForm->setEntity($address)
					->extractData($this->getRequest(), $requestScope);

				$errors = $addressForm->validateData($formData);
				if ($errors !== true) {
					foreach ($errors as $error) {
						$this->_getSession()->addError($error);
					}
					$response->setError(1);
				}
			}
		}

		if ($response->getError()) {
			$this->_initLayoutMessages('adminhtml/session');
			$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
		}

		$this->getResponse()->setBody($response->toJson());
	}
}
