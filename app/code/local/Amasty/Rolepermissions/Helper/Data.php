<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_skipObjectRestriction = null;

    public function currentRule()
    {
        if (($rule = Mage::registry('current_amrolepermissions_rule')) == null)
        {
            $rule = Mage::getModel('amrolepermissions/rule')->loadCurrent();
            Mage::register('current_amrolepermissions_rule', $rule, true);
        }
        return $rule;
    }

    public function redirectHome()
    {
        if (!Mage::getSingleton('admin/session')->getUser())
            return;

        Mage::getSingleton('adminhtml/session')->addError($this->__('Access Denied'));

        if (Mage::app()->getRequest()->getActionName() == 'index')
        {
            $page = Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl();

            $url = Mage::helper('adminhtml')->getUrl($page);
        }
        else
            $url = Mage::helper('adminhtml')->getUrl('*/*');

        Mage::app()->getResponse()
            ->setRedirect($url)
            ->sendResponse();

        exit(0);
    }

    public function restrictObjectByStores($data)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($data);
        if ($value)
        {
            $rule = Mage::helper('amrolepermissions')->currentRule();

            if ($isWebsite)
                $allowedIds = $rule->getPartiallyAccessibleWebsites();
            else
                $allowedIds = $rule->getScopeStoreviews();

            if (!is_array($value))
                $value = explode(',', $value);

            if (($value != array(0)) && !array_intersect($value, $allowedIds))
                $this->redirectHome();
        }
        return $this;
    }

    public function alterObjectStores($object)
    {
        list($name, $value, $isWebsite) = $this->_getRelationField($object->getData());
        if ($value)
        {
            if (!is_array($value))
            {
                $value = explode(',', $value);
                $array = false;
            }
            else
                $array = true;

            if ($object->getId())
            {
                list($origName, $origValue, $isWebsite) = $this->_getRelationField($object->getOrigData());

                if ($origName === null)
                {
                    $oldObject = clone $object;
                    $oldObject->load($object->getId());

                    list($origName, $origValue, $isWebsite) = $this->_getRelationField($oldObject->getOrigData());
                }

                if (!is_array($origValue))
                    $origValue = explode(',', $origValue);
            }
            else
                $origValue = array();

            if ($value != $origValue)
            {
                $rule = Mage::getModel('amrolepermissions/rule')->loadCurrent();

                if ($isWebsite)
                    $allowedIds = $rule->getPartiallyAccessibleWebsites();
                else
                    $allowedIds = $rule->getScopeStoreviews();

                $newValue = $this->combine($origValue, $value, $allowedIds);

                if (!$array)
                    $newValue = implode(',', array_filter($newValue));

                $object->setData($name, $newValue);
            }
        }

        return $this;
    }

    public function combine($old, $new, $allowed)
    {
        if (!is_array($old))
            $old = array();

        $map = array_flip(array_unique(array_merge($new, $old)));

        foreach ($map as $id => $order)
        {
            if (in_array($id, $allowed))
            {
                if (!in_array($id, $new))
                    unset($map[$id]);
            }
            else
            {
                if (!in_array($id, $old))
                    unset($map[$id]);
            }
        }

        return array_keys($map);
    }

    protected function _getRelationField($data)
    {
        if (!$data)
            return false;

        $fieldNames = array(
            'stores', 'store_id', 'store_ids',
            'websites', 'website_id', 'website_ids',
        );

        foreach ($fieldNames as $name)
        {
            if (isset($data[$name]))
            {
                if (substr($name, 0, 7) == 'website')
                    $isWebsite = true;
                else
                    $isWebsite = false;

                return array($name, $data[$name], $isWebsite);
            }
        }
    }

    public function checkClass($object, $type)
    {
        if ($object instanceof $type)
            return true;

        if (strpos($type, 'Mysql4') !== FALSE)
        {
            $version = Mage::getVersionInfo();

            if ($version['minor'] >= 6) {
                $type = str_replace('Mysql4', 'Resource', $type);
                return $object instanceof $type;
            }
        }

        return false;
    }

    public function canSkipObjectRestriction()
    {
        if ($this->_skipObjectRestriction === null)
        {
            $this->_skipObjectRestriction = false;

            $action = Mage::app()->getRequest()->getActionName();

            if (in_array($action, array('edit', 'view')))
            {
                $controller = Mage::app()->getRequest()->getControllerName();

                $rule = Mage::helper('amrolepermissions')->currentRule();

                if (
                    (!$rule->getLimitOrders() && $controller == 'sales_order')
                    ||
                    (!$rule->getLimitInvoices() && ($controller == 'sales_invoice' || $controller == 'sales_transactions'))
                    ||
                    (!$rule->getLimitShipments() && $controller == 'sales_shipment')
                    ||
                    (!$rule->getLimitMemos() && $controller == 'sales_creditmemo')
                )
                {
                    $this->_skipObjectRestriction = true;
                }
            }
        }

        return $this->_skipObjectRestriction;
    }

    public function setSkipRestrition($restrition)
    {
        Mage::helper('amrolepermissions')->currentRule()->setSkipRestrition($restrition);
    }

    public function getSkipRestrition()
    {
        return Mage::helper('amrolepermissions')->currentRule()->getSkipRestrition();
    }

    public function reloadPage($request, array $params)
    {
        $requestParams = $request->getParams();

        foreach ($params as $key => $value)
        {
            $requestParams[$key] = $value;
        }

        unset($requestParams['key']);
        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/*", $requestParams));
    }
}
