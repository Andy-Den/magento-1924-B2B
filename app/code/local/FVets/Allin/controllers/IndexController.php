<?php

class FVets_Allin_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function listasAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function indexAction() {
		$this->_forward('accountIndex');
	}

	public function accountIndexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function editAction()
	{
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('fvets_allin/account');

		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_account')->__('This registry no longer exists.'));
				$this->_redirect('*/*/accountindex');
				return;
			}
		}

		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('fvets_allin', $model);

		$this->loadLayout();
		$this->_setActiveMenu('customer/fvets_allin');
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {
			$id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('fvets_allin/account')->load($id);
			if (!$model->getId() && $id) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_allin')->__('This account no longer exists.'));
				$this->_redirect('*/*/accountindex');
				return;
			}

			$model->setData($data);

			try {
				//$_flagCreateList = $this->getRequest()->getParam('create_list');
//				if ($_flagCreateList) {
//					$model->createRemoteList();
//				}

				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fvets_allin')->__('The AllIn account has been saved.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/accountindex');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);

				if ($id) {
					$this->_redirect('*/*/edit', array('id' => $id));
				} else {
					$this->_redirect('*/*/new');
				}
			}
		}
	}

	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				$model = Mage::getModel('fvets_allin/account');
				$model->load($id);
				$model->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fvets_allin')->__('The account has been deleted.'));
				$this->_redirect('*/*/accountindex');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
				return;
			}
		}

		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_allin')->__('This account no longer exists.'));
		$this->_redirect('*/*/');
	}
}
