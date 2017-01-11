<?php

require_once '../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$customers = array(
	//'2053' => '10%_todas_linhas',
	'1947' => 'promocao_ou_10%_todas_linhas', //OK
	'2593' => '10%_todas_linhas',//OK
	'1866' => '15%_agener',//OK
	'3134' => '17%_emporiopet',
	//'3311' => '10%_agener_mundoanimal',
	'3354' => '10%_todas_linhas',//OK
	'2603' => '10%_todas_linhas',//OK
	'2097' => '13%_todas_linhas',//OK
	//'3321' => '5%_emporiopet',
	//'3317' => '5%_emporiopet',
	'3305' => '5%_emporiopet',//OK
	//'3304' => '5%_emporiopet',
	//'3099' => '5%_emporiopet',
	//'2870' => '5%_emporiopet',
	//'3384' => '10%_emporiopet',
	'3459' => '15%_todas_linhas',
	'3242' => '15%_todas_linhas',
	'1696' => '15%_todas_linhas',
	'1724' => '10%_todas_linhas',
	'1693' => '13%_todas_linhas',
	'2426' => '10%_todas_linhas',
	'2081' => '10%_todas_linhas',
	'2681' => '10%_todas_linhas',
	'2659' => '10%_todas_linhas',
	'2373' => '40_chemitec_bonificar_8',//OK
	//'2053' => '10%_mectimax',//Preciso dos sku para fazer a lista de sku
	'2595' => '3%_todas_linhas',
	'1831' => '3%_todas_linhas',
	'2113' => '10%_todas_linhas',
	'2110' => '10%_todas_linhas',
	'1631' => '10%_todas_linhas',
	'2625' => '30%_todas_linhas',
	//'2278' => '10%_mundoanimal'
);


foreach ($customers as $id => $coupon)
{
	$customer = Mage::getModel('customer/customer')->load($id);
	$customer->setCoupon($coupon);
	$customer->save();
	echo $customer->getId() . '|' . $customer->getIdErp() .   $customer->getName() . "\n";
}

//Remover promoções
$customers = array(2278, 2053, 3311, 3321, 3317, 3304, 3099, 2870, 3384, 2053);

foreach ($customers as $id)
{
	$customer = Mage::getModel('customer/customer')->load($id);
	$customer->setCoupon('');
	$customer->save();
}