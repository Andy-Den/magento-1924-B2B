<?php
class FVets_CustomReports_Block_Report_Comissions extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_blockGroup = 'fvets_customreports';
		$this->_controller = 'report_comissions';
		$this->_headerText = $this->__('Relatório de Comissões');
		parent::__construct();
		$this->setTemplate('fvets/customreports/container.phtml');
		$this->_removeButton('add');
	}
}
