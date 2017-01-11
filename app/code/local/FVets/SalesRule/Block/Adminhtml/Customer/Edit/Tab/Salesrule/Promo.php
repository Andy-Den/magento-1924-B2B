<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule tab on customer edit form
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule_Promo extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */

    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrule_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getCustomer()->getId()) {
            $this->setDefaultFilter(array('in_salesrules'=>1));
        }
    }

    /**
     * prepare the salesrule collection
     *
     * @access protected
     * @return FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('salesrule/rule_collection');
		$collection->addFieldToSelect('rule_id');
		$collection->addFieldToSelect('rule_type');
		$collection->addFieldToSelect('name');

		if ($this->getCustomer()->getId()) {
			$constraint = 'website.website_id='.$this->getCustomer()->getWebsiteId();
		} else {
			$constraint = 'website.website_id=0';
		}
		$collection->getSelect()->join(
			array('website' => $collection->getTable('salesrule/website')),
			'website.rule_id=main_table.rule_id AND '.$constraint,
			array('website_id')
		);

		$collection->getSelect()->joinLeft(
			array('related' => $collection->getTable('fvets_salesrule/customer')),
			'related.salesrule_id=main_table.rule_id',
			array('position')
		);



        $this->setCollection($collection);


		$collection->getSelect()->group('main_table.rule_id');


        parent::_prepareCollection();

        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_salesrules',
            array(
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_salesrules',
                'values'=> $this->_getSelectedSalesrules(),
                'align' => 'center',
                'index' => 'rule_id'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_salesrule')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_salesrule/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/promo_quote/edit',
            )
        );
		$this->addColumn(
			'rule_type',
			array(
				'header'         => Mage::helper('fvets_salesrule')->__('Type'),
				'name'           => 'rule_type',
				'width'          => 200,
				'type'           => 'options',
				'index'          => 'rule_type',
				'editable'       => false,
				'options'		 => Mage::getModel('fvets_salesrule/salesrule_attribute_source_ruletype')->getOptionsArray(false),
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
     * Retrieve selected salesrules
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    protected function _getSelectedSalesrules()
    {
        $salesrules = $this->getCustomerSalesrules();
        if (!is_array($salesrules)) {
            $salesrules = array_keys($this->getSelectedSalesrules());
        }
        return $salesrules;
    }

    /**
     * Retrieve selected salesrules
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedSalesrules()
    {
        $salesrules = array();
        //used helper here in order not to override the customer model
        $selected = Mage::helper('fvets_salesrule/customer')->getSelectedSalesrules(Mage::registry('current_customer'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $salesrule) {
            $salesrules[$salesrule->getId()] = array('position' => $salesrule->getPosition());
        }
        return $salesrules;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_SalesRule_Model_Salesrule
     * @return string
     * @author Douglas Borella Ianitsky
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
     * @author Douglas Borella Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/salesrulesGrid',
            array(
                'id'=>$this->getCustomer()->getId()
            )
        );
    }

    /**
     * get the current customer
     *
     * @access public
     * @return Mage_Catalog_Model_Customer
     * @author Douglas Borella Ianitsky
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
     * @return FVets_SalesRule_Block_Adminhtml_Customer_Edit_Tab_Salesrule
     * @author Douglas Borella Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_salesrules') {
            $salesruleIds = $this->_getSelectedSalesrules();
            if (empty($salesruleIds)) {
                $salesruleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('rule_id', array('in'=>$salesruleIds));
            } else {
                if ($salesruleIds) {
                    $this->getCollection()->addFieldToFilter('rule_id', array('nin'=>$salesruleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
