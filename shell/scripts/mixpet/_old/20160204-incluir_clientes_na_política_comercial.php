<?php
require_once './configScript.php';

Mage::app()->setCurrentStore(17);

$product = Mage::getModel('catalog/product');

$premier_rules = array(
	'170812' => array('170812', '170813', '170814'),
	'170816' => array('170816', '170817', '170818'),
	'170819' => array('170819', '170820', '170821'),
	'170822' => array('170822', '170823', '170824'),
	'171707' => array('171707', '171708', '171709'),
	'171710' => array('171710', '171711', '171712')
);

$nao_encontrados = array();

if (($handle = fopen("extras/Relação de clientes primeira Faixa - Fev.2016.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
		$customer = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('id_erp')
			->addAttributeToFilter('id_erp', $data[0])
			->getFirstItem()
		;

		if ($customer->getId()) {

			$output = '';
			foreach ($premier_rules as $rule => $search) {
				$collection = Mage::getModel('fvets_salesrule/salesrule_customer')
					->getCollection()
					->addSalesruleFilter($search)
					->addFieldToFilter('entity_id', $customer->getId())
					;
				$a = $collection->getFirstItem();

				if ($a->getId()) {
					$output = '.';
				} else {
					Mage::getModel('fvets_salesrule/salesrule_customer')
						->setCustomerId($customer->getId())
						->setSalesruleId($rule)
						->save();
					$output = '+';
				}
			}
			echo $output;
		} else {
			echo '-';
			$nao_encontrados[]  = implode (',', $data);
		}
	}
}


echo "\n";

echo "Cliente não encontrados\n";

echo implode ("\n", $nao_encontrados);