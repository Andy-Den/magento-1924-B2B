<?php

class FVets_CustomReports_Report_ComissionsController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('report/comissions/comissions');
		$this->_addBreadcrumb($this->__('Comissions Report'), $this->__('Comissions Report'));
		$this->_addContent($this->getLayout()->createBlock('fvets_customreports/report_comissions'));
		$this->renderLayout();
	}

	public function exportCsvAction()
	{
		$websiteId = $this->getRequest()->getParam('website_switcher');
		$dateFrom = $this->getRequest()->getParam('date_from');
		$dateTo = $this->getRequest()->getParam('date_to');
		$tipoCliente = $this->getRequest()->getParam('tipoCliente');

		$this->getRequest()->getParams();
		$fileName = 'fvets_rel_comissoes_website_' . $websiteId . '_de_' . $dateFrom . '_a_' . $dateTo . '_tipo-cliente_' . $tipoCliente . '_' . date('Y_m_d_h_i_s') . '.csv';

		$content = $this->getLayout()
			->createBlock('fvets_customreports/report_comissions_grid');
		$this->_prepareDownloadResponse($fileName, $content->getCsvFile());
	}

	public function exportXlsAction()
	{
		$websiteId = $this->getRequest()->getParam('website_switcher');
		$dateFrom = $this->getRequest()->getParam('date_from');
		$dateTo = $this->getRequest()->getParam('date_to');
		$tipoCliente = $this->getRequest()->getParam('tipoCliente');

		$this->getRequest()->getParams();
		$fileName = 'fvets_rel_comissoes_website_' . $websiteId . '_de_' . $dateFrom . '_a_' . $dateTo . '_tipo-cliente_' . $tipoCliente . '_' . date('Y_m_d_h_i_s') . '.xls';
		$content = $this->getLayout()->createBlock('fvets_customreports/report_comissions_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('admin/report/salesroot/comissionsreports');
	}
}
