<?php

class FVets_Cms_Model_Observer
{
    /**
     * Faz o redirect da home para /home quando o cliente não estiver logado e para / quando estiver.
     * @param $observer
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function redirectToHomeUrl($observer)
    {
        $params = array();
        // prepares the redirect url
        if (!Mage::helper('customer')->isLoggedIn() && Mage::app()->getRequest()->getRequestUri() == '/') {
            $params['_direct'] = 'home';
        } elseif (Mage::helper('customer')->isLoggedIn() && Mage::app()->getRequest()->getRequestUri() == '/home') {
            $params['_direct'] = '';
        }

        if (count($params) > 0) {
            // force the redirect
            $exception = new Mage_Core_Controller_Varien_Exception();
            $exception->prepareRedirect('', $params);
            throw $exception;
        }
    }

    public function addCmsPageHandle($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $identifier = $observer->getPage()->getIdentifier();
        $identifier = str_replace(array('-'), '_', $identifier);

        $actionName = strtolower($action->getFullActionName());
        $action->getLayout()->getUpdate()
            ->addHandle($actionName . '_' . $identifier);
        return $this;
    }
}