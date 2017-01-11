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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product search result block
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class FVets_SalesRule_Block_Mix extends Mage_Core_Block_Template
{
    /**
     * Catalog Product collection
     *
     * @var Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    protected $_productCollection;

    /**
     * Prepare layout
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    protected function _prepareLayout()
    {
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {

            $category = Mage::getModel('catalog/category')->load(Mage::getSingleton('catalog/session')->getLastVisitedCategoryId());

            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('category', array(
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link'  => $category->getUrl()
            ))->addCrumb('mix', array(
                'label' => $this->getHeaderText(),
                'title' => $this->getHeaderText()
            ));
        }

        // modify page title
        $this->getLayout()->getBlock('head')->setTitle($this->getHeaderText());

        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_rule')->getDescription() != '')
        {
            return $this->__("Mix: %s", Mage::registry('current_rule')->getDescription());
        }
        return $this->__("Mix: %s", Mage::registry('current_rule')->getName());
    }

    public function getBackLink()
    {
        return Mage::getModel('catalog/category')->load(Mage::getSingleton('catalog/session')->getLastVisitedCategoryId())->getUrl();
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return Mage_Catalog_Block_Product_List
     */
    public function getListBlock()
    {
        return $this->getChild('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListOrders()
    {
        $category = Mage::getSingleton('catalog/layer')
            ->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'relevance' => $this->__('Relevance')
        ), $availableOrders);

        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListModes()
    {
        $this->getListBlock()
            ->setModes(array(
                'grid' => $this->__('Grid'),
                'list' => $this->__('List'))
            );
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
//        $this->getListBlock()
//           ->setCollection($this->_getProductCollection());
       return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        return $this->__('Mix not found!');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return Mage::helper('catalogsearch')->getNoteMessages();
    }
}
