<?php

include_once 'configScript.php';

// Procurar todos os customers que já compraram

/**
 * Now get all unique customers from the orders of these items.
 */
$orderCollection = Mage::getResourceModel('sales/order_collection')
	->addFieldToFilter('customer_id', array('neq' => 'NULL'));
$orderCollection->getSelect()->group('customer_id');

/**
 * Now get a customer collection for those customers.
 */
$customerCollection = Mage::getModel('customer/customer')->getCollection()
	->addFieldToFilter('entity_id', array('in' => $orderCollection->getColumnValues('customer_id')))
	->addFieldToFilter('website_id', $websiteId);

//Setar coupon_usage nos customers que ja compraram
$csv_JaCompraram = array();

/*@var $coupon Mage_SalesRule_Model_Coupon */
$rule = Mage::getModel('salesrule/rule')->load(63215);

if($rule->getId()){  //check whether coupon exist or not

	/**
	 * Traverse the customers like any other collection.
	 */
	$count = 0;
	foreach ($customerCollection as $customer)
	{
		$resourceRuleUsage = Mage::getResourceModel('salesrule/rule_customer');
		$read = $resourceRuleUsage->getReadConnection();

		$select = $read->select()
			->from($resourceRuleUsage->getMainTable(), array('times_used'))
			->where('rule_id = :rule_id')
			->where('customer_id = :customer_id');

		$timesUsed = $read->fetchOne($select, array(':rule_id' => $rule->getId(), ':customer_id' => $customer->getId()));

		if ($timesUsed <= 0) {
			$object = Mage::getModel('salesrule/rule_customer')
				->setRuleId($rule->getId())
				->setCustomerId($customer->getId())
				->setTimesUsed('1')
				->save();
			//$resourceRuleUsage->save($object);
			$csv_JaCompraram[] = implode(',', $customer->getData());
			$count++;
		}
		echo '+';

	}
	echo "\n" . 'Adicionado coupon_usage de ' . $count . ' clientes' . "\n";
}

$channel = 'scripts';
$filename = date('Ymd').'-cantUseCoupon.csv';
$data = implode("\n", $csv_JaCompraram);

if (file_put_contents('extras'. DS .$filename, $data))
{
	Mage::helper('datavalidate')->sendSlackFile(end(explode('/', __FILE__)), count($csv_JaCompraram) . " - Clientes não podem utilizar o cupom de desconto", Mage::getBaseDir() . DS . 'shell' . DS . 'scripts' . DS . 'doctorsvet' . DS . 'extras' . DS . $filename, 'csv', $channel);
}
else
{
	echo "Arquivo " . $filename . " não salvo" . "\n";
}