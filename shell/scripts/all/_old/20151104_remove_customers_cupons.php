<?php

require_once 'configScript.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('firstname')
	->addAttributeToSelect('lastname')
	->addAttributeToSelect('id_erp')
	->addAttributeToSelect('coupon')
	->addAttributeToFilter('coupon', array('notnull' => true))
;
$customers
	->getSelect()
	->order('coupon');

$changed = array();

foreach ($customers as $customer)
{
	echo $customer->getCoupon() . " | ";

	$rule = Mage::getModel('salesrule/rule')
		->getCollection()
		->addFieldToSelect('name')
		->addFieldToSelect('description')
		->addWebsiteFilter($customer->getWebsiteId())
	;

	$rule->getSelect()
		->joinInner(
			array('coupon' => 'salesrule_coupon'),
			'coupon.rule_id = main_table.rule_id'
		)
		->where('coupon.code = "'.$customer->getCoupon().'"')
	;

	$rule = $rule->getFirstItem();

	echo $rule->getId();

	echo "\n";

	if ($rule->getId())
	{
		Mage::getResourceModel('fvets_salesrule/salesrule_customer')->saveCustomerRelation($customer, array($rule->getId() => array()));
		$changed[] = implode(',', array($customer->getId(), $customer->getIdErp(), $customer->getFirstname() . ' ' . $customer->getLastname(), '', $rule->getName(), $rule->getDescription()));
	}
}

echo "\n";

//$channel = 'scripts';
$channel = 'test';

Mage::helper('datavalidate')->createChannel($channel);

echo count($changed) . " - Clientes alterados" . "\n";

$filename = date('Ymd').'-changeCustomersFromCouponToPromoRelation.csv';
$data = implode("\n", $changed);

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), count($changed) . " - Clientes alterados", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'all' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo" . $filename . " não salvo" . "\n";
}

echo "\n";
echo 'Bye';