<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price model
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Model_Tableprice extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'fvets_tableprice';
    const CACHE_TAG = 'fvets_tableprice';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'fvets_tableprice';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'tableprice';
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('fvets_tableprice/tableprice');
    }

    /**
     * before save table price
     *
     * @access protected
     * @return FVets_TablePrice_Model_Tableprice
     * @author Douglas Ianitsky
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save table price relation
     *
     * @access public
     * @return FVets_TablePrice_Model_Tableprice
     * @author Douglas Ianitsky
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveTablepriceRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return FVets_TablePrice_Model_Tableprice_Category
     * @author Douglas Ianitsky
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('fvets_tableprice/category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return FVets_TablePrice_Resource_Tableprice_Category_Collection
     * @author Douglas Ianitsky
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Douglas Ianitsky
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
