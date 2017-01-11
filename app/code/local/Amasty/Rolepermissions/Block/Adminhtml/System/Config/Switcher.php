<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_System_Config_Switcher extends Mage_Adminhtml_Block_System_Config_Switcher
{
    public function getStoreSelectOptions()
    {
        $section = $this->getRequest()->getParam('section');

        $curWebsite = $this->getRequest()->getParam('website');
        $curStore   = $this->getRequest()->getParam('store');

        $storeModel = Mage::getSingleton('adminhtml/system_store');
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */

        $url = Mage::getModel('adminhtml/url');


        $rule = Mage::helper('amrolepermissions')->currentRule();

        if (!$storeRestrictions = $rule->getScopeStoreviews())
            $storeRestrictions = array();

        if (!$websiteRestrictions = $rule->getScopeWebsites())
            $websiteRestrictions = array();


        $options = array();

        if (!$storeRestrictions)
        {
            $options['default'] = array(
                'label'    => Mage::helper('adminhtml')->__('Default Config'),
                'url'      => $url->getUrl('*/*/*', array('section'=>$section)),
                'selected' => !$curWebsite && !$curStore,
                'style'    => 'background:#ccc; font-weight:bold;',
            );
        }

        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }

                    if ($storeRestrictions && !in_array($store->getId(), $storeRestrictions))
                        continue;

                    if (!$websiteShow) {
                        $websiteShow = true;

                        if (!$storeRestrictions || in_array($website->getId(), $websiteRestrictions))
                        {
                            $options['website_' . $website->getCode()] = array(
                                'label'    => $website->getName(),
                                'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$website->getCode())),
                                'selected' => !$curStore && $curWebsite == $website->getCode(),
                                'style'    => 'padding-left:16px; background:#DDD; font-weight:bold;',
                            );
                        }
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $options['group_' . $group->getId() . '_open'] = array(
                            'is_group'  => true,
                            'is_close'  => false,
                            'label'     => $group->getName(),
                            'style'     => 'padding-left:32px;'
                        );
                    }
                    $options['store_' . $store->getCode()] = array(
                        'label'    => $store->getName(),
                        'url'      => $url->getUrl('*/*/*', array('section'=>$section, 'website'=>$website->getCode(), 'store'=>$store->getCode())),
                        'selected' => $curStore == $store->getCode(),
                        'style'    => '',
                    );
                }
                if ($groupShow) {
                    $options['group_' . $group->getId() . '_close'] = array(
                        'is_group'  => true,
                        'is_close'  => true,
                    );
                }
            }
        }

        return $options;
    }
}
