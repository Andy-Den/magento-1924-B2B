<?php
class FVets_SalesRule_SalesrepController extends Mage_Core_Controller_Front_Action
{
	public function premierAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function premierCustomerAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function premierCustomerSaveAction()
	{
		$post = $this->getRequest()->getPost();

		$customer = Mage::getModel('customer/customer')->load($post['customer_id']);

		if (isset($post['premier']))
		{
			$data = array();
			foreach ($post['premier'] as $rule_id)
			{
				$data[$rule_id] = array();
			}

			Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')->saveCustomerRelation($customer, $data);
		}

		$this->_redirect('salesrule/salesrep/premierCustomer/customer_id/'.$post['customer_id']);
	}
}