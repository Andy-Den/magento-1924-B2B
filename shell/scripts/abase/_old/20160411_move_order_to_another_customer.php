<?php
require_once './configScript.php';

/**
* Created by PhpStorm.
* User: julio
* Date: 4/11/16
* Time: 9:23 AM
*/
?>

<?php
$fromCustomerId = 40770;
$toCustomerId = 12547;
//$orderId = 10;
?>

<?php $customer = Mage::getModel('customer/customer'); ?>
<?php $fromCustomer = Mage::getModel('customer/customer')->load($fromCustomerId); ?>
<?php $toCustomer = Mage::getModel('customer/customer')->load($toCustomerId); ?>

<?php $orderFromCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $fromCustomer->getId()); ?>
<?php //$orderToCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', $fromCustomer->getId()); ?>

<?php foreach ($orderFromCollection as $order): ?>
	<?php //$orderid = $order->getIncrementId(); ?>
	<?php //if ($orderid == "200017139"): ?>
	<?php $order->setCustomerId($toCustomer->getId()); ?>
	<?php $order->setCustomerFirstname($toCustomer->getFirstname()); ?>
	<?php $order->setCustomerLastname($toCustomer->getLastname()); ?>
	<?php $order->setCustomerEmail($toCustomer->getEmail()); ?>
	<?php $order->save(); ?>
	<?php //endif; ?>
<?php endforeach; ?>