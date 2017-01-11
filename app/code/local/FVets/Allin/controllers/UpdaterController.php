<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 3/2/15
 * Time: 6:18 PM
 */
class FVets_Allin_UpdaterController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function updateRemoteAction()
	{
		//faz o update dos registros da allin
		$idAccountToUpdate = $this->getRequest()->getParam('accounttoupdate');

		if (!$idAccountToUpdate) {
			$this->_redirect('*/*/index');;
		}

		$customersToUpdate = Mage::helper('fvets_allin')->getCustomerList($idAccountToUpdate);

		$allinCustomer = Mage::getModel('fvets_allin/customers');

		try {
			$allinCustomer->massRemoteUpdate($idAccountToUpdate, $customersToUpdate);
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('fvets_allin')->__('A lista foi atualizada com sucesso.'));
			$this->_redirect('*/*/index');
		} catch (exception $ex) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_allin')->__('Ocorreu um problema ao realizar a sincronização') . ' (' . $ex->getMessage() . ')');
			$this->_redirect('*/*/index');
		}
	}
}