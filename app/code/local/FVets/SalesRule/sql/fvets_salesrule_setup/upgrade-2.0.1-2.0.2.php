<?php


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();

//Adiciona todos os customers que têm um cupom na campanha de cupom
$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToSelect('website_id')
	->addAttributeToSelect('coupon')
	->addAttributeToFilter('coupon', array('notnull' => true))
;
$customers
	->getSelect()
	->order('coupon');

$couponRules = array();

foreach ($customers as $customer)
{
	$rule = Mage::getModel('salesrule/rule')
		->getCollection()
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

	if ($rule->getId())
	{
		Mage::getResourceModel('fvets_salesrule/salesrule_customer')->saveCustomerRelation($customer, array($rule->getId() => array()));
		$couponRules[$rule->getId()] = $rule;
	}
}

//Deixa as promoções de cupons vinculadas a clientes como promoções normais
//e remove a flag de poder usar para todos os clientes.
foreach ($couponRules  as $rule)
{
	$model = Mage::getModel('fvets_salesrule/salesrule_customer_group')->load($rule->getId());
	$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer_group')
		->delete($model);
	$rule->unsetData('customer_group_ids');

	$rule->setApplyToAll('0');
	$rule->setCouponType('1');
	$rule->save();
}

//Remove fvets_coupon attribute
$setup->removeAttribute('customer', 'coupon');

$setup->endSetup();