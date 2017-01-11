<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition - customer relation edit block
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access protected
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCondition()->getId()) {
            $this->setDefaultFilter(array('in_customers'=>1));
        }
    }

    /**
     * prepare the customer collection
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Customer
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
            ->join(array('website1' => 'core_website'), 'website1.website_id =e.website_id', array('website' => 'name'));

        if ($this->getCondition()->getId()) {
            $constraint = '{{table}}.condition_id='.$this->getCondition()->getId();
        } else {
            $constraint = '{{table}}.condition_id=0';
        }
        $collection->joinField(
            'position',
            'fvets_payment/condition_customer',
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
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Customer
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Customer
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
                'index'     => 'customer_id'
            )
        );
        $this->addColumn(
            'customer_name',
            array(
                'header' => Mage::helper('customer')->__('Name'),
                'align'  => 'left',
                'index'  => 'customer_name',
                'renderer'  => 'fvets_payment/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/customer/edit',
            )
        );
        $this->addColumn(
            'email',
            array(
                'header' => Mage::helper('customer')->__('Name'),
                'align'  => 'left',
                'index'  => 'email'
            )
        );
        /*$this->addColumn(
            'price',
            array(
                'header'        => Mage::helper('customer')->__('Price'),
                'type'          => 'currency',
                'width'         => '1',
                'currency_code' => (string)Mage::getStoreConfig(
                    Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE
                ),
                'index'         => 'price'
            )
        );*/
        $this->addColumn(
            'website',
            array(
                'header' => Mage::helper('customer')->__('Website'),
                'align'  => 'left',
                'index'  => 'website',
                'width'  => 80,
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('customer')->__('Position'),
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
     */
    protected function _getSelectedCustomers()
    {
        $customers = $this->getConditionCustomers();
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
     */
    public function getSelectedCustomers()
    {
        $customers = array();
        $selected = Mage::registry('current_condition')->getSelectedCustomers();
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
     * @param FVets_Payment_Model_Customer
     * @return string
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
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/customersGrid',
            array(
                'id' => $this->getCondition()->getId()
            )
        );
    }

    /**
     * get the current condition
     *
     * @access public
     * @return FVets_Payment_Model_Condition
     */
    public function getCondition()
    {
        return Mage::registry('current_condition');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_Payment_Block_Adminhtml_Condition_Edit_Tab_Customer
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
			$this->getCollection()->getSelect()->where("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) LIKE '%".$column->getFilter()->getValue()."%'");
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
