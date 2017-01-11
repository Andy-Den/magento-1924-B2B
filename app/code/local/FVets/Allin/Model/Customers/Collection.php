<?php

class FVets_Allin_Model_Customers_Collection extends Varien_Data_Collection
{

	public function _construct()
	{
		$this->_init('fvets_allin/customers');
	}

	public function getCustomers()
	{
		$helper = Mage::helper('fvets_allin');

		$ticket = $helper->getTicket();

		$client = new nusoap_client("http://painel01.allinmail.com.br/wsAllin/listar_listas.php?wsdl", true);
		$result = $client->call('getListas', array($ticket));

		//print_r($result);

		foreach ($result['itensConteudo'] as $lista) {
			$object = new Varien_Object();
			$object->setName($lista['itensConteudo_nm_lista']);
			$this->addItem($object);
		}

		return $this;
	}
}