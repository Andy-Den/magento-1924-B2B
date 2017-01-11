<?php
/**
* Inchoo
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
* Please do not edit or add to this file if you wish to upgrade
* Magento or this extension to newer versions in the future.
** Inchoo *give their best to conform to
* "non-obtrusive, best Magento practices" style of coding.
* However,* Inchoo *guarantee functional accuracy of
* specific extension behavior. Additionally we take no responsibility
* for any possible issue(s) resulting from extension usage.
* We reserve the full right not to provide any kind of support for our free extensions.
* Thank you for your understanding.
*
* @category Inchoo
* @package SocialConnect
* @author Marko Martinović <marko.martinovic@inchoo.net>
* @copyright Copyright (c) Inchoo (http://inchoo.net/)
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

class Inchoo_SocialConnect_FacebookController extends Inchoo_SocialConnect_Controller_Abstract
{

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        Mage::helper('inchoo_socialconnect/facebook')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('Sua conta do Facebook foi desconectada com sucesso.')
            );
    }

    protected function _connectCallback() {
        $errorCode = $this->getRequest()->getParam('error');
        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        if(!($errorCode || $code) && !$state) {
            // Direct route access - deny
            return $this;
        }

        if(!$state || $state != Mage::getSingleton('core/session')->getFacebookCsrf()) {
            return $this;
        }

        if($errorCode) {
            // Facebook API read light - abort
            if($errorCode === 'access_denied') {
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Tentativa de conexão com conta do Facebook abortada.')
                    );

                return $this;
            }

            throw new Exception(
                sprintf(
                    $this->__('Desculpe-nos, o correu o erro "%s". Por favor, tente novamente.'),
                    $errorCode
                )
            );
        }

        if ($code) {
            // Facebook API green light - proceed
            $info = Mage::getModel('inchoo_socialconnect/facebook_info')->load();
            /* @var $info Inchoo_SocialConnect_Model_Facebook_Info */

            $token = $info->getClient()->getAccessToken();

            $customersByFacebookId = Mage::helper('inchoo_socialconnect/facebook')
                ->getCustomersByFacebookId($info->getId());

            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                // Logged in user
                if($customersByFacebookId->getSize()) {
                    // Facebook account already connected to other account - deny
                    Mage::getSingleton('core/session')
                        ->addNotice(
                            $this->__('Sua conta do Facebook está conectada com sua conta 4Vets.')
                        );

                    return $this;
                }

                // Connect from account dashboard - attach
                $customer = Mage::getSingleton('customer/session')->getCustomer();

                Mage::helper('inchoo_socialconnect/facebook')->connectByFacebookId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Sua conta do Facebook foi conectada à sua conta 4Vets com sucesso.')
                );

                return $this;
            }

            if($customersByFacebookId->getSize()) {
                // Existing connected user - login
                $customer = $customersByFacebookId->getFirstItem();

                Mage::helper('inchoo_socialconnect/facebook')->loginByCustomer($customer);

                Mage::getSingleton('core/session')
                    ->addSuccess(
                        $this->__('Você realizou login com sucesso através da sua conta do Facebook.')
                    );

                return $this;
            }

            $customersByEmail = Mage::helper('inchoo_socialconnect/facebook')
                ->getCustomersByEmail($info->getEmail());

            if($customersByEmail->getSize()) {
                // Email account already exists - attach, login
                $customer = $customersByEmail->getFirstItem();

                Mage::helper('inchoo_socialconnect/facebook')->connectByFacebookId(
                    $customer,
                    $info->getId(),
                    $token
                );

                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('Identificamos que você já tem uma conta no nosso site. Sua conta do facebook agora está conectada na 4Vets.')
                );

                return $this;
            }

            // New connection - create, attach, login
            $firstName = $info->getFirstName();
            if(empty($firstName)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Facebook first name. Please try again.')
                );
            }

            $lastName = $info->getLastName();
            if(empty($lastName)) {
                throw new Exception(
                    $this->__('Sorry, could not retrieve your Facebook last name. Please try again.')
                );
            }

            Mage::helper('inchoo_socialconnect/facebook')->connectByCreatingAccount(
                $info->getEmail(),
                $info->getFirstName(),
                $info->getLastName(),
                $info->getId(),
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Sua conta do Facebook está agora conectada à sua nova conta 4Vets.')
            );
        }
    }

}