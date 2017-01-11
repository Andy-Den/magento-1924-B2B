<?php
require_once 'Mage/Adminhtml/controllers/Customer/GroupController.php';
class FVets_Customer_Adminhtml_Customer_GroupController extends Mage_Adminhtml_Customer_GroupController
{
	/**
	 * Create or save customer group.
	 */
	public function saveAction()
	{
		$customerGroup = Mage::getModel('customer/group');
		$id = $this->getRequest()->getParam('id');
		if (!is_null($id)) {
			$customerGroup->load((int)$id);
		}

		$taxClass = (int)$this->getRequest()->getParam('tax_class');

		if ($taxClass) {
			$customerGroupCode = (string)$this->getRequest()->getParam('code');

			if (!empty($customerGroupCode)) {
				$customerGroup->setCode($customerGroupCode);
			}

			$customerGroup->setTaxClassId($taxClass);
		} else {
			$this->_forward('new');
		}

		$websiteId = (int)$this->getRequest()->getParam('website_id');

		$customerGroup->setWebsiteId($websiteId);

		try {

			$params = array('object' => $this, 'customer_group' => $customerGroup);
			Mage::dispatchEvent('adminhtml_customer_group_save', $params);

			$customerGroup->save();

			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customer')->__('The customer group has been saved.'));
			$this->getResponse()->setRedirect($this->getUrl('*/customer_group'));
			return;
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::getSingleton('adminhtml/session')->setCustomerGroupData($customerGroup->getData());
			$this->getResponse()->setRedirect($this->getUrl('*/customer_group/edit', array('id' => $id)));
			return;
		}
	}
}