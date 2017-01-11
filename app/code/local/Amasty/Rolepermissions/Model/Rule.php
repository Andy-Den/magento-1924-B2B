<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Model_Rule extends Mage_Core_Model_Abstract
{
    protected $_partialWs = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('amrolepermissions/rule');
    }

    public function loadCurrent()
    {
        $user = Mage::getSingleton('admin/session')->getUser();

        if (!$user)
            return $this;

        $roleId = $user->getRole()->getId();

        $this->load($roleId, 'role_id');

        $fields = array('scope_storeviews', 'categories', 'products', 'scope_websites');

        foreach($fields as $field)
        {
            $data = $this->getData($field);
            if ($data == "")
                $data = null;
            else
                $data = explode(',', $data);

            $this->setData($field, $data);
        }

        $websites = $this->getScopeWebsites();

        if (!empty($websites))
        {
            $stores = Mage::getResourceModel('core/store_collection')
                ->addWebsiteFilter($websites)
            ;

            $ids = array();

            foreach ($stores as $id => $store)
            {
                $ids []= $id;
            }

            $this->setScopeStoreviews($ids);
        }

        return $this;
    }

    public function storeAccessible($store)
    {
        if ($this->getScopeStoreviews())
        {
            if (in_array(0, $this->getScopeStoreviews()))
                return true;

            if (!in_array($store, $this->getScopeStoreviews()))
                return false;
        }

        return true;
    }

    public function getPartiallyAccessibleWebsites()
    {
        if (!$this->_partialWs)
        {
            if ($this->getScopeWebsites())
                return $this->getScopeWebsites();

            $stores = Mage::getResourceModel('core/store_collection');

			if ($this->getScopeStoreviews())
				$stores->addIdFilter($this->getScopeStoreviews());

            $this->_partialWs = array_unique($stores->getColumnValues('website_id'));
        }

        return $this->_partialWs;
    }

    public function restrictProductCollection($collection)
    {
        $ruleConditions = array();
        $adapter = $this->getResource()->getReadConnection();
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $collection->addAttributeToSelect('amrolepermissions_owner', 'left');
        $allowOwn = false;

        if ($this->getProducts()) {
            if ($this->getProducts() != array(0)) {
                $ruleConditions []= $adapter->quoteInto(
                    'e.entity_id IN (?)',
                    $this->getProducts()
                );
            }
            else {
                $allowOwn = true;
            }
        }

        if ($this->getCategories())
        {
            $collection->getSelect()
                ->joinLeft(
                    array('cp' => $collection->getTable('catalog/category_product')),
                    'cp.product_id = e.entity_id',
                    array()
                )
            ;

            $ruleConditions []= $adapter->quoteInto(
                'cp.category_id IN (?)',
                $this->getCategories()
            );
        }

        if ($this->getScopeStoreviews())
        {
            $allowedWebsites = $this->getPartiallyAccessibleWebsites();

            $websiteTable = $collection
                ->getResource()
                ->getTable('catalog/product_website');

            $collection->getSelect()
                ->join(
                    array('am_product_website' => $websiteTable),
                    'am_product_website.product_id = e.entity_id',
                    array()
                )
            ;
            $ruleConditions []= $adapter->quoteInto(
                'am_product_website.website_id IN (?)',
                $allowedWebsites
            );
        }

        $ownerCondition = $adapter->quoteInto(
            'at_amrolepermissions_owner.value = ?',
            $userId
        );

        if ($ruleConditions) {
            $ruleConditionsSql = implode(' AND ', $ruleConditions);
            $combinedCondition = "($ownerCondition OR ($ruleConditionsSql))";
            $collection->getSelect()->where($combinedCondition);
        }
        else if ($allowOwn) {
            $collection->getSelect()->where($ownerCondition);
        }

        $collection->getSelect()->distinct();
    }

    public function getAllowedProductIds()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');

        return $collection->getColumnValues('entity_id');
    }

    public function checkProductOwner($product)
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();

        if ($product->getAmrolepermissionsOwner() == $userId)
            return true;

        return false;
    }

    public function checkProductPermissions($product)
    {
        if (!$this->getProducts())
            return true;

        if (!$product->getId())
            return true;

        return in_array($product->getId(), $this->getProducts());
    }

    public function update($data, $categories)
    {
        $this
            ->setScopeWebsites('')
            ->setScopeStoreviews('');

        $data['role_id'] = Mage::registry('current_role')->getId();

        if (isset($data['products_access_mode']))
        {
            if ($data['products_access_mode'] == Amasty_Rolepermissions_Block_Adminhtml_Tab_Products::MODE_MY)
                $data['products'] = 0;
            else if ($data['products_access_mode'] == Amasty_Rolepermissions_Block_Adminhtml_Tab_Products::MODE_ANY)
                $data['products'] = '';
            else
                $data['products'] = explode('&', $data['products']);
        }

        if (isset($data['categories_access_mode']))
        {
            if ($data['categories_access_mode'] == Amasty_Rolepermissions_Block_Adminhtml_Tab_Categories::MODE_ALL)
            {
                $data['categories'] = array();
            }
            else
                $data['categories'] = explode(',', $categories);
        }

        $this->addData($data);

        foreach ($this->getData() as $key => $value)
        {
            if (is_array($value))
            {
                $this->setData($key, implode(',', array_unique(array_filter($value, array($this, 'filter_func')))));
            }
        }

        $this->save();
    }

    public function filter_func($x) {
        return +$x > 0;
    }
}
