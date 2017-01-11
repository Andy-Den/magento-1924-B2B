<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group - customer relation edit block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('customer_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCatalogrestrictiongroup()->getId()) {
            $this->setDefaultFilter(array('in_customers'=>1));
        }
    }

    /**
     * prepare the customer collection
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Customer
     * @author Douglas Ianitsky
     */
    protected function _prepareCollection()
    	{
    		$collection = Mage::getResourceModel('customer/customer_collection');
    		$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
    		$collection->joinAttribute('customer_id', 'customer/entity_id', 'entity_id', null, 'left', $adminStore);
    		//Customer name
    		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
    		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');

    		$collection->getSelect()
    			->join(array('ce1' => 'customer_entity_varchar'), 'ce1.entity_id=e.entity_id', array('firstname' => 'value'))
    			->where('ce1.attribute_id='.$fn->getAttributeId())
    			->join(array('ce2' => 'customer_entity_varchar'), 'ce2.entity_id=e.entity_id', array('lastname' => 'value'))
    			->where('ce2.attribute_id='.$ln->getAttributeId())
    			->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));

    		$collection->getSelect()
    			->join(array('website1' => 'core_website'), 'website1.website_id =e.website_id', array('website' => 'name', 'website_id' => 'website_id'));

    		if ($this->getCatalogrestrictiongroup()->getId()) {
    			$constraint = '{{table}}.catalogrestrictiongroup_id='.$this->getCatalogrestrictiongroup()->getId();
    		} else {
    			$constraint = '{{table}}.catalogrestrictiongroup_id=0';
    		}
    		$collection->joinField(
    			'position',
    			'fvets_catalogrestrictiongroup/entity_customer',
    			'position',
    			'customer_id=entity_id',
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
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Customer
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
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Customer
     * @author Douglas Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_customers',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_customers',
                'values'=> $this->_getSelectedCustomers(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
					'customer_id',
					array(
						'header'    => Mage::helper('customer')->__('ID'),
						'align'     => 'left',
						'index'     => 'customer_id',
						'width'  => 80,
					)
				);
        $this->addColumn(
            'customer_name',
            array(
                'header'    => Mage::helper('catalog')->__('Name'),
                'align'     => 'left',
                'index'     => 'customer_name',
                'renderer'  => 'fvets_catalogrestrictiongroup/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/customer/edit',
            )
        );
        $this->addColumn(
					'email',
					array(
						'header' => Mage::helper('customer')->__('Email'),
						'align'  => 'left',
						'index'  => 'email'
					)
				);
				$this->addColumn(
					'website_id',
					array(
						'header' => Mage::helper('customer')->__('Website'),
						'align'  => 'left',
						'index'  => 'website_id',
						'width'  => 80,
						'type'      => 'options',
						'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
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
     * Retrieve selected customers
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    protected function _getSelectedCustomers()
    {
        $customers = $this->getCatalogrestrictiongroupCustomers();
        if (!is_array($customers)) {
            $customers = array_keys($this->getSelectedCustomers());
        }
        return $customers;
    }

    /**
     * Retrieve selected customers
     *
     * @access protected
     * @return array
     * @author Douglas Ianitsky
     */
    public function getSelectedCustomers()
    {
        $customers = array();
        $selected = Mage::registry('current_catalogrestrictiongroup')->getSelectedCustomers();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $customer) {
            $customers[$customer->getId()] = array('position' => $customer->getPosition());
        }
        return $customers;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_CatalogRestrictionGroup_Model_Customer
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
            '*/*/customersGrid',
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
     * @return FVets_CatalogRestrictionGroup_Block_Adminhtml_Catalogrestrictiongroup_Edit_Tab_Customer
     * @author Douglas Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in customer flag
        if ($column->getId() == 'in_customers') {
            $customerIds = $this->_getSelectedCustomers();
            if (empty($customerIds)) {
                $customerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $customerIds));
            } else {
                if ($customerIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $customerIds));
                }
            }
        } else if ($column->getId() == 'customer_name') {
            $this->getCollection()->getSelect()->where("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) LIKE '%" . $column->getFilter()->getValue() . "%'");
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
