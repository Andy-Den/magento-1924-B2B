<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Helper_Block extends Mage_Core_Helper_Abstract
{
    protected $_allowedOptions = array();

    public function getAllowedOptions()
    {
        return $this->_allowedOptions;
    }

    public function addRulesTabs($block)
    {
        $role = Mage::registry('current_role');

        $rule = Mage::getModel('amrolepermissions/rule')->load($role->getId(), 'role_id');
        if (!$rule->getId())
            $rule->setRoleId($role->getId());

        Mage::register('current_rule', $rule, true);

        $block->addTab(
            'amrolepermissions_scope',
            $block->getLayout()->createBlock('amrolepermissions/adminhtml_tab_scope')
        );

        $block->addTab(
            'amrolepermissions_categories',
            $block->getLayout()->createBlock('amrolepermissions/adminhtml_tab_categories')
        );

        $block->addTab(
            'amrolepermissions_categories',
            array(
                'class'     => 'ajax',
                'url'       => Mage::getUrl('adminhtml/amrolepermissions_categories/categories', array('_current' => true)),
                'label'     => $this->__('Advanced: Categories'),
                'title'     => $this->__('Advanced: Categories')
            )
        );

        $block->addTab(
            'amrolepermissions_products',
            array(
                'class'     => 'ajax',
                'url'       => Mage::getUrl('adminhtml/amrolepermissions_products/products', array('_current' => true)),
                'label'     => $this->__('Advanced: Products'),
                'title'     => $this->__('Advanced: Products')
            )
        );

        $block->addTab(
            'amrolepermissions_entities',
            $block->getLayout()->createBlock('amrolepermissions/adminhtml_tab_entities')
        );
    }

    public function addDuplicateRoleButton($block)
    {
        $id = Mage::app()->getRequest()->getParam('rid');
        $duplicateUrl = $block->getUrl('adminhtml/amrolepermissions_role/duplicate', array('id' => $id));

        $duplicateButton = $block->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('amrolepermissions')->__('Duplicate Role'),
                'onclick'   => "window.location.href='$duplicateUrl'",
                'class' => 'save'
            ));

        $saveButton = $block->getChild('saveButton');

        $block->unsetChild('saveButton');

        $list = $block->getLayout()->createBlock('adminhtml/text_list');

        $list
            ->append($duplicateButton)
            ->append($saveButton);

        $block->setChild('saveButton', $list);
    }

    public function alterWebsitesBlock($block)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews() || $rule->getScopeWebsites())
        {
            $block->setTemplate('amasty/amrolepermissions/websites.phtml');
            $websites = $block->getWebsiteCollection();

            $isNew = !$block->getProduct()->getId();

            $accessibleWebsites = 0;
            foreach ($websites as $ws)
            {
                $accessible = in_array($ws->getId(), $rule->getPartiallyAccessibleWebsites());

                $websiteChecked = $block->hasWebsite($ws->getId());
                if ($websiteChecked || $isNew && $accessible)
                {
                    if ($isNew)
                        $ws->setForceChecked(true);

                    $accessibleWebsites++;
                }

                $ws->setAccessible($accessible);

                if (!$accessible) {
                    $websites->removeItemByKey($ws->getId());
                }
            }

            if ($accessibleWebsites == 0)
                Mage::helper('amrolepermissions')->redirectHome();

            $block->setWebsiteRestrictedCollection($websites);
        }
    }

    public function alterStoreSwitcher($block)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        $request = Mage::app()->getRequest();
        if (!$request->getParam('store')) // "All store views"
        {
            if (Mage::app()->getRequest()->isXmlHttpRequest())
                return;

            $views = $rule->getScopeStoreviews();
            if ($views) // Redirect to firs available store view
            {
                $params = $request->getParams();
                $params['store'] = $views[0];
                unset($params['key']);
                $return = $request->getControllerName() == 'catalog_category' ? "*/*/" : "*/*/*";
                Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl($return, $params));
            }
        }

        if ($rule->getScopeStoreviews())
        {
            $block->setStoreIds($rule->getScopeStoreviews());
            $block->hasDefaultOption(false);
        }
    }

    public function disableStores($html)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            $this->_allowedOptions = $rule->getScopeStoreviews();
            $html = $this->_disableListElements($html, 'disableOptionCallback');
        }

        return $html;
    }

    public function disableWebsites($html)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
        {
            $this->_allowedOptions = $rule->getPartiallyAccessibleWebsites();
            $html = $this->_disableListElements($html, 'disableOptionCallback');
        }

        return $html;
    }

    protected function _disableListElements($html, $callback)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
            $html = preg_replace_callback('/<option[^>]+value="(\d*)".*?>/', array($this, $callback), $html);

        return $html;
    }

    public function removeDefaultStoreOption($html)
    {
        $rule = Mage::helper('amrolepermissions')->currentRule();

        if ($rule->getScopeStoreviews())
            $html = preg_replace('|<option value="">[^<]*</option>|', '', $html);

        return $html;
    }

    public function disableOptionCallback($matches)
    {
        $result = $matches[0];

        $allowed = $this->getAllowedOptions();

        if (!in_array($matches[1], $allowed))
            return '';

        return $result;
    }
}
