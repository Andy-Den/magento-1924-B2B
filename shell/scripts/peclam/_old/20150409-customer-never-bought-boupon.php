<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(2);

$websiteId = 2;

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
$csv_addCoupon = '';

/*@var $coupon Mage_SalesRule_Model_Coupon */
$coupon = Mage::getModel('salesrule/coupon');
$coupon->load('desconto10', 'code');
if($coupon->getId()){  //check whether coupon exist or not
	/* @var $resourceCouponUsage Mage_SalesRule_Model_Mysql4_Coupon_Usage */
	$resourceCouponUsage = Mage::getResourceModel('salesrule/coupon_usage');
	$read = $resourceCouponUsage->getReadConnection();

	/**
	 * Traverse the customers like any other collection.
	 */
	$csv_addCoupon .= 'Usuários que já compraram, e que não poderão comprar utilizando o cupom (desconto 10)' . "\n";
	$count = 0;
	foreach ($customerCollection as $customer) {

		$select = $read->select()
			->from($resourceCouponUsage->getMainTable())
			->where('coupon_id = ?', $coupon->getId())
			->where('customer_id = ?', $customer->getId());
		$data = $read->fetchAll($select)[0];

		if (!isset($data))
		{
			$resourceCouponUsage->updateCustomerCouponTimesUsed($customer->getId(), $coupon->getId());
			$csv_addCoupon .= implode('|',$customer->getData()) . "\n";
			$count++;
			echo '+';
		}
	}
	echo "\n" . 'Adicionado coupon_usage de ' . $count . ' clientes' . "\n";
}






$csv_addCoupon .= "\n\n\n\n\n\n";

$csv_addCoupon .= 'Cupom adicionado para usuários que nunca compraram' . "\n";

//Adicionar o atributo de coupon nos clientes que nunca compraram
$customerCollection = Mage::getModel('customer/customer')->getCollection()
	->addFieldToFilter('entity_id', array('nin' => $orderCollection->getColumnValues('customer_id')))
	->addFieldToFilter('website_id', $websiteId);

$count = 0;
foreach ($customerCollection as $customer) {
	$customer = Mage::getModel('customer/customer')->load($customer->getId());
	if(trim($customer->getCoupon()) == '') {
		$customer->setCoupon('desconto10');
		$customer->save();
		$csv_addCoupon .= implode('|',$customer->getData()) . "\n";
		$count++;
		echo '+';
	}
}

echo "\n" . 'cupom "desconto10" adicionado em ' . $count . ' clientes' . "\n";


file_put_contents('extras/'.date('Ymd').'-coupon-desconto10.csv', $csv_addCoupon);