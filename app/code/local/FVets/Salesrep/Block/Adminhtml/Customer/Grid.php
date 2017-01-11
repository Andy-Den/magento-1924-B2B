<?php

class FVets_Salesrep_Block_Adminhtml_Customer_Grid extends MageFM_Customer_Block_Adminhtml_Customer_Grid
{

	protected function _prepareMassaction()
	{
		// add default mass actions
		parent::_prepareMassaction();

//		$groups = $this->helper('fvets_salesrep')->getSalesRep()->toOptionArray();
//
//		array_unshift($groups, array('label'=> '', 'value'=> ''));
//		$this->getMassactionBlock()->addItem('assign_salesrep', array(
//			'label'        => Mage::helper('customer')->__('Assign a Customer Sales Rep'),
//			'url'          => $this->getUrl('*/fvets_salesrep/massAssignSalesrep'),
//			'additional'   => array(
//				'visibility'    => array(
//					'name'     => 'salesrep',
//					'type'     => 'select',
//					'class'    => 'required-entry',
//					'label'    => Mage::helper('customer')->__('SalesRep'),
//					'values'   => $groups
//				)
//			)
//		));

		return $this;
	}

}