<?php

class MageFM_Customer_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        //$this->removeColumn('Telephone');
        $this->removeColumn('billing_postcode');
        $this->removeColumn('billing_country_id');
        $this->removeColumn('billing_region');

		if (!Mage::getSingleton('admin/session')->isAllowed('customer/customer_grid_email'))
		{
			$this->removeColumn('email');
		}

        $tipos = Mage::getModel('magefm_customer/source_tipopessoa')->getKeyValueOptions();

		$this->addColumn('id_erp', array(
			'header' => Mage::helper('magefm_customer')->__('ID ERP'),
			'width' => '100',
			'index' => 'id_erp'
		));

        $this->addColumn('tipopessoa', array(
            'header' => Mage::helper('magefm_customer')->__('Tipo pessoa'),
            'width' => '100',
            'index' => 'tipopessoa',
            'type' => 'options',
            'options' => $tipos
        ));

        $this->addColumn('cpf', array(
            'header' => Mage::helper('magefm_customer')->__('CPF'),
            'width' => '100',
            'index' => 'cpf',
        ));

        $this->addColumn('cnpj', array(
            'header' => Mage::helper('magefm_customer')->__('CNPJ'),
            'width' => '100',
            'index' => 'cnpj',
        ));

		$this->addColumn('crmv', array(
			'header' => Mage::helper('magefm_customer')->__('CRMV'),
			'width' => '100',
			'index' => 'crmv',
		));

		$this->addColumnsOrder('id_erp', 'entity_id');
        $this->addColumnsOrder('tipopessoa', 'id_erp');
        $this->addColumnsOrder('cpf', 'name');
        $this->addColumnsOrder('cnpj', 'cpf');
		$this->addColumnsOrder('crmv', 'cnpj');
        $this->sortColumnsByOrder();

        return $this;
    }

    public function setCollection($collection)
    {
        $collection->addAttributeToSelect('tipopessoa');
        $collection->addAttributeToSelect('cpf');
        $collection->addAttributeToSelect('cnpj');
		$collection->addAttributeToSelect('crmv');
		$collection->addAttributeToSelect('id_erp');

		if (!Mage::getSingleton('admin/session')->isAllowed('customer/customer_grid_tests'))
		{
			$collection->addAttributeToFilter('email', array('nlike' => '%@4vets.com.br'));
		}

        parent::setCollection($collection);
    }

}