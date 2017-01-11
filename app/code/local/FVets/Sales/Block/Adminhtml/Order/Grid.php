<?php

class FVets_Sales_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		/* adding customer name section */
		$customerFirstNameAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('firstname');
		$customerLastNameAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('lastname');
		$collection->getSelect()
			->joinLeft(
				array('cusFirstnameTb' => $customerFirstNameAttr->getBackend()->getTable()),
				'main_table.customer_id = cusFirstnameTb.entity_id AND cusFirstnameTb.attribute_id = '.$customerFirstNameAttr->getId(). ' AND cusFirstnameTb.entity_type_id = '.Mage::getSingleton('customer/customer')->getResource()->getTypeId(),
				array('cusFirstnameTb.value')
			);

		$collection->getSelect()
			->joinLeft(
				array('cusLastnameTb' => $customerLastNameAttr->getBackend()->getTable()),
				'main_table.customer_id = cusLastnameTb.entity_id AND cusLastnameTb.attribute_id = '.$customerLastNameAttr->getId(). ' AND cusLastnameTb.entity_type_id = '.Mage::getSingleton('customer/customer')->getResource()->getTypeId(),
				array('customer_name' => "CONCAT(cusFirstnameTb.value, ' ', cusLastnameTb.value)")
			);
		/* end adding customer name section */

		/* Adding customer razão social */
		$customerRazaoSocialAttr = Mage::getSingleton('customer/customer')->getResource()->getAttribute('razao_social');
		$collection->getSelect()
			->joinLeft(
				array('cusRazaoSocialTb' => $customerRazaoSocialAttr->getBackend()->getTable()),
				'main_table.customer_id = cusRazaoSocialTb.entity_id AND cusRazaoSocialTb.attribute_id = '.$customerRazaoSocialAttr->getId(). ' AND cusRazaoSocialTb.entity_type_id = '.Mage::getSingleton('customer/customer')->getResource()->getTypeId(),
				array('razao_social' => 'cusRazaoSocialTb.value')
			);
		/* end adding customer razão social */

		$this->setCollection($collection);
		return call_user_func('Mage_Adminhtml_Block_Widget_Grid::_prepareCollection');
	}

	protected function _prepareColumns()
	{
		$this->addColumnAfter('customer_name', array(
			'header'    => Mage::helper('adminhtml')->__('Customer Name'),
			'index'     => 'customer_name',
			'filter_condition_callback' => array($this, 'customerNameFilter'),
			/*'width'     => '120px',*/
		), 'created_at');

		$this->addColumnAfter('razao_social', array(
			'header'    => Mage::helper('adminhtml')->__('Razão Social'),
			'index'     => 'razao_social',
			'filter_condition_callback' => array($this, 'razaoSocialFilter'),
			/*'width'     => '120px',*/
		), 'customer_name');

		parent::_prepareColumns();

		$this->removeColumn('billing_name');
		$this->removeColumn('shipping_name');
		$this->removeColumn('base_grand_total');
		$this->removeColumn('action');

		return $this;
	}

	public function razaoSocialFilter($collection, $column){
		$filterValue = $column->getFilter()->getValue();
		if(!is_null($filterValue)){
			$filterValue = trim($filterValue);
			$filterValue = preg_replace('/[\s]+/', ' ', $filterValue);

			$where = $collection->getConnection()->quoteInto("cusRazaoSocialTb.value = ?", $filterValue);
			$collection->getSelect()->where($where);
		}
	}

	public function customerNameFilter($collection, $column){
		$filterValue = $column->getFilter()->getValue();
		if(!is_null($filterValue)){
			$filterValue = trim($filterValue);
			$filterValue = preg_replace('/[\s]+/', ' ', $filterValue);

			$whereArr = array();
			$whereArr[] = $collection->getConnection()->quoteInto("cusFirstnameTb.value = ?", $filterValue);
			$whereArr[] = $collection->getConnection()->quoteInto("cusLastnameTb.value = ?", $filterValue);
			$whereArr[] = $collection->getConnection()->quoteInto("CONCAT(cusFirstnameTb.value, ' ', cusLastnameTb.value) = ?", $filterValue);
			$where = implode(' OR ', $whereArr);
			$collection->getSelect()->where($where);
		}
	}
}
