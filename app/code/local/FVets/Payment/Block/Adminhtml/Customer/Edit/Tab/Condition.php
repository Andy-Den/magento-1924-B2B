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
 * Condition tab on customer edit form
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Block_Adminhtml_Customer_Edit_Tab_Condition extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('condition_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCustomer()->getId()) {
            $this->setDefaultFilter(array('in_conditions'=>1));
        }
    }

    /**
     * prepare the condition collection
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Customer_Edit_Tab_Condition
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('fvets_payment/condition_collection');
        $collection->addWebsiteFilter($this->getCustomer()->getWebsiteId());
        if ($this->getCustomer()->getId()) {
            $constraint = 'related.customer_id='.$this->getCustomer()->getId();
        } else {
            $constraint = 'related.customer_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('fvets_payment/condition_customer')),
            'related.condition_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Customer_Edit_Tab_Condition
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Customer_Edit_Tab_Condition
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_conditions',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_conditions',
                'values'=> $this->_getSelectedConditions(),
                'align' => 'center',
                'index' => 'entity_id'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_payment')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_payment/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/payment_condition/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('fvets_payment')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected conditions
     *
     * @access protected
     * @return array
     */
    protected function _getSelectedConditions()
    {
        $conditions = $this->getCustomerConditions();
        if (!is_array($conditions)) {
            $conditions = array_keys($this->getSelectedConditions());
        }
        return $conditions;
    }

    /**
     * Retrieve selected conditions
     *
     * @access protected
     * @return array
     */
    public function getSelectedConditions()
    {
        $conditions = array();
        //used helper here in order not to override the customer model
        $selected = Mage::helper('fvets_payment/customer')->getSelectedConditions($this->getCustomer());
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $condition) {
            $conditions[$condition->getId()] = array('position' => $condition->getPosition());
        }
        return $conditions;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_Payment_Model_Condition
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
            '*/*/conditionsGrid',
            array(
                'id'=>$this->getCustomer()->getId()
            )
        );
    }

    /**
     * get the current customer
     *
     * @access public
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_Payment_Block_Adminhtml_Customer_Edit_Tab_Condition
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_conditions') {
            $conditionIds = $this->_getSelectedConditions();
            if (empty($conditionIds)) {
                $conditionIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$conditionIds));
            } else {
                if ($conditionIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$conditionIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
