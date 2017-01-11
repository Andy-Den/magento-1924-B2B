<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - product relation edit block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access protected
     * @author Douglas Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCatalogrestrictiongroup()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    /**
     * prepare the product collection
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Product
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('price');
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute('product_name', 'catalog_product/name', 'entity_id', null, 'left', $adminStore);
        if ($this->getCatalogrestrictiongroup()->getId()) {
            $constraint = '{{table}}.catalogrestrictiongroup_id='.$this->getCatalogrestrictiongroup()->getId();
        } else {
            $constraint = '{{table}}.catalogrestrictiongroup_id=0';
        }
        $collection->joinField(
            'position',
            'fvets_catalogrestrictiongroup/entity_product',
            'position',
            'product_id=entity_id',
            $constraint,
            'left'
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Product
     * @author Douglas Ianitsky
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Product
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_products',
                'values'=> $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'product_name',
            array(
                'header'    => Mage::helper('catalog')->__('Name'),
                'align'     => 'left',
                'index'     => 'product_name',
                'renderer'  => 'fvets_catalogrestrictiongroup/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/catalog_product/edit',
            )
        );
        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('catalog')->__('SKU'),
                'align'  => 'left',
                'index'  => 'sku',
            )
        );
        $this->addColumn(
            'price',
            array(
                'header'        => Mage::helper('catalog')->__('Price'),
                'type'          => 'currency',
                'width'         => '1',
                'currency_code' => (string)Mage::getStoreConfig(
                    Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE
                ),
                'index'         => 'price'
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('catalog')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
    }

    /**
     * Retrieve selected products
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getCatalogrestrictiongroupProducts();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedProducts());
        }
        return $products;
    }

    /**
     * Retrieve selected products
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedProducts()
    {
        $products = array();
        $selected = Mage::registry('current_catalogrestrictiongroup')->getSelectedProducts();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Product
     * @return string
     * @author Douglas Ianitsky
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Douglas Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/productsGrid',
            array(
                'id' => $this->getCatalogrestrictiongroup()->getId()
            )
        );
    }

    /**
     * get the current restriction group
     *
     * @access public
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     * @author Douglas Ianitsky
     */
    public function getCatalogrestrictiongroup()
    {
        return Mage::registry('current_catalogrestrictiongroup');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Product
     * @author Douglas Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
